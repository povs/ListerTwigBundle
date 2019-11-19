<?php
namespace Povs\ListerTwigBundle\Service;

use PHPUnit\Framework\TestCase;
use Povs\ListerBundle\Service\ListManager;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Povilas Margaiatis <p.margaitis@gmail.com>
 */
class TwigListerTest extends TestCase
{
    private $listManagerMock;

    public function setUp()
    {
        $this->listManagerMock = $this->createMock(ListManager::class);
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

        $res = $this->getTwigLister()->generateView('tempName', ['foo' => 'bar']);
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

        $res = $this->getTwigLister()->getView('tempName', ['foo' => 'bar']);
        $this->assertEquals('view', $res);
    }

    private function getTwigLister(): ViewLister
    {
        return new ViewLister($this->listManagerMock);
    }
}