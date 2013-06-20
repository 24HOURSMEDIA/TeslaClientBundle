<?php

namespace Tesla\Bundle\ClientBundle\Tests\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


use Tesla\Bundle\ClientBundle\Proxy\HttpProxy;


class HttpProxyServiceTest extends WebTestCase {


	/**
	 * Tests the default http client service tesla_client.http_client
	 */
	public function testExampleProxyService() {
		$client = static::createClient();
		/* @var $proxy HttpProxy */
		$proxy = $client->getContainer()->get('tesla_client.http_proxy_example');

		// foo host gets proxied to example.com
		$responseContent = $proxy->execute($proxy->createRequest('http://foo'))->getContent();
		$this->assertContains('example', $responseContent);

		// foo host gets proxied to example.com
		$responseContent = $proxy->execute($proxy->createRequest('http://bar/sub'))->getContent();
		$this->assertContains('example', $responseContent);

	}
}
