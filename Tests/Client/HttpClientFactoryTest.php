<?php

namespace Tesla\Bundle\ClientBundle\Tests\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Tesla\Bundle\ClientBundle\Client\HttpClientFactory;
use Tesla\Bundle\ClientBundle\Client\HttpClientInterface;

class HttpClientFactoryTest extends WebTestCase {

	public function testGet()
	{
		$client = static::createClient();
		$factory = $client->getContainer()->get('tesla_client.http_client_factory');
		$this->assertTrue($factory instanceof HttpClientFactory);

		// get a configured http client
		$client = $factory->get();
		$this->assertTrue($client instanceof HttpClientInterface);
	}
}