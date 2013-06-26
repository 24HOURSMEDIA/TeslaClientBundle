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
	 *
	 * @var Sf2Request
	 */
	private $originRequest;

	/**
	 *
	 * @return HttpProxyInterface
	 */
	public function get ($baseUrl = null)
	{
		/* @var $client HttpProxy */
		$client = parent::get($baseUrl);
		$client->setOriginRequest($this->originRequest ? $this->originRequest : new Sf2Request());
		return $client;
	}

	/**
	 * Sets the origin request
	 * @return \Tesla\Bundle\ClientBundle\Proxy\HttpProxy
	 */
	public function setOriginRequest($request) {
		$this->originRequest = $request;
		return $this;
	}

}
