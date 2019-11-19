<?php
namespace Povs\ListerTwigBundle\Service;

use Povs\ListerTwigBundle\Definition\ViewListerInterface;
use Symfony\Component\HttpFoundation\Response;
use \Povs\ListerBundle\Service\Lister;

/**
 * @author Povilas Margaiatis <p.margaitis@gmail.com>
 */
class ViewLister extends Lister implements ViewListerInterface
{
    /**
     * @inheritDoc
     */
    public function generateView(string $template, array $parameters = []): Response
    {
        return $this->generateResponse([
            'template' => $template,
            'context' => $parameters
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getView(string $template, array $parameters = []): string
    {
        return $this->generateData([
            'template' => $template,
            'context' => $parameters
        ]);
    }
}