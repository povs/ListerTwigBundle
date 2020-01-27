<?php

namespace Povs\ListerTwigBundle\Twig\Extension;

use PHPUnit\Framework\TestCase;
use Povs\ListerBundle\View\ViewInterface;
use Povs\ListerTwigBundle\Service\ListRenderer;
use Twig\Node\Node;
use Twig\TwigFunction;

/**
 * @author Povilas Margaiatis <p.margaitis@gmail.com>
 */
class ListExtensionTest extends TestCase
{
    private $renderer;

    public function setUp()
    {
        $this->renderer = $this->createMock(ListRenderer::class);
    }

    public function testGetFunctions(): void
    {
        $functions = $this->getExtension()->getFunctions();
        $nodeMock = $this->createMock(Node::class);
        $this->assertCount(1, $functions);
        /** @var TwigFunction $func */
        $func = $functions[0];
        $this->assertEquals('list_view', $func->getName());
        $this->assertEquals(['html'], $func->getSafe($nodeMock));
        $this->assertTrue($func->needsContext());
    }

    public function testListView(): void
    {
        $viewMock = $this->createMock(ViewInterface::class);
        $this->renderer->expects($this->once())
            ->method('render')
            ->with($viewMock, 'foo', ['foo' => 'bar'], true)
            ->willReturn('block');

        $res = $this->getExtension()->listView(['foo' => 'bar'], $viewMock, 'foo', true);
        $this->assertEquals('block', $res);
    }

    private function getExtension(): ListExtension
    {
        return new ListExtension($this->renderer);
    }
}
