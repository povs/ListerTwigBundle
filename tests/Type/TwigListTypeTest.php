<?php
namespace Povs\ListerTwigBundle\Type;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Povs\ListerBundle\View\ListView;
use Povs\ListerTwigBundle\Service\ConfigurationResolver;
use Povs\ListerTwigBundle\Service\ListRenderer;
use Povs\ListerTwigBundle\Type\ListType\TwigListType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

/**
 * @author Povilas Margaiatis <p.margaitis@gmail.com>
 */
class TwigListTypeTest extends TestCase
{
    private $twigMock;
    private $rendererMock;
    private $configurationResolverMock;

    public function setUp()
    {
        $this->twigMock = $this->createMock(Environment::class);
        $this->rendererMock = $this->createMock(ListRenderer::class);
        $this->configurationResolverMock = $this->createMock(ConfigurationResolver::class);
    }

    /**
     * @dataProvider currentPageProvider
     * @param $passed
     * @param $expected
     */
    public function testGetCurrentPage($passed, $expected): void
    {
        $type = $this->getType();
        $this->assertEquals($expected, $type->getCurrentPage($passed));
    }

    public function currentPageProvider(): array
    {
        return [
            [5, 5],
            [null, 1]
        ];
    }

    /**
     * @dataProvider lengthProvider
     * @param $passed
     * @param $default
     * @param $options
     * @param $expected
     */
    public function testGetLength($passed, $default, $options, $expected): void
    {
        $config = [
            'default_length' => $default,
            'length_options' => $options
        ];

        $type = $this->getType($config);
        $this->assertEquals($expected, $type->getLength($passed));
    }

    public function lengthProvider(): array
    {
        return [
            [50, 20, [20, 50, 100], 50],
            [null, 20, [20, 50, 100], 20],
            [40, 20, [20, 50, 100], 20],
            [80, 30, [20, 50, 100], 30],
        ];
    }

    public function testGenerateResponse(): void
    {
        $listViewMock = $this->createMock(ListView::class);
        $options = [
            'template' => 'testTemplate',
            'context' => ['foo' => 'bar']
        ];
        $type = $this->getTypeView($listViewMock, 'testTemplate');
        $res = $type->generateResponse($listViewMock, $options);
        $this->assertEquals('viewContent', $res->getContent());
    }

    public function testGenerateData(): void
    {
        $listViewMock = $this->createMock(ListView::class);
        $options = [
            'template' => 'testTemplate',
            'context' => ['foo' => 'bar']
        ];
        $type = $this->getTypeView($listViewMock, 'testTemplate');
        $res = $type->generateData($listViewMock, $options);
        $this->assertEquals('viewContent', $res);
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
            'export_limit' => []
        ];

        $testCases = [
            ['theme' => 'test_theme', 'default_length' => 50],
            ['theme' => 'test_theme', 'export_types' => ['type1', 'type2'], 'export_limit' => ['type1' => 1000]],
        ];

        foreach ($testCases as $testCase) {
            yield [
                $testCase,
                array_merge($settings, $testCase)
            ];
        }
    }

    /**
     * @dataProvider configureOptionsProvider
     * @param array $passed
     * @param array $expected
     */
    public function testConfigureOptions(array $passed, array $expected): void
    {
        $resolver = new OptionsResolver();
        $type = $this->getType();
        $type->configureOptions($resolver);
        $result = $resolver->resolve($passed);
        $this->assertEquals($expected, $result);
    }

    public function configureOptionsProvider(): iterable
    {
        $options = [
            'context' => []
        ];

        $testCases = [
            ['template' => 'test_template', 'context' => ['foo' => 'bar']],
            ['template' => 'foobar'],
        ];

        foreach ($testCases as $testCase) {
            yield [
                $testCase,
                array_merge($options, $testCase)
            ];
        }
    }

    /**
     * @param MockObject $listViewMock
     * @param string     $template
     *
     * @return TwigListType
     */
    private function getTypeView(MockObject $listViewMock, string $template): TwigListType
    {
        $config = [
            'theme' => 'testTheme',
            'form_theme' => 'testFormTheme',
            'length_options' => [20, 50, 100],
            'export_types' => ['test_type'],
            'type_name' => 'type',
            'export_limit' => 1000
        ];
        $listData = $config;
        $listData['ajax'] = false;

        $expectedContext = [
            'list' => $listViewMock,
            'list_data' => $listData,
            'foo' => 'bar'
        ];

        $this->configurationResolverMock->expects($this->once())
            ->method('getTypeName')
            ->willReturn('type');
        $this->rendererMock->expects($this->once())
            ->method('setListTemplate')
            ->with($template);
        $this->twigMock->expects($this->once())
            ->method('render')
            ->with('testTemplate', $expectedContext)
            ->willReturn('viewContent');

        return $this->getType($config);
    }

    /**
     * @param array $config
     *
     * @return TwigListType
     */
    private function getType(array $config = []): TwigListType
    {
        $type =  new TwigListType($this->twigMock, $this->rendererMock, $this->configurationResolverMock);
        $type->setConfig($config);

        return $type;
    }
}