<?php
namespace Tesla\Bundle\ClientBundle\Client;
use Symfony\Component\HttpFoundation\Request;

class TeslaRequest extends Request {


	public function setContent($content) {
		$this->content = $content;
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