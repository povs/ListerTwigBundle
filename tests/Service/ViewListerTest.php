<?php

namespace Povs\ListerTwigBundle\Service;

use PHPUnit\Framework\TestCase;
use Povs\ListerBundle\Service\ListManager;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Povilas Margaiatis <p.margaitis@gmail.com>
 */
class ViewListerTest extends TestCase
{
    private $listManagerMock;
    private $typeResolverMock;

    public function setUp(): void
    {
        $this->listManagerMock = $this->createMock(ListManager::class);
        $this->typeResolverMock = $this->createMock(TypeResolver::class);
    }

    public function testGenerateView(): void
    {
        $responseMock = $this->createMock(Response::class);
        $this->listManagerMock->expects($this->once())
            ->method('getResponse')
            ->with([
                'template' => 'tempName',
                'context' => [
                    'foo' => 'bar'
                ]
            ])->willReturn($responseMock);
        $this->typeResolverMock->expects($this->once())
            ->method('resolveType')
            ->willReturn('type');
        $this->typeResolverMock->expects($this->once())
            ->method('isViewType')
            ->willReturn(true);

        $res = $this->getViewLister()->buildList('list', null, [])
            ->generateView('tempName', ['foo' => 'bar']);
        $this->assertEquals($responseMock, $res);
    }

    public function testGenerateViewWithOptions(): void
    {
        $responseMock = $this->createMock(Response::class);
        $this->listManagerMock->expects($this->once())
            ->method('getResponse')
            ->with(['foo' => 'bar'])
            ->willReturn($responseMock);
        $res = $this->getViewLister()
            ->setOptions('type', ['foo' => 'bar'])
            ->buildList('list', 'type')
            ->generateView('test');
        $this->assertEquals($responseMock, $res);
    }

    public function testGenerateViewWithoutType(): void
    {
        $responseMock = $this->createMock(Response::class);
        $this->listManagerMock->expects($this->once())
            ->method('getResponse')
            ->with([])
            ->willReturn($responseMock);
        $res = $this->getViewLister()
            ->generateView('test');
        $this->assertEquals($responseMock, $res);
    }

    public function testGenerateViewWithoutOptions(): void
    {
        $responseMock = $this->createMock(Response::class);
        $this->listManagerMock->expects($this->once())
            ->method('getResponse')
            ->with([])
            ->willReturn($responseMock);
        $res = $this->getViewLister()
            ->buildList('list', 'type')
            ->generateView('test');
        $this->assertEquals($responseMock, $res);
    }

    public function testGetView(): void
    {
        $this->listManagerMock->expects($this->once())
            ->method('getData')
            ->with([
                'template' => 'tempName',
                'context' => [
                    'foo' => 'bar'
                ]
            ])->willReturn('view');
        $this->typeResolverMock->expects($this->once())
            ->method('resolveType')
            ->willReturn('type');
        $this->typeResolverMock->expects($this->once())
            ->method('isViewType')
            ->willReturn(true);

        $res = $this->getViewLister()
            ->buildList('list')
            ->getView('tempName', ['foo' => 'bar']);
        $this->assertEquals('view', $res);
    }

    private function getViewLister(): ViewLister
    {
        return new ViewLister($this->listManagerMock, $this->typeResolverMock);
    }
}
