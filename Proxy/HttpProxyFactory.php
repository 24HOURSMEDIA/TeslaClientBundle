<?php
namespace Tesla\Bundle\ClientBundle\Proxy;
use Tesla\Bundle\ClientBundle\Client\HttpClientFactory;
use Tesla\Bundle\ClientBundle\Proxy\HttpProxy;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request as Sf2Request;

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
	 * Required to set the request object
	 * @var ContainerInterface
	 */
	private $container;

	/**
	 *
	 * @return HttpProxyInterface
	 */
	public function get ($baseUrl = null)
	{
		/* @var $client HttpProxy */
		$client = parent::get($baseUrl);
		// st the originating request
		if ($this->container->isScopeActive('request')) {
			$origin = $this->container->get('request');
		} else {
			$origin = new Sf2Request();
		}
		$client->setOriginRequest($origin);
		return $client;
	}

	/**
	 * Sets the origin request
	 * @return \Tesla\Bundle\ClientBundle\Proxy\HttpProxy
	 */
	public function setContainer($container) {
		$this->container = $container;
		return $this;
	}

}
