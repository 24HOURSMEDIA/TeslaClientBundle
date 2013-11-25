# TeslaClientBundle for Symfony 2

Symfony bundle that provides:
- http client
- caching for http client
- proxy

Requests to a client are done through a subclass of SF's Request instances.

## installation

Via composer:

(1) add following lines to composer.json:

	(...)
    	"repositories": [
        	{
           		"type": "git",
            	"url": "git@github.com:24HOURSMEDIA/TeslaClientBundle.git"
        	}
    	],
    (..)
    
    "require": {
    	(...)
    	"24hoursmedia/teslaclientbundle": "master-dev"
    	(...)
    }
    
(2) run composer update

	php composer.phar update
	
(3) add bundle to AppKernel.php

	$bundles = array(
		(....)
		new Tesla\Bundle\ClientBundle\TeslaClientBundle()
	);


## history
20131125 added possibility to directly inject additional curl options into client and proxy service

## examples

### http client


create client from a factory and do a request:
   
    use Tesla\Bundle\ClientBundle\Client\HttpClientFactory;
    use Tesla\Bundle\ClientBundle\Client\HttpClientInterface;
    use Tesla\Bundle\ClientBundle\Client\TeslaRequest;
    use Tesla\Bundle\ClientBundle\Client\TeslaResponse;

    /* @var $factory HttpClientFactory */
    $factory = $client->getContainer()->get('tesla_client.http_client_factory');
    /* @var $client HttpClientInterface */
    $http = $factory->get($baseUrl);

    $request = $http->createRequest('http://www.example.com');
    // (add headers and other request stuff here) //
    $response = $http->execute($request);
    $format = $response->getFormat(); // (json etc)
    $content = $response->getContent();
    $status = $response->getStatusCode();
    
   
### create http client in services.yml

configure an http client as a service which can be obtained through the container:

    acme.http_client:
        class: %tesla_client.http_client.class%
        factory_service: tesla_client.http_client_factory
        factory_method: get
        arguments:
            - http://www.example.com
        

### create http proxy as a service in services.yml

create a proxy service that proxies requests from http://foo & http://bar/sub to http://www.example.com:

    acme.http_proxy_example:
        class: %tesla_client.http_proxy.class%
        factory_service: tesla_client.httpproxy_factory
        factory_method: get
        arguments:
            - http://www.example.com
        calls:
            - [addTranslationUrl, ["http://foo"]]
            - [addTranslationUrl, ["http://bar/sub"]]
            
### use a proxy

example of using a proxy defined as a service:

	use Tesla\Bundle\ClientBundle\Proxy\HttpProxy;
	
	/* @var $proxy HttpProxy */
	$proxy = $client->getContainer()->get('tesla_client.http_proxy_example');

	// foo host gets proxied to example.com
	$responseContent = $proxy->execute($proxy->createRequest('http://foo'))->getContent();
	$this->assertContains('example', $responseContent);
	// foo bar/sub gets proxied to example.com
	$responseContent = $proxy->execute($proxy->createRequest('http://bar/sub'))->getContent();
            
