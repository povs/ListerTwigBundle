<?php
namespace Povs\ListerTwigBundle\Service;

use Povs\ListerTwigBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

/**
 * @author Povilas Margaiatis <p.margaitis@gmail.com>
 */
class ConfigurationResolver
{
    /**
     * @var array
     */
    private $configuration;

    /**
     * ConfigurationResolver constructor.
     *
     * @param array $configs
     */
    public function __construct(array $configs)
    {
        $configuration = new Configuration();
        $processor = new Processor();
        $this->configuration = $processor->processConfiguration($configuration, $configs);
    }

    /**
     * @return array
     */
    public function getViewTypes(): array
    {
        return $this->configuration['view_types'];
    }

    /**
     * @return string
     */
    public function getDefaultType(): string
    {
        return $this->configuration['default_type'];
    }

    /**
     * @return array
     */
    public function getResolvableTypes(): array
    {
        return $this->configuration['resolvable_types'];
    }

    /**
     * @return string
     */
    public function getTypeName(): string
    {
        return $this->configuration['request']['type'];
    }
}