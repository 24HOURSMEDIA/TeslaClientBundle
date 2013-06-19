<?php
// master dev
namespace Tesla\Bundle\ClientBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tesla\Bundle\ClientBundle\DependencyInjection\Compiler\ConfigurationCheckPass;

class TeslaClientBundle extends Bundle
{
	public function build(ContainerBuilder $container)
	{
		$container->addCompilerPass(new ConfigurationCheckPass());
	}
}
