<?php
namespace Povs\ListerTwigBundle\Service;

use Povs\ListerBundle\View\FieldView;
use Povs\ListerBundle\View\ViewInterface;
use Throwable;
use Twig\Environment;
use Twig\TemplateWrapper;

/**
 * @author Povilas Margaiatis <p.margaitis@gmail.com>
 */
class ListRenderer
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var string|null
     */
    private $listTemplateName;

    /**
     * @var TemplateWrapper|null
     */
    private $themeTemplate;

    /**
     * @var TemplateWrapper|null
     */
    private $listTemplate;

    /**
     * @var bool
     */
    private $templatesLoaded = false;

    /**
     * ListRenderer constructor.
     *
     * @param Environment $twig
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param string|null $listTemplateName
     */
    public function setListTemplate(?string $listTemplateName): void
    {
        $this->listTemplateName = $listTemplateName;
    }

    /**
     * @param ViewInterface $view
     * @param string        $blockName
     * @param array         $context
     * @param bool          $rewritable
     *
     * @return string
     * @throws Throwable
     */
    public function render(ViewInterface $view, string $blockName, array $context, bool $rewritable): string
    {
        $this->loadTemplates($context);

        if ($rewritable && $block = $this->findBlock($view, $blockName)) {
            return $this->listTemplate->renderBlock($block, $this->getContext($view, $context));
        }

        return $this->themeTemplate->renderBlock($blockName, $this->getContext($view, $context));
    }

    /**
     * @param ViewInterface $view
     * @param string        $blockName
     *
     * @return string|null
     */
    private function findBlock(ViewInterface $view, string $blockName): ?string
    {
        if (!$this->listTemplate) {
            return null;
        }

        if ($view instanceof FieldView) {
            $fieldBlock = sprintf('%s_%s', $blockName, $view->getId());
            $blocks = [$fieldBlock, $blockName];
        } else {
            $blocks = [$blockName];
        }

        foreach ($blocks as $block) {
            if ($this->listTemplate->hasBlock($block, [])) {
                return $block;
            }
        }

        return null;
    }

    /**
     * @param array $context
     */
    private function loadTemplates(array $context): void
    {
        if ($this->templatesLoaded) {
            return;
        }

        $this->themeTemplate = $this->twig->load($context['list_data']['theme']);

        if ($this->listTemplateName) {
            $this->listTemplate = $this->twig->load($this->listTemplateName);
        }

        $this->templatesLoaded = true;
    }

    /**
     * @param ViewInterface $view
     * @param array         $context
     *
     * @return array
     */
    private function getContext(ViewInterface $view, array $context): array
    {
        return array_merge($context, ['list' => $view]);
    }
}