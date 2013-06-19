<?php
namespace Tesla\Bundle\ClientBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tesla\Bundle\ClientBundle\DependencyInjection\Compiler\ConfigurationCheckPass;

/**
 * Bundle
 * @author eapbachman
 */
class TeslaClientBundle extends Bundle
{

	public function build (ContainerBuilder $container)
	{
		// check configuration
		$container->addCompilerPass(new ConfigurationCheckPass());
	}
}
