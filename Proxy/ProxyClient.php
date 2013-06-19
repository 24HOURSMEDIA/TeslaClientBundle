<?php
namespace Tesla\Bundle\ClientBundle\Proxy;
use Tesla\Bundle\ClientBundle\Client\HttpClient;

class ProxyClient extends HttpClient
{

	private $translationUrls = array();

	public function createRequest ($uri = null, array $parms = array(), $method = 'GET')
	{

		// translate the uri...
		$parts = parse_url($uri);
		$uri = (isset($parts['scheme']) ? $parts['scheme'] . '://' . $parts['host'] : '') . (isset($parts['path']) ? $parts['path'] : '/') .
				 (isset($parts['query']) ? '?' . $parts['query'] : '');

		foreach ($this->translationUrls as $url) {
			$parts = parse_url($url);
			$url = $parts['scheme'] . '://' . $parts['host'];
			if ($parts['path']) {
				$url .= $parts['path'];
			}
			if (substr($uri, 0, strlen($url)) == $url) {
				$uri = substr($uri, strlen($url));
			}
		}
		return parent::createRequest($uri, $parms, $method);
	}

	/**
	 *
	 * @return the $translationUrls
	 */
	public function getTranslationUrls ()
	{
		return $this->translationUrls;
	}

	/**
	 *
	 * @param multitype: $translationUrls
	 */
	public function setTranslationUrls ($translationUrls)
	{
		$this->translationUrls = $translationUrls;
		return $this;
	}
}
