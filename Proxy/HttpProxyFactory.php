<?php
namespace Tesla\Bundle\ClientBundle\Proxy;
use Tesla\Bundle\ClientBundle\Client\HttpClientFactory;
use Tesla\Bundle\ClientBundle\Proxy\HttpProxy;
use Doctrine\Common\Cache\Cache;

/**
 * Factory for http clients
 *
 * @author eapbachman
 *
 */
class HttpProxyFactory extends HttpClientFactory
{

	protected $class = 'Tesla\Bundle\ClientBundle\Proxy\HttpProxy';

	/**
	 *
	 * @return HttpProxyInterface
	 */
	public function get ($baseUrl = null)
	{
		$client = parent::get($baseUrl);

		return $client;
	}
}
