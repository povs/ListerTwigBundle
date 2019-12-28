<?php
namespace Povs\ListerTwigBundle\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

/**
 * @author Povilas Margaiatis <p.margaitis@gmail.com>
 */
class ConfigurationTest extends TestCase
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var Processor
     */
    private $processor;

    public function setUp(): void
    {
        $this->configuration = new Configuration();
        $this->processor = new Processor();
    }

    public function testDefaultConfig(): void
    {
        $defaultConfig = [
            'view_types' => ['list'],
            'resolvable_types' => ['list', 'export'],
            'default_type' => 'list',
            'request' => ['type' => 'lister_type']
        ];

        $config = $this->processor->processConfiguration($this->configuration, []);
        $this->assertEquals($defaultConfig, $config);
    }

    public function testCustomConfig(): void
    {
        $customConfig = [
            'view_types' => ['list', 'another_list'],
            'resolvable_types' => ['list'],
            'default_type' => 'another_list',
        ];

        $expectedConfig = [
            'view_types' => ['list', 'another_list'],
            'resolvable_types' => ['list'],
            'default_type' => 'another_list',
            'request' => ['type' => 'lister_type']
        ];

        $config = $this->processor->processConfiguration($this->configuration, [$customConfig]);
        $this->assertEquals($expectedConfig, $config);
    }
}