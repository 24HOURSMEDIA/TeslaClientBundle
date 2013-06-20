<?php

namespace Tesla\Bundle\ClientBundle\Tests\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Tesla\Bundle\ClientBundle\Client\HttpClientFactory;
use Tesla\Bundle\ClientBundle\Client\HttpClientInterface;
use Tesla\Bundle\ClientBundle\Client\Request;



class HttpClientTest extends WebTestCase {

	/**
	 *
	 * @return \Tesla\Bundle\ClientBundle\Client\HttpClientInterface
	 * Interface
	 */
	public function getHttpClient($baseUrl = null) {
		$client = static::createClient();
		$factory = $client->getContainer()->get('tesla_client.http_client_factory');
		$this->assertTrue($factory instanceof HttpClientFactory);

		// get a configured http client
		$client = $factory->get($baseUrl);
		$this->assertTrue($client instanceof HttpClientInterface);
		return $client;
	}

	public function testCreateRequest()
	{
		$http = $this->getHttpClient();
		$request = $http->createRequest();
		$this->assertTrue($request instanceof Request);
	}

	public function testGetRequest() {
		$http = $this->getHttpClient();
		$r = $http->createRequest('http://www.example.com');
		$this->assertEquals('http://www.example.com/', $r->getUri());

		$r = $http->createRequest('http://www.example.com/test?q=1');
		$this->assertEquals($r->get('q'), 1);

		$r = $http->createRequest('http://www.example.com/test?a=1&q=2', array('q' => 1));
		$this->assertEquals(1, $r->get('a'));
		$this->assertEquals(1, $r->get('a'));
		$this->assertEquals('http://www.example.com/test?a=1&q=1', $r->getUri());

		$r = $http->createRequest('http://www.example.com?a=1&q=2', array('q' => 1));
		$this->assertEquals('http://www.example.com/?a=1&q=1', $r->getUri());
		$r = $http->createRequest('http://www.example.com/test/?a=1&q=2', array('q' => 1));
		$this->assertEquals('http://www.example.com/test/?a=1&q=1', $r->getUri());
	}

	public function testExecuteGetRequest() {
		$http = $this->getHttpClient();
		$r = $http->createRequest('http://www.example.com/', array(), 'GET');
		$response  = $http->execute($r);
		$this->assertContains('example', $response->getContent());
		$contentType = $response->headers->get('content-type');
		$this->assertContains('html', $contentType);
		$this->assertEquals('html', $response->getFormat());
		// test 404
		$r = $http->createRequest('http://example.iana.org/tdfsdsf', array(), 'GET');
		$response  = $http->execute($r);
		$this->assertEquals(404, $response->getStatusCode());

	}

	public function testExecutePostRequest() {
		// test posting field as array
		$testBase = 'http://www.24hoursmedia.com/httptests/';
		$http = $this->getHttpClient();
		$r = $http->createRequest($testBase . 'postfields_test.php', array(), 'POST');
		$r->request->set('test', 'test');
		$response  = $http->execute($r);
		$data = json_decode($response->getContent(), true);
		$this->assertTrue(is_array($data));
		$this->assertArrayHasKey('test', $data);
		// nested array
		$r->request->set('test', array("test1" => "a", "test2" => "b"));
		$response  = $http->execute($r);
		$data = json_decode($response->getContent(), true);
		$this->assertArrayHasKey('test', $data);
		$this->assertArrayHasKey('test1', $data['test']);
		$this->assertArrayHasKey('test2', $data['test']);
		// test direct post
		$r = $http->createRequest($testBase . 'inputstream_test.php', array(), 'POST');
		$r->setContent('test');
		$response  = $http->execute($r);
		$this->assertEquals('test', $response->getContent());
	}

	public function testContentNegotiation() {
		// test posting field as array
		$testBase = 'http://www.24hoursmedia.com/httptests/';
		$http = $this->getHttpClient();
		$r = $http->createRequest($testBase . 'contentnegotiation_test.php', array(), 'GET');
		$r->setRequestFormat('json');
		$response = $http->execute($r);
		$this->assertEquals('json', $response->getFormat());

		$r = $http->createRequest($testBase . 'contentnegotiation_test.php', array(), 'GET');
		$r->setRequestFormat('xml');
		$response = $http->execute($r);
		$this->assertEquals('xml', $response->getFormat());
	}

	public function testCache() {
		$http = $this->getHttpClient();
		$r = $http->createRequest('http://www.example.com/a', array(), 'GET');
		$response1  = $http->cacheExecute($r, 4);
		$r = $http->createRequest('http://www.example.com/a', array(), 'GET');
		$response2  = $http->cacheExecute($r, 4);
		$this->assertEquals(1, $response1->headers->get('x-tesla-cache-fresh', null, true));
		$this->assertEquals(0, $response2->headers->get('x-tesla-cache-fresh', null, true));
	}

	public function testBaseUrl() {
		$http = $this->getHttpClient('http://www.24hoursmedia.com/httptests/');
		$r = $http->createRequest('foo.php');
		$foo = $http->execute($r)->getContent();
		$this->assertEquals('foo', $foo);
		$r = $http->createRequest('/foo.php');
		$foo = $http->execute($r)->getContent();
		$this->assertEquals('foo', $foo);
	}

	public function testRequestThroughMediator() {
		$http = $this->getHttpClient();
		$r = $http->createRequest('http://www.example.com');
		$content = $r->execute()->getContent();
		$this->assertContains('example', $content);
	}
}