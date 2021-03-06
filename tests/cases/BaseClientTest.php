<?php

namespace Lenddo\tests\cases;

abstract class BaseClientTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @return String
	 */
	abstract protected function _getExpectedBaseUri();

	/**
	 * Exists for purposes of DRY code. This doesn't change from test to test so no reason to keep re-writing it.
	 *
	 * @param $mock_result \Lenddo\tests\mocks\GuzzleClientMock
	 * @param $expect_method
	 * @param $expect_path
	 * @return mixed
	 */
	protected function _testResultGetRequestOptions($mock_result, $expect_method, $expect_path)
	{
		list($method, $path, $query, $headers, $body, $guzzle_options) = $mock_result->getRequestArgs();
		$construct_options = $mock_result->getConstructArgs();
		$construct_options = $construct_options[0];

		$this->assertEquals($expect_method, $method);
		$this->assertEquals($expect_path, $path);

		$this->assertArrayHasKey('Date', $headers);

		// Analyze Construction
		$this->assertEquals($this->_getExpectedBaseUri(), $construct_options);

		return compact('method', 'path', 'query', 'headers', 'body', 'guzzle_options');
	}
}