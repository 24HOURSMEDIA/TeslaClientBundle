<?php

namespace Tesla\Bundle\ClientBundle\Tests\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Tesla\Bundle\ClientBundle\Client\HttpClientFactory;
use Tesla\Bundle\ClientBundle\Client\HttpClientInterface;
use Tesla\Bundle\ClientBundle\Client\TeslaRequest;
use Tesla\Bundle\ClientBundle\Client\TeslaResponse;
use Symfony\Component\HttpFoundation\Request;

class HttpClientService extends WebTestCase {


	/**
	 * Tests the default http client service tesla_client.http_client
	 */
	public function testHttpService() {
		$client = static::createClient();
		/* @var $http HttpClientInterface */
		$http = $client->getContainer()->get('tesla_client.http_client');

		$responseContent = $http->execute($http->createRequest('http://www.example.com'))->getContent();
		$this->assertContains('example', $responseContent);


	}
}
