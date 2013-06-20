<?php
namespace Tesla\Bundle\ClientBundle\Proxy;
use Tesla\Bundle\ClientBundle\Client\HttpClient;
use Tesla\Bundle\ClientBundle\Client\HttpClientInterface;

class HttpProxy extends HttpClient implements HttpProxyInterface
{

	private $translationUrls = array();

	/**
	 * If this behaviour is true, the resulting url is always url encoded and passed through the base url
	 * (which MUST be set). It is automatically append to the end of the proxy base url.
	 * @var boolean
	 */
	private $passEncodedUrlBehavior = false;

	public function createRequest ($uri = null, array $parms = array(), $method = 'GET')
	{

		// translate the uri...
		$parts = parse_url($uri);
		$uri = (isset($parts['scheme']) ? $parts['scheme'] . '://' . $parts['host'] : '') . (isset($parts['path']) ? $parts['path'] : '/') .
				 (isset($parts['query']) ? '?' . $parts['query'] : '');

		foreach ($this->translationUrls as $url) {
			$parts = parse_url($url);
			$url = $parts['scheme'] . '://' . $parts['host'];
			if (isset($parts['path'])) {
				$url .= $parts['path'];
			}
			if (substr($uri, 0, strlen($url)) == $url) {
				$uri = substr($uri, strlen($url));
			}
		}

		if ($this->passEncodedUrlBehavior) {
			if (!$this->getBaseUrl()) {
				throw new \RuntimeException('For passEncodedUrlBehavior proxy base url MUST be set');
			}
			$uri = $this->getBaseUrl() . urlencode($uri);
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

	public function addTranslationUrl($url) {
		$this->translationUrls[] = $url;
	}
	/**
	 * @return the $passEncodedUrlBehavior
	 */
	public function getPassEncodedUrlBehavior ()
	{
		return $this->passEncodedUrlBehavior;
	}

	/**
	 * @param boolean $passEncodedUrlBehavior
	 */
	public function setPassEncodedUrlBehavior ($passEncodedUrlBehavior)
	{
		$this->passEncodedUrlBehavior = $passEncodedUrlBehavior;
		return $this;
	}

}
