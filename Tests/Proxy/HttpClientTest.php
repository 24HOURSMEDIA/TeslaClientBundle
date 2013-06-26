<?php
namespace Tesla\Bundle\ClientBundle\Tests\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tesla\Bundle\ClientBundle\Client\HttpClientFactory;
use Tesla\Bundle\ClientBundle\Client\HttpClientInterface;


class HttpClientTestCase extends WebTestCase
{


	/**
	 * Creates a client with a preconfigured test url
	 * @return HttpClientInterface
	 */
	protected function createTestClient() {
		$client = static::createClient();
		/* @var $factory HttpClientFactory */
		$factory = $client->getContainer()->get('tesla_client.http_client_factory');
		$client = $factory->get('http://www.example.com');
		return $client;
	}

	public function testAddHeader() {
		$client = $this->createTestClient();
		$client->addRequestHeader('x-foo', 'test');
		$request = $client->createRequest('/');
		$this->assertEquals('test', $request->headers->get('X-foo'), 'could not set the x-foo request header');

		$client->addRequestHeader('x-foo', 'test2');
		$request = $client->createRequest('/');
		$all = $request->headers->all();
		$this->assertEquals(array('test', 'test2'), $all['x-foo'], 'could not set multiple request headers');

	}

}
