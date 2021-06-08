<?php

namespace SymfonySkillbox\HomeworkBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('symfony_skillbox_homework');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
            ->scalarNode('strategy')->defaultNull()
                ->info('Стратегия производства юнитов по умолчанию по силе (StrengthStrategy)')
                ->end()
            ->scalarNode('unit_provider')->defaultNull()
                ->info('Список доступных юнитов (BaseUnitProvider)')
                ->end()
            ->booleanNode('enable_solder')->defaultTrue()->info('Разрешено ли производство солдат')->end()
            ->booleanNode('enable_archer')->defaultTrue()->info('Разрешено ли производство лучников')->end()
            ->end();

        return $treeBuilder;
    }
}
