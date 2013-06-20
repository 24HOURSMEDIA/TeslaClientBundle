<?php
namespace Tesla\Bundle\ClientBundle\Proxy;
use Tesla\Bundle\ClientBundle\Client\HttpClientInterface;

interface HttpProxyInterface extends HttpClientInterface {

	/**
	 * @return HttpProxyInterface
	 */
	public function getTranslationUrls ();
	/**
	 * @return HttpProxyInterface
	 */
	public function setTranslationUrls ($translationUrls);
	/**
	 * @return HttpProxyInterface
	 */
	public function addTranslationUrl($url);



}