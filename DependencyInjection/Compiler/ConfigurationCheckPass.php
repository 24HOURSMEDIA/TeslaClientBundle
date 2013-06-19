<?php
namespace Tesla\Bundle\ClientBundle\DependencyInjection\Compiler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 *
 * @author eapbachman
 *
 */
class ConfigurationCheckPass implements CompilerPassInterface
{
	public function process(ContainerBuilder $container)
	{
		// @TODO: do compiler config checks here
	}
}