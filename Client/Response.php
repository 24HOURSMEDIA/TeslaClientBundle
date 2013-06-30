<?php
namespace Tesla\Bundle\ClientBundle\Client;
use Symfony\Component\HttpFoundation\Response as Sf2Response;
class Response extends Sf2Response {

	/**
	 * @var array
	 */
	protected static $formats;

	/**
	 * Initializes HTTP request formats.
	 */
	protected static function initializeFormats()
	{
		static::$formats = array(
				'html' => array('text/html', 'application/xhtml+xml'),
				'txt'  => array('text/plain'),
				'js'   => array('application/javascript', 'application/x-javascript', 'text/javascript'),
				'css'  => array('text/css'),
				'json' => array('application/json', 'application/x-json'),
				'xml'  => array('text/xml', 'application/xml', 'application/x-xml'),
				'rdf'  => array('application/rdf+xml'),
				'atom' => array('application/atom+xml'),
				'rss'  => array('application/rss+xml'),
		);
	}

	/**
	 * Gets the format of the respose (i.e. html, txt, js, css, xml, rss)
	 * @return string | null
	 */
	public function getFormat() {
		$mimeType = $this->headers->get('content-type', null, true);
		if (false !== $pos = strpos($mimeType, ';')) {
			$mimeType = substr($mimeType, 0, $pos);
		}

		if (null === static::$formats) {
			static::initializeFormats();
		}

		foreach (static::$formats as $format => $mimeTypes) {
			if (in_array($mimeType, (array) $mimeTypes)) {
				return $format;
			}
		}
		return null;
	}

	/**
	 * Factory method to create a response from a load curl handle (and the contents)
	 * @param unknown $ch
	 * @param string $content
	 * @return \Tesla\Bundle\ClientBundle\Client\Response
	 */
	public static function createFromExecutedCurl($ch, $content) {
		$response = new Response();
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if (!$code) {
			$code = 404;
		}
		$response->setStatusCode($code);
		$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($content, 0, $headerSize);
		if ($content) {
			$response->setContent((string)substr($content, $headerSize));
		}
		// parse the headers
		$rawHeaders = explode("\r\n", $header);
		foreach ($rawHeaders as $header) {
			$parts = explode(':', $header, 2);
			if (count($parts) == 2) {
				$vals = $response->headers->get($parts[0], array());
				if (is_array($vals)) {
					$vals[] = trim($parts[1]);
				} else {
					$vals = $parts[1];
				}
				$response->headers->set($parts[0], $vals);
			}
		}
		return $response;
	}

}
