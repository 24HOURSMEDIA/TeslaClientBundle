<?php
namespace Tesla\Bundle\ClientBundle\Client;


interface HttpClientInterface {

	/**
	 * Creates a request
	 * @return Request
	 */
	public function createRequest($uri = null, array $parms = array(), $method='GET');

	/**
	 * Executes a request
	 * @param Request $request
	 * @return Response
	 */
	public function execute(Request $request);

	/**
	 * Forces cached execution of request
	 * @param Request $request
	 * @param int $ttl
	 * @return HttpClientInterface
	 */
	public function cacheExecute(Request $request, $ttl);

	/**
	 * Enables basic authentication
	 * @param string $user
	 * @param string $password
	 * @return HttpClientInterface
	 */
	public function basicAuthentication($user = null, $password = null);

	/**
	 * Set headers (nested array, i.e. [['user-agent', 'headervalue']]
	 * @param array $headers
	 * @return HttpClientInterface
	 */
	function setRequestHeaders(array $headers);

	/**
	 * Adds a header to the request
	 * @param string $key
	 * @param string $val
	 */
	public function addRequestHeader($key, $val);

}