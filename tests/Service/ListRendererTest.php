<?php
namespace Povs\ListerTwigBundle\Service;

use PHPUnit\Framework\TestCase;
use Povs\ListerBundle\View\FieldView;
use Povs\ListerBundle\View\ViewInterface;
use Twig\Environment;
use Twig\Template;

/**
 * @author Povilas Margaiatis <p.margaitis@gmail.com>
 */
class ListRendererTest extends TestCase
{
    private const CONTEXT = [
        'list_data' => [
            'theme' => 'themeTemplate'
        ],
        'foo' => 'bar'
    ];

    private $twigMock;
    private $themeTemplateMock;
    private $listTemplateMock;
    private $viewMock;

    public function setUp()
    {
        $this->twigMock = $this->createMock(Environment::class);
        $this->themeTemplateMock = $this->createMock(Template::class);
        $this->listTemplateMock = $this->createMock(Template::class);
        $this->viewMock = $this->createMock(ViewInterface::class);
    }

    public function testRenderWithListTemplate(): void
    {
        $this->twigMock->expects($this->exactly(2))
            ->method('load')
            ->willReturnMap([
                ['themeTemplate', $this->themeTemplateMock],
                ['listTemplate', $this->listTemplateMock]
            ]);
        $this->listTemplateMock->expects($this->once())
            ->method('hasBlock')
            ->with('foo', [])
            ->willReturn(true);
        $this->listTemplateMock->expects($this->once())
            ->method('renderBlock')
            ->with('foo', array_merge(self::CONTEXT, ['list' => $this->viewMock]))
            ->willReturn('renderedBlock');

        $renderer = $this->getRenderer();
        $renderer->setListTemplate('listTemplate');
        $res = $renderer->render($this->viewMock, 'foo', self::CONTEXT, true);
        $this->assertEquals('renderedBlock', $res);
    }

    public function testRenderWithoutListTemplate(): void
    {
        $this->twigMock->expects($this->once())
            ->method('load')
            ->with('themeTemplate')
            ->willReturn($this->themeTemplateMock);
        $this->themeTemplateMock->expects($this->once())
            ->method('renderBlock')
            ->with('foo', array_merge(self::CONTEXT, ['list' => $this->viewMock]))
            ->willReturn('renderedBlock');

        $renderer = $this->getRenderer();
        $res = $renderer->render($this->viewMock, 'foo', self::CONTEXT, true);
        $this->assertEquals('renderedBlock', $res);
    }

    public function testRenderBlockNameModifications(): void
    {
        $viewMock = $this->createMock(FieldView::class);
        $viewMock->expects($this->once())
            ->method('getId')
            ->willReturn('id');

        $this->twigMock->expects($this->exactly(2))
            ->method('load')
            ->willReturnMap([
                ['themeTemplate', $this->themeTemplateMock],
                ['listTemplate', $this->listTemplateMock]
            ]);
        $this->listTemplateMock->expects($this->once())
            ->method('hasBlock')
            ->with('foo_id', [])
            ->willReturn(true);

        $this->listTemplateMock->expects($this->once())
            ->method('renderBlock')
            ->with('foo_id', array_merge(self::CONTEXT, ['list' => $viewMock]))
            ->willReturn('renderedBlock');

        $renderer = $this->getRenderer();
        $renderer->setListTemplate('listTemplate');
        $res = $renderer->render($viewMock, 'foo', self::CONTEXT, true);
        $this->assertEquals('renderedBlock', $res);
    }

    public function testRenderBlockNameNotFound(): void
    {
        $this->twigMock->expects($this->exactly(2))
            ->method('load')
            ->willReturnMap([
                ['themeTemplate', $this->themeTemplateMock],
                ['listTemplate', $this->listTemplateMock]
            ]);
        $this->listTemplateMock->expects($this->once())
            ->method('hasBlock')
            ->with('foo', [])
            ->willReturn(false);

        $this->themeTemplateMock->expects($this->once())
            ->method('renderBlock')
            ->with('foo', array_merge(self::CONTEXT, ['list' => $this->viewMock]))
            ->willReturn('renderedBlock');

        $renderer = $this->getRenderer();
        $renderer->setListTemplate('listTemplate');
        $res = $renderer->render($this->viewMock, 'foo', self::CONTEXT, true);
        $this->assertEquals('renderedBlock', $res);
    }

    public function testRenderTemplatesNotLoadedTwice(): void
    {
        $this->twigMock->expects($this->once())
            ->method('load')
            ->with('themeTemplate')
            ->willReturn($this->themeTemplateMock);
        $this->themeTemplateMock->expects($this->exactly(2))
            ->method('renderBlock')
            ->with('foo', array_merge(self::CONTEXT, ['list' => $this->viewMock]))
            ->willReturn('renderedBlock');

        $renderer = $this->getRenderer();
        $renderer->render($this->viewMock, 'foo', self::CONTEXT, true);
        $renderer->render($this->viewMock, 'foo', self::CONTEXT, true);
    }

    private function getRenderer(): ListRenderer
    {
        return new ListRenderer($this->twigMock);
    }
}