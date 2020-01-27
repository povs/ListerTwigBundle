<?php

namespace Povs\ListerTwigBundle\Service;

use Povs\ListerBundle\Declaration\ListerInterface;
use Povs\ListerBundle\Service\ListManager;
use Povs\ListerTwigBundle\Declaration\ViewListerInterface;
use Symfony\Component\HttpFoundation\Response;
use Povs\ListerBundle\Service\Lister;

/**
 * @author Povilas Margaiatis <p.margaitis@gmail.com>
 */
class ViewLister extends Lister implements ViewListerInterface
{
    /**
     * @var TypeResolver
     */
    private $typeResolver;

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $typeOptions = [];

    /**
     * @param ListManager  $listManager
     * @param TypeResolver $typeResolver
     */
    public function __construct(ListManager $listManager, TypeResolver $typeResolver)
    {
        parent::__construct($listManager);
        $this->typeResolver = $typeResolver;
    }

    /**
     * @inheritDoc
     */
    public function setOptions(string $type, array $options): ViewListerInterface
    {
        $this->typeOptions[$type] = $options;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function buildList(string $list, ?string $type = null, array $parameters = []): ListerInterface
    {
        if (!$type) {
            $type = $this->typeResolver->resolveType();
        }

        $this->type = $type;

        return parent::buildList($list, $type, $parameters);
    }

    /**
     * @inheritDoc
     */
    public function generateView(string $template, array $parameters = []): Response
    {
        return $this->generateResponse($this->getOptions($template, $parameters));
    }

    /**
     * @inheritDoc
     */
    public function getView(string $template, array $parameters = []): string
    {
        return $this->generateData($this->getOptions($template, $parameters));
    }

    /**
     * @param string $template
     * @param array  $parameters
     *
     * @return array
     */
    private function getOptions(string $template, array $parameters = []): array
    {
        if (!$this->type) {
            return [];
        }

        if (array_key_exists($this->type, $this->typeOptions)) {
            return $this->typeOptions[$this->type];
        }

        if ($this->typeResolver->isViewType($this->type)) {
            return [
                'template' => $template,
                'context' => $parameters
            ];
        }

        return [];
    }
}
