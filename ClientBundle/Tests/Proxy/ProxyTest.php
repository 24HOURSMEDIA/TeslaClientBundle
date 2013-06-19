<?php
namespace Tesla\Bundle\ClientBundle\Tests\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tesla\Bundle\ClientBundle\Proxy\HttpProxyFactory;

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
}