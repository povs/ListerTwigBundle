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
     * To overwrite view options or set options for other types. i.e export
     *
     * @param string $type    type name
     * @param array  $options type options
     *
     * @return self
     */
    public function setOptions(string $type, array $options): self;

    /**
     * More friendly function for generating view type
     *
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