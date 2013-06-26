<?php
namespace Tesla\Bundle\ClientBundle\Tests\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tesla\Bundle\ClientBundle\Proxy\HttpProxyFactory;
use Tesla\Bundle\ClientBundle\Proxy\HttpProxy;
use Symfony\Component\HttpFoundation\Request as Sf2Request;
class ProxyTest extends WebTestCase
{

	public function testProxy ()
	{
		$client = static::createClient();
		/* @var $factory HttpProxyFactory */
		$factory = $client->getContainer()->get('tesla_client.httpproxy_factory');

		// test if the base urls below get properly translated
		$urlset = array(
				'http://host1abc/',
				'http://host2/abc',
				'http://host2/abd'
		);
		$proxy = $factory->get('http://example.iana.org', $urlset);
		$proxy->setTranslationUrls($urlset);

		$content = $proxy->execute($proxy->createRequest('http://host1abc/'))
			->getContent();
		$this->assertContains('example', $content);

		$content = $proxy->execute($proxy->createRequest('http://host1abc'))
			->getContent();
		$this->assertContains('example', $content);

		$content = $proxy->execute($proxy->createRequest('http://host2/abc'))
			->getContent();
		$this->assertContains('example', $content);

		$content = $proxy->execute($proxy->createRequest('http://host2/abd'))
			->getContent();
		$this->assertContains('example', $content);

		$content = $proxy->execute($proxy->createRequest('http://host2/abd/'))
			->getContent();
		$this->assertContains('example', $content);

		$content = $proxy->execute($proxy->createRequest(''))
			->getContent();
		$this->assertContains('example', $content);
		$content = $proxy->execute($proxy->createRequest('?q=1'))
			->getContent();
		$this->assertContains('example', $content);
		$content = $proxy->execute($proxy->createRequest('/'))
			->getContent();
		$this->assertContains('example', $content);

		// cache test.. two different domains - the second should be cached
		$uid = uniqid('!');
		$response1 = $proxy->cacheExecute($proxy->createRequest('http://host2/abd/', array(
				'uid' => $uid
		)), 4);
		$response2 = $proxy->cacheExecute($proxy->createRequest('http://host2/abc', array(
				'uid' => $uid
		)), 4);
		$this->assertEquals(1, $response1->headers->get('x-tesla-cache-fresh', null, true));
		$this->assertEquals(0, $response2->headers->get('x-tesla-cache-fresh', null, true));
	}

	/**
	 * Tests wether indicated headers are correctly transferred
	 */
	public function testHeaderTransfer() {
		$client = static::createClient();
		/* @var $factory HttpProxyFactory */
		$factory = $client->getContainer()->get('tesla_client.httpproxy_factory');
		/* @var $proxy HttpProxy */
		$proxy = $factory->get();
		$proxy->setForwardedHeaders(array('user-agent', 'X-My-custom-Header'));
		// create a stub request
		$origin = new Sf2Request();
		$origin->headers->set('user-agent', 'testagent');
		$origin->headers->set('X-my-custom-Header', array('test1', 'test2'));
		$proxy->setOriginRequest($origin);
		$proxyRequest = $proxy->createRequest('http://www.24hoursmedia.com/httptests/useragent.php');
		$agent = $proxyRequest->headers->get('User-Agent');
		$this->assertEquals('testagent', $proxyRequest->headers->get('User-Agent'), 'The user agent is not correctly transferred');
		$all = $proxyRequest->headers->all();
		$this->assertEquals(array('test1', 'test2'), $all['x-my-custom-header'], 'The custom header array not correctly transferred');

		$remoteAgent = $proxyRequest->execute()->getContent();
		$this->assertEquals($remoteAgent, 'testagent');
	}
}