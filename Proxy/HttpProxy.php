<?php
namespace Tesla\Bundle\ClientBundle\Proxy;
use Tesla\Bundle\ClientBundle\Client\HttpClient;
use Tesla\Bundle\ClientBundle\Client\HttpClientInterface;
use Symfony\Component\HttpFoundation\Request as Sf2Request;
class HttpProxy extends HttpClient implements HttpProxyInterface
{

	private $translationUrls = array();

	/**
	 * If this behaviour is true, the resulting url is always url encoded and passed through the base url
	 * (which MUST be set). It is automatically append to the end of the proxy base url.
	 * @var boolean
	 */
	private $passEncodedUrlBehavior = false;

	private $enabled = true;

	private $proxiedHeaders = array();

	/**
	 * The original symfony2 request
	 * @var Sf2Request
	 */
	private $originRequest;


	/**
	 * Override
	 * (non-PHPdoc)
	 * @see \Tesla\Bundle\ClientBundle\Client\HttpClient::createRequest()
	 */
	private function _createRequest($uri, $params, $method) {
		$r = parent::createRequest($uri, $params, $method);
		// transfer proxied headers
		// this way because sometimes sf does not return multiple headers with same key as array
		$allHeaders = $this->originRequest->headers->all();

		$singularHeaders = array('user-agent');
		foreach ($this->proxiedHeaders as $key) {
			$key = strtolower($key);
			$values = isset($allHeaders[$key]) ? $allHeaders[$key] : array();
			$values = is_array($values) ? $values: array($values);
			foreach ($values as $v) {

					$r->headers->set($key, $v, in_array($key, $singularHeaders));

			}

		}
		return $r;
	}

	public function createRequest ($uri = null, array $parms = array(), $method = 'GET')
	{

		if (!$this->enabled) {
			// do nothing if the proxy is disabled, direct pass.
			return $this->_createRequest($uri, $parms, $method);
		}


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

		return $this->_createRequest($uri, $parms, $method);
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
	/**
	 * @return the $enabled
	 */
	public function getEnabled ()
	{
		return $this->enabled;
	}

	/**
	 * @param boolean $enabled
	 */
	public function setEnabled ($enabled)
	{
		$this->enabled = $enabled;
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see \Tesla\Bundle\ClientBundle\Proxy\HttpProxyInterface::setProxiedHeaders()
	 */
	public function setProxiedHeaders(array $headers) {
		$this->proxiedHeaders = $headers;
		return $this;
	}

	/**
	 * Sets the origin request
	 * @param Sf2Request $request
	 * @return \Tesla\Bundle\ClientBundle\Proxy\HttpProxy
	 */
	public function setOriginRequest(Sf2Request $request) {
		$this->originRequest = $request;
		return $this;
	}

}
