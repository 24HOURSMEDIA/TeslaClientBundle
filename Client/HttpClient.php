<?php
namespace Tesla\Bundle\ClientBundle\Client;

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

    private $ch = null;

    /**
     *
     * @var Cache
     */
    private $cache;

    private $basicAuth = false;
    private $basicAuthUser = '';
    private $basicAuthPassword = '';

    private $curlOpts = array(
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_VERBOSE => false
    );

    /**
     * Array with request headers to set in key value pairs, i.e.
     * [ ['user-agent', 'browser'], [.., ..]  ]
     * @var unknown
     */
    protected $requestHeaders = array();

    public function basicAuthentication($user = null, $password = null)
    {
        $this->basicAuth = $user && $password;
        $this->basicAuthUser = $user;
        $this->basicAuthPassword = $password;

        return $this;
    }

    /**
     * Sets a CURL option
     * @api
     * @param $key
     * @param $val
     */
    public function setCurlOpt($key, $val)
    {
        if (substr($key, 0, 8) != 'CURLOPT_') {
            throw new \RuntimeException('Invalid CURL Option key');
        }
        $consts = get_defined_constants();
        if (!isset($consts[$key])) {
            throw new \RuntimeException('Invalid CURL Option key');
        }
        $this->curlOpts[$consts[$key]] = $val;

        return $this;
    }

    public function setCache(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    public function createRequest($uri = null, array $parms = array(), $method = 'GET')
    {
        if (substr($uri, 0, 4) != 'http') {
            $base = $this->baseUrl;
            if ((substr($uri, 0, 1) != '/') && (substr($uri, -1) != '/')) {
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
        if (isset($p['port']) && $p['port'] != '80') {
            $uri .= ':'.$p['port'];
        }
        if (isset($p['path'])) {
            $uri .= $p['path'];
        }
        if (isset($p['query'])) {
            $uri .= '?' . $p['query'];
        }
        $r = Request::create($uri, $method);
        $r->setMediator($this);
        $r->headers->set(
            'Accept',
            array(
                '*/*;q=0.1'
            )
        );

        $singularHeaders = array('user-agent');
        foreach ($this->requestHeaders as $h) {
            $r->headers->set($h[0], $h[1], in_array($h[0], $singularHeaders));
        }

        return $r;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Tesla\Bundle\ClientBundle\Client\HttpClientInterface::execute()
     * @return Response
     */
    public function execute(Request $request)
    {

        $ch = $this->ch ? $this->ch : ($this->ch = curl_init());
        $opts =
            $this->curlOpts +
            array(
                CURLOPT_URL => $request->getUri(),
                CURLOPT_CUSTOMREQUEST => $request->getMethod(),
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => true
            )
        ;
        curl_setopt_array($ch, $opts);

		if ($this->basicAuth) {
            curl_setopt($ch, CURLOPT_USERPWD, $this->basicAuthUser . ":" . $this->basicAuthPassword);
        }
		if (($request instanceof Request) && (string)$request->getContent()) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, (string)$request->getContent());
        } elseif ($request->request->count() > 0) {
            $query = http_build_query($request->request->all());
            curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        }
		$created = array();
		foreach ($request->headers->all() as $key => $header) {
            if (is_string($key)) {
                if (!is_array($header)) {
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

		$response = Response::createFromExecutedCurl($ch, $data);
		
		return $response;
	}

    public function cacheExecute(Request $request, $ttl)
    {
        if (!$this->cache) {

            return $this->execute($request);
        }

        if (!in_array(
            $request->getMethod(),
            array(
                'GET',
                'HEAD'
            )
        )
        ) {
            return $this->execute($request);
        }
        $key = $request->getCacheKey();

        if ($this->cache->contains($key)) {
            $response = $this->cache->fetch($key);
            $response->headers->set('x-tesla-cache-fresh', 0);
        } else {
            $response = $this->execute($request);
            $response->headers->set('x-tesla-cached', date('Y-m-d H:i:s'));
            $response->headers->set('x-tesla-cache-fresh', 1);
            $this->cache->save($key, $response, $ttl);
        }
        return $response;
    }

    /**
     *
     * @return the $baseUrl
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    public function setRequestHeaders(array $headers)
    {
        $this->requestHeaders = $headers;

        return $this;
    }

    public function addRequestHeader($key, $val)
    {

        $this->requestHeaders[] = array($key, $val);

        return $this;
    }
}