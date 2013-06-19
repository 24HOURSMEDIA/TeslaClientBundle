<?php
namespace Tesla\Bundle\ClientBundle\Client;
use Symfony\Component\HttpFoundation\Request;

interface HttpClientInterface {

	/**
	 * Creates a request
	 * @return Request
	 */
	public function createRequest($uri = null, array $parms = array(), $method='GET');

	/**
	 * Executes a request
	 * @param Response $request
	 */
	public function execute(Request $request);

	/**
	 * Forces cached execution of request
	 * @param Request $request
	 * @param int $ttl
	 */
	public function cacheExecute(Request $request, $ttl);

}