<?php

namespace SymfonySkillbox\HomeworkBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class HomeworkBundleExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(dirname(__DIR__) . "/Resources/config/"));
        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs, $container);

        $config = $this->processConfiguration($configuration, $configs);

        if (null !== $config['strategy']) {
            $container->setAlias('symfony_skillbox_homework.strategy', $config['strategy']);
        }

        if (null !== $config['unit_provider']) {
            $container->setAlias('symfony_skillbox_homework.unit_provider', $config['unit_provider']);
        }

        $definition = $container->getDefinition('symfony_skillbox_homework.base_unit_provider');
        $definition->setArgument(0, $config['enable_solder']);
        $definition->setArgument(1, $config['enable_archer']);
    }

    public function getAlias()
    {
        return 'symfony_skillbox_homework';
    }
}
