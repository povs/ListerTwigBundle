<?php
namespace Povs\ListerTwigBundle\Service;

use Povs\ListerBundle\Service\RequestHandler;

/**
 * @author Povilas Margaiatis <p.margaitis@gmail.com>
 */
class TypeResolver
{
    /**
     * @var RequestHandler
     */
    private $requestHandler;

    /**
     * @var ConfigurationResolver
     */
    private $configurationResolver;

    /**
     * @param RequestHandler        $requestHandler
     * @param ConfigurationResolver $configurationResolver
     */
    public function __construct(RequestHandler $requestHandler, ConfigurationResolver $configurationResolver)
    {
        $this->requestHandler = $requestHandler;
        $this->configurationResolver = $configurationResolver;
    }

    /**
     * @return string
     */
    public function resolveType(): string
    {
        $typeRequestName = $this->configurationResolver->getTypeName();
        $type = $this->requestHandler->getRequest()->query->get($typeRequestName);

        if (!$type) {
            $type = $this->configurationResolver->getDefaultType();
        }

        return $type;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function isViewType(string $type): bool
    {
        return in_array($type, $this->configurationResolver->getViewTypes(), true);
    }
}