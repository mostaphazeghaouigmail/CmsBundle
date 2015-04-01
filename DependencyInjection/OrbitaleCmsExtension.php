<?php
/*
* This file is part of the OrbitaleCmsBundle package.
*
* (c) Alexandre Rock Ancelet <alex@orbitale.io>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Orbitale\Bundle\CmsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class OrbitaleCmsExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        foreach ($config['layouts'] as $name => $layout) {
            $config['layouts'][$name] = array_merge(array(
                'name' => $name,
                'assets_css' => '',
                'assets_js' => '',
                'host' => '',
                'pattern' => '^/',
            ), $layout);
        }

        // Sort configs by host, because host is checked before pattern.
        uasort($config['layouts'], function($a, $b) {
            return ($a['host'] - $b['host']) < 0 ? -1 : 0;
        });

        $container->setParameter('orbitale_cms.config', $config);
    }
}
