<?php
namespace Tesla\Bundle\ClientBundle\Client;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Cache\Cache;

/**
 * Http client implementation
 *
 * @author eapbachman
 *
 */
class HttpClient implements HttpClientInterface
{

	private $baseUrl = '';

	/**
	 *
	 * @var Cache
	 */
	private $cache;

	public function setCache (Cache $cache)
	{
		$this->cache = $cache;
	}

	public function setBaseUrl ($baseUrl)
	{
		$this->baseUrl = $baseUrl;
	}

	public function createRequest ($uri = null, array $parms = array(), $method = 'GET')
	{
		if (substr($uri, 0, 4) != 'http') {
			$base = $this->baseUrl;
			if ((substr($uri, 0, 1) != '/') && (substr($uri, - 1) != '/')) {
				$base .= '/';
			}
			$uri = $base . $uri;
		}

		// parse uri
		// parse GET variables
		$p = parse_url($uri);

		if (count($parms)) {
			$get = array();
			if (isset($p['query'])) {
				parse_str($p['query'], $get);
			}
			$get = array_merge($get, $parms);
			$p['query'] = http_build_query($get);
		}
		$uri = '';
		if (isset($p['scheme'])) {
			$uri .= $p['scheme'] . '://';
		}
		if (isset($p['host'])) {
			$uri .= $p['host'];
		}
		if (isset($p['path'])) {
			$uri .= $p['path'];
		}
		if (isset($p['query'])) {
			$uri .= '?' . $p['query'];
		}
		$r = TeslaRequest::create($uri, $method);
		$r->headers->set('Accept', array(
				'*/*;q=0.1'
		));
		return $r;
	}

	/**
	 * (non-PHPdoc)
	 *
	 * @see \Tesla\Bundle\ClientBundle\Client\HttpClientInterface::execute()
	 * @return TeslaResponse
	 */
	public function execute (Request $request)
	{
		$response = new TeslaResponse();
		$ch = curl_init($request->getUri());
		curl_setopt_array($ch,
				array(
						CURLOPT_CUSTOMREQUEST => $request->getMethod(),
						CURLOPT_FOLLOWLOCATION => true,
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_HEADER => true,
						CURLOPT_VERBOSE => false
				));
		if (($request instanceof TeslaRequest) && (string) $request->getContent()) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, (string) $request->getContent());
		} elseif ($request->request->count() > 0) {
			$query = http_build_query($request->request->all());
			curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
		}
		$created = array();
		foreach ($request->headers->all() as $key => $header) {
			if (is_string($key)) {
				if (! is_array($header)) {
					$header = array(
							$header
					);
				}
				foreach ($header as $h) {
					if (is_string($h)) {
						$realKey = str_replace(' ', '-', ucwords(str_replace('-', ' ', $key)));
						$created[] = $realKey . ': ' . $h;
					}
				}
			}
		}
		curl_setopt($ch, CURLOPT_HTTPHEADER, $created);
		$data = curl_exec($ch);
		$response = TeslaResponse::createFromExecutedCurl($ch, $data);
		curl_close($ch);
		return $response;
	}

	public function cacheExecute (Request $request, $ttl)
	{
		if (! $this->cache) {
			return $this->execute($request);
		}

		if (! in_array($request->getMethod(), array(
				'GET',
				'POST'
		))) {
			return $this->execute($request);
		}
		$key = $request->getUri();
		$response = $this->cache->fetch($key);
		if (! $response) {
			$response = $this->execute($request);
			$response->headers->set('x-tesla-cached', date('Y-m-d H:i:s'));
			$response->headers->set('x-tesla-cache-fresh', 1);
		} else {
			$response->headers->set('x-tesla-cache-fresh', 0);
		}
		$this->cache->save($key, $response, $ttl);
		return $response;
	}
}