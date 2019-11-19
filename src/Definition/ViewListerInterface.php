<?php
namespace Povs\ListerTwigBundle\Definition;

use Symfony\Component\HttpFoundation\Response;
use \Povs\ListerBundle\Definition\ListerInterface;

/**
 * @author Povilas Margaiatis <p.margaitis@gmail.com>
 */
interface ViewListerInterface extends ListerInterface
{
    /**
     * @param string $template   twig template name
     * @param array  $parameters template context
     *
     * @return Response
     */
    public function generateView(string $template, array $parameters = []): Response;

    /**
     * @param string $template   twig template name
     * @param array  $parameters template context
     *
     * @return string
     */
    public function getView(string $template, array $parameters = []): string;
}