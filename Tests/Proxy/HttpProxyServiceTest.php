<?php

namespace Tesla\Bundle\ClientBundle\Tests\Proxy;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


use Tesla\Bundle\ClientBundle\Proxy\HttpProxy;
use Tesla\Bundle\ClientBundle\Proxy\HttpProxyFactory;

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

	/**
	 * Tests the behaviour when base url is empty (proxies all)
	 */
	public function testEmptyBaseUrlBehaviour() {
		$client = static::createClient();
		/* @var $factory HttpProxyFactory */
		$factory = $client->getContainer()->get('tesla_client.http_proxy_factory');
		$proxy = $factory->get('http://proxy-foo/');
		$request = $proxy->createRequest('http://www.example.com/');
		$this->assertEquals('http://www.example.com/', $request->getUri());
	}

	public function testPassEncodedUrlBehavior() {
		$client = static::createClient();
		/* @var $factory HttpProxyFactory */
		$factory = $client->getContainer()->get('tesla_client.http_proxy_factory');
		$proxy = $factory->get('http://proxy-foo/proxy?url=');
		$proxy->setPassEncodedUrlBehavior(true);
		$request = $proxy->createRequest('http://www.example.com/');
		$this->assertEquals('http://proxy-foo/proxy?url=' . urlencode('http://www.example.com/'), $request->getUri());
	}



}
