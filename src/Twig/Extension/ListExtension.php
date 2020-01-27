<?php

namespace Povs\ListerTwigBundle\Twig\Extension;

use Povs\ListerTwigBundle\Service\ListRenderer;
use Closure;
use Povs\ListerBundle\View\ViewInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Povilas Margaiatis <p.margaitis@gmail.com>
 */
class ListExtension extends AbstractExtension
{
    /**
     * @var ListRenderer
     */
    private $renderer;

    /**
     * ListExtension constructor.
     *
     * @param ListRenderer $renderer
     */
    public function __construct(ListRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * @inheritdoc
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'list_view',
                Closure::fromCallable([$this, 'listView']),
                ['is_safe' => ['html'], 'needs_context' => true]
            ),
        ];
    }

    /**
     * @param array         $context
     * @param ViewInterface $view
     * @param string        $blockName
     * @param bool          $rewritable
     *
     * @return string
     */
    public function listView(
        array $context,
        ViewInterface $view,
        string $blockName = 'list_parent',
        bool $rewritable = true
    ): string {
        return $this->renderer->render($view, $blockName, $context, $rewritable);
    }
}
