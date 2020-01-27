<?php

namespace Povs\ListerTwigBundle\Type\ListType;

use Povs\ListerBundle\Service\RequestHandler;
use Povs\ListerTwigBundle\Service\ConfigurationResolver;
use Povs\ListerTwigBundle\Service\ListRenderer;
use Povs\ListerBundle\View\ListView;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

/**
 * @author Povilas Margaiatis <p.margaitis@gmail.com>
 */
class AjaxListType extends TwigListType
{
    private const HEADER_AJAX_REQUEST = 'ajax-request';

    /**
     * @var RequestHandler
     */
    private $requestHandler;

    /**
     * AjaxListType constructor.
     *
     * @param Environment           $twig
     * @param ListRenderer          $renderer
     * @param ConfigurationResolver $configurationResolver
     * @param RequestHandler        $requestHandler
     */
    public function __construct(
        Environment $twig,
        ListRenderer $renderer,
        ConfigurationResolver $configurationResolver,
        RequestHandler $requestHandler
    ) {
        parent::__construct($twig, $renderer, $configurationResolver);
        $this->requestHandler = $requestHandler;
    }

    /**
     * @inheritDoc
     */
    public function generateResponse(ListView $listView, array $options): Response
    {
        $view = $this->getView($listView, $options);
        $params = [$view, 200, ['Vary' => self::HEADER_AJAX_REQUEST]];

        return $this->isAjaxRequest() ? new JsonResponse(...$params) : new Response(...$params);
    }

    /**
     * @inheritDoc
     */
    public function configureSettings(OptionsResolver $optionsResolver): void
    {
        parent::configureSettings($optionsResolver);
        $optionsResolver->setDefined(['block']);
        $optionsResolver->setAllowedTypes('block', 'string');
        $optionsResolver->setDefault('block', 'list_table');
    }

    /**
     * @param ListView $listView
     * @param array    $options
     *
     * @return string
     */
    protected function getView(ListView $listView, array $options): string
    {
        $context = $this->buildContext($listView, $options);
        $context['list_data']['ajax'] = true;
        $this->renderer->setListTemplate($options['template']);

        if ($this->isAjaxRequest()) {
            $view = $this->renderer->render(
                $listView,
                $this->config['block'],
                $context,
                false
            );
        } else {
            $view = $this->twig->render($options['template'], $context);
        }

        return $view;
    }

    /**
     * @return bool
     */
    protected function isAjaxRequest(): bool
    {
        return $this->requestHandler->getRequest()->headers->has(self::HEADER_AJAX_REQUEST);
    }
}
