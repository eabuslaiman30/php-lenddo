<?php

namespace Lenddo\clients\guzzle_handlers;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use Lenddo\clients\exceptions\ExceptionRouter;
use Lenddo\clients\guzzle_handlers\response\V4Response as Response;

class GuzzleV4Handler implements HandlerInterface {
	protected $_base_uri;

	public function __construct($base_uri)
	{
		$this->_base_uri = $base_uri;
	}

	protected function __setCaRootBundleOnGuzzleOptions($guzzle_options) {
		if (isset($guzzle_options['verify'])) {
			// Something else has already defined the verify value.
			return $guzzle_options;
		}

		$guzzle_options['verify'] = __DIR__ . '/ca-bundle.crt';
		return $guzzle_options;
	}

	public function request($method, $path, $query, $headers, $body, $guzzle_options) {
		$guzzle_client = new GuzzleClient(array(
			'base_url' => $this->_base_uri
		));

		// Attach the bundle for machines without the appropriate understanding of SSL Certificates.
		$guzzle_options = $this->__setCaRootBundleOnGuzzleOptions($guzzle_options);

		$request = $guzzle_client->createRequest($method, $path, array_merge($guzzle_options, array(
			'query' => $query,
			'headers' => $headers,
			'body' => $body
		)));

		try {
			// Send the request
			return new Response($request, $guzzle_client->send($request));
		} catch(RequestException $e) {
			// Catch request exceptions and wrap them in our own.
			$response = new Response($request, $e->getResponse());
			throw ExceptionRouter::routeException($e, $response);
		}
	}
}