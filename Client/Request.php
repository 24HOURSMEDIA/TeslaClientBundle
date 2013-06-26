<?php
namespace Tesla\Bundle\ClientBundle\Client;
use Symfony\Component\HttpFoundation\Request as Sf2Request;

class Request extends Sf2Request {


	/**
	 *
	 * @var HttpClientInterface
	 */
	private $mediator;

	public function setContent($content) {
		$this->content = $content;
	}

	public function setMediator(HttpClientInterface $mediator) {
		$this->mediator = $mediator;
	}

	/**
	 * Executes the request through the mediator
	 */
	public function execute() {
		return $this->mediator->execute($this);
	}

	/**
	 * Executes the request through the mediator
	 */
	public function cacheExecute($ttl) {
		return $this->mediator->cacheExecute($this, $ttl);
	}

	public function setRequestFormat($format, $priority = 0) {
		parent::setRequestFormat($format);
		$mimeType = $this->getMimeType($format);
		if ($priority) {
			$mimeType.=';q=' . $priority;
		}
		$this->headers->set('accept', array($mimeType), false);
		return $this;
	}




}