<?php

namespace Tesla\Bundle\ClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as SF;

/**
 * @SF\Route("/tesla/client")
 */
class DefaultController extends Controller
{

	/**
	 * @SF\Route("/")
	 * @SF\Template("TeslaClientBundle:Default:index.html.twig")
	 */
    public function indexAction()
    {
    	//echo $this->getRequest()->get('test');
    	//echo $this->getRequest()->request->get('test');
        return array();
    }
}
