<?php

namespace Povs\ListerTwigBundle\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Povs\ListerTwigBundle\Type\ListType\TwigListType;
use Povs\ListerTwigBundle\Type\ListType\AjaxListType;

/**
 * @author Povilas Margaiatis <p.margaitis@gmail.com>
 */
class PovsListerTwigExtensionTest extends TestCase
{
    private $container;
    private $extension;

    public function setUp()
    {
        $this->container = new ContainerBuilder();
        $this->extension = new PovsListerTwigExtension();
    }

    public function testLoadServicesDefinition(): void
    {
        $services = [
            'povs.view_lister',
            '.povs_lister.twig.renderer',
            '.povs_lister.twig.extension.list',
            TwigListType::class,
            AjaxListType::class
        ];

        $this->extension->load([], $this->container);

        foreach ($services as $service) {
            $this->assertTrue($this->container->hasDefinition($service), $service);
        }
    }
}
