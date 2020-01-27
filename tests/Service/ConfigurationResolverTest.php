<?php

namespace Povs\ListerTwigBundle\Service;

use PHPUnit\Framework\TestCase;

/**
 * @author Povilas Margaiatis <p.margaitis@gmail.com>
 */
class ConfigurationResolverTest extends TestCase
{
    /**
     * @dataProvider getViewTypesProvider
     *
     * @param array $config
     * @param array $expected
     */
    public function testGetViewTypes(array $config, array $expected): void
    {
        $this->assertEquals($expected, $this->getConfigurationResolver($config)->getViewTypes());
    }

    public function getViewTypesProvider(): array
    {
        return [
            [['view_types' => ['foo', 'bar']] , ['foo', 'bar']],
            [[] , ['list']],
        ];
    }

    /**
     * @dataProvider getDefaultTypeProvider
     *
     * @param array  $config
     * @param string $expected
     */
    public function testGetDefaultType(array $config, string $expected): void
    {
        $this->assertEquals($expected, $this->getConfigurationResolver($config)->getDefaultType());
    }

    public function getDefaultTypeProvider(): array
    {
        return [
            [['default_type' => 'foo'], 'foo'],
            [[], 'list']
        ];
    }

    /**
     * @dataProvider getResolvableTypesProvider
     *
     * @param array $config
     * @param array $expected
     */
    public function testGetResolvableTypes(array $config, array $expected): void
    {
        $this->assertEquals($expected, $this->getConfigurationResolver($config)->getResolvableTypes());
    }

    public function getResolvableTypesProvider(): array
    {
        return [
            [['resolvable_types' => ['list']], ['list']],
            [[], ['list', 'export']]
        ];
    }

    /**
     * @dataProvider getTypeNameProvider
     *
     * @param array  $config
     * @param string $expected
     */
    public function testGetTypeName(array $config, string $expected): void
    {
        $this->assertEquals($expected, $this->getConfigurationResolver($config)->getTypeName());
    }

    public function getTypeNameProvider(): array
    {
        return [
            [['request' => ['type' => 'foo_bar']], 'foo_bar'],
            [[], 'lister_type']
        ];
    }

    /**
     * @param array $config
     *
     * @return ConfigurationResolver
     */
    private function getConfigurationResolver(array $config): ConfigurationResolver
    {
        return new ConfigurationResolver([$config]);
    }
}
