<?php
namespace Tesla\Bundle\ClientBundle\Client;

use Doctrine\Common\Cache\Cache;
/**
 * Factory for http clients
 * @author eapbachman
 *
 */
class HttpClientFactory {

	protected $class = 'Tesla\Bundle\ClientBundle\Client\HttpClient';

	private $config;
	private $cache;

	public function __construct($config, Cache $cache) {
		$this->config = $config;
		$this->cache = $cache;
	}

	/**
	 * @return HttpClientInterface
	 */
	public function get($baseUrl = null) {


		$client = new $this->class();
		$client->setBaseUrl($baseUrl);
		$client->setCache($this->cache);
		return $client;
	}
	/**
	 * @return the $class
	 */
	public function getClass ()
	{
		return $this->class;
	}

	/**
	 * @param string $class
	 */
	public function setClass ($class)
	{
		$this->class = $class;
		return $this;
	}


}
