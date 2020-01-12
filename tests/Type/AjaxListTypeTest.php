<?php
namespace Povs\ListerTwigBundle\Type;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Povs\ListerBundle\Service\RequestHandler;
use Povs\ListerBundle\View\ListView;
use Povs\ListerTwigBundle\Service\ConfigurationResolver;
use Povs\ListerTwigBundle\Service\ListRenderer;
use Povs\ListerTwigBundle\Type\ListType\AjaxListType;
use Povs\ListerTwigBundle\Type\ListType\TwigListType;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

/**
 * @author Povilas Margaiatis <p.margaitis@gmail.com>
 */
class AjaxListTypeTest extends TestCase
{
    private $twigMock;
    private $rendererMock;
    private $configurationResolverMock;
    private $requestHandlerMock;

    public function setUp()
    {
        $this->twigMock = $this->createMock(Environment::class);
        $this->rendererMock = $this->createMock(ListRenderer::class);
        $this->requestHandlerMock = $this->createMock(RequestHandler::class);
        $this->configurationResolverMock = $this->createMock(ConfigurationResolver::class);
    }

    public function testGenerateResponseNotAjax(): void
    {
        $listViewMock = $this->createMock(ListView::class);
        $options = [
            'template' => 'test_template',
            'context' => ['foo' => 'bar']
        ];
        $type = $this->getTypeView($listViewMock, 'test_template', false);
        $res = $type->generateResponse($listViewMock, $options);
        $this->assertNotInstanceOf(JsonResponse::class, $res);
        $this->assertEquals('baseViewContent', $res->getContent());
        $this->assertEquals('ajax-request', $res->headers->get('Vary'));
    }

    public function testGenerateResponseAjax(): void
    {
        $listViewMock = $this->createMock(ListView::class);
        $options = [
            'template' => 'test_template',
            'context' => ['foo' => 'bar']
        ];
        $type = $this->getTypeView($listViewMock, 'test_template', true);
        $res = $type->generateResponse($listViewMock, $options);
        $this->assertInstanceOf(JsonResponse::class, $res);
        $this->assertEquals('"ajaxViewContent"', $res->getContent());
        $this->assertEquals('ajax-request', $res->headers->get('Vary'));
        $this->assertEquals('application/json', $res->headers->get('Content-Type'));
    }

    /**
     * @dataProvider configureSettingsProvider
     * @param array $passed
     * @param array $expected
     */
    public function testConfigureSettings(array $passed, array $expected): void
    {
        $resolver = new OptionsResolver();
        $type = $this->getType();
        $type->configureSettings($resolver);
        $result = $resolver->resolve($passed);
        $this->assertEquals($expected, $result);
    }

    public function configureSettingsProvider(): iterable
    {
        $settings = [
            'default_length' => 20,
            'length_options' => [20, 50, 100],
            'export_types' => [],
            'export_limit' => [],
            'block' => 'list_table',
            'allow_export' => false
        ];

        $testCases = [
            ['theme' => 'test_theme', 'block' => 'list_foo'],
            ['theme' => 'test_theme'],
        ];

        foreach ($testCases as $testCase) {
            yield [
                $testCase,
                array_merge($settings, $testCase)
            ];
        }
    }

    /**
     * @param MockObject $listViewMock
     * @param string     $template
     * @param bool       $ajaxRequest
     *
     * @return TwigListType
     */
    private function getTypeView(MockObject $listViewMock, string $template, bool $ajaxRequest): TwigListType
    {
        $requestMock = $this->createMock(Request::class);
        $headersBagMock = $this->createMock(HeaderBag::class);
        $requestMock->headers = $headersBagMock;
        $config = [
            'theme' => 'testTheme',
            'form_theme' => 'testFormTheme',
            'length_options' => [20, 50, 100],
            'export_types' => ['test_type'],
            'export_limit' => 1000,
            'type_name' => 'type',
            'block' => 'list_table',
            'allow_export' => true
        ];
        $listData = $config;
        $listData['ajax'] = true;
        unset($listData['block']);
        $expectedContext = [
            'list' => $listViewMock,
            'list_data' => $listData,
            'foo' => 'bar'
        ];

        $this->configurationResolverMock->expects($this->once())
            ->method('getTypeName')
            ->willReturn('type');
        $this->requestHandlerMock->expects($this->exactly(2))
            ->method('getRequest')
            ->willReturn($requestMock);
        $headersBagMock->expects($this->exactly(2))
            ->method('has')
            ->with('ajax-request')
            ->willReturn($ajaxRequest);
        $this->rendererMock->expects($this->once())
            ->method('setListTemplate')
            ->with($template);

        if ($ajaxRequest) {
            $this->rendererMock->expects($this->once())
                ->method('render')
                ->with($listViewMock, 'list_table', $expectedContext, false)
                ->willReturn('ajaxViewContent');
        } else {
            $this->twigMock->expects($this->once())
                ->method('render')
                ->with($template, $expectedContext)
                ->willReturn('baseViewContent');
        }

        return $this->getType($config);
    }

    /**
     * @param array $config
     *
     * @return AjaxListType
     */
    private function getType(array $config = []): AjaxListType
    {
        $type =  new AjaxListType(
            $this->twigMock,
            $this->rendererMock,
            $this->configurationResolverMock,
            $this->requestHandlerMock
        );
        $type->setConfig($config);

        return $type;
    }
}