<?php
namespace Povs\ListerTwigBundle\Type\ListType;

use Povs\ListerBundle\Type\ListType\AbstractListType;
use Povs\ListerTwigBundle\Service\ConfigurationResolver;
use Povs\ListerTwigBundle\Service\ListRenderer;
use Povs\ListerBundle\View\ListView;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

/**
 * @author Povilas Margaiatis <p.margaitis@gmail.com>
 */
class TwigListType extends AbstractListType
{
    /**
     * @var Environment
     */
    protected $twig;

    /**
     * @var ListRenderer
     */
    protected $renderer;

    /**
     * @var ConfigurationResolver
     */
    private $configurationResolver;

    /**
     * TwigResponse constructor.
     *
     * @param Environment|null      $twig
     * @param ListRenderer          $renderer
     * @param ConfigurationResolver $configurationResolver
     */
    public function __construct(Environment $twig, ListRenderer $renderer, ConfigurationResolver $configurationResolver)
    {
        $this->twig = $twig;
        $this->renderer = $renderer;
        $this->configurationResolver = $configurationResolver;
    }

    /**
     * @inheritDoc
     */
    public function getCurrentPage(?int $currentPage): int
    {
        return $currentPage ?? 1;
    }

    /**
     * @inheritDoc
     */
    public function getLength(?int $length): int
    {
        $length = $length ?? $this->config['default_length'];

        if (!in_array($length, $this->config['length_options'], true)) {
            $length = $this->config['default_length'];
        }

        return $length;
    }

    /**
     * @inheritDoc
     */
    public function generateResponse(ListView $listView, array $options): Response
    {
        $response = new Response();
        $response->setContent($this->getView($listView, $options));

        return $response;
    }

    /**
     * @inheritDoc
     */
    public function generateData(ListView $listView, array $options): string
    {
        return $this->getView($listView, $options);
    }

    /**
     * @inheritDoc
     */
    public function configureSettings(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefined(['theme', 'form_theme', 'default_length', 'length_options', 'export_types', 'export_limit']);
        $optionsResolver->setAllowedTypes('export_types', 'string[]');
        $optionsResolver->setAllowedTypes('export_limit', 'array');
        $optionsResolver->setAllowedTypes('default_length', 'int');
        $optionsResolver->setAllowedTypes('length_options', 'int[]');
        $optionsResolver->setDefaults([
            'theme' => '@PovsListerTwig/default_theme.html.twig',
            'default_length' => 20,
            'length_options' => [20, 50, 100],
            'export_types' => [],
            'export_limit' => []
        ]);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefined(['template', 'context']);
        $optionsResolver->setAllowedTypes('template', 'string');
        $optionsResolver->setAllowedTypes('context', 'array');
        $optionsResolver->setRequired(['template']);
        $optionsResolver->setDefaults(['context' => []]);
    }

    /**
     * @param ListView $listView
     * @param array    $options
     *
     * @return string the view
     */
    protected function getView(ListView $listView, array $options): string
    {
        $this->renderer->setListTemplate($options['template']);
        $view = $this->twig->render($options['template'], $this->buildContext($listView, $options));

        return $view;
    }

    /**
     * @param ListView $listView
     * @param array    $options
     *
     * @return array
     */
    protected function buildContext(ListView $listView, array $options): array
    {
        return array_merge([
            'list' => $listView,
            'list_data' => [
                'theme' => $this->config['theme'],
                'form_theme' => $this->config['form_theme'] ?? null,
                'length_options' => $this->config['length_options'],
                'export_types' => $this->config['export_types'],
                'export_limit' => $this->config['export_limit'],
                'type_name' => $this->configurationResolver->getTypeName(),
                'ajax' => false
            ]
        ], $options['context'] ?? []);
    }
}