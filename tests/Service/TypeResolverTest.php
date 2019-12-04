<?php
namespace Povs\ListerTwigBundle\Service;

use PHPUnit\Framework\TestCase;
use Povs\ListerBundle\Service\RequestHandler;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Povilas Margaiatis <p.margaitis@gmail.com>
 */
class TypeResolverTest extends TestCase
{
    private $requestHandler;
    private $configurationResolver;

    public function setUp()
    {
        $this->requestHandler = $this->createMock(RequestHandler::class);
        $this->configurationResolver = $this->createMock(ConfigurationResolver::class);
    }

    public function testResolveTypeWithoutRequestType(): void
    {
        $requestMock = $this->createMock(Request::class);
        $parametersMock = $this->createMock(ParameterBag::class);
        $requestMock->request = $parametersMock;
        $this->requestHandler->expects($this->once())
            ->method('getRequest')
            ->willReturn($requestMock);
        $parametersMock->expects($this->once())
            ->method('get')
            ->with('type_name')
            ->willReturn(null);
        $this->configurationResolver->expects($this->once())
            ->method('getTypeName')
            ->willReturn('type_name');
        $this->configurationResolver->expects($this->once())
            ->method('getDefaultType')
            ->willReturn('default_type');

        $this->assertEquals('default_type', $this->getTypeResolver()->resolveType());
    }

    public function testResolveTypeWithRequestType(): void
    {
        $requestMock = $this->createMock(Request::class);
        $parametersMock = $this->createMock(ParameterBag::class);
        $requestMock->request = $parametersMock;
        $this->requestHandler->expects($this->once())
            ->method('getRequest')
            ->willReturn($requestMock);
        $parametersMock->expects($this->once())
            ->method('get')
            ->with('type_name')
            ->willReturn('request_type');
        $this->configurationResolver->expects($this->once())
            ->method('getTypeName')
            ->willReturn('type_name');

        $this->assertEquals('request_type', $this->getTypeResolver()->resolveType());
    }

    /**
     * @dataProvider isViewTypeProvider
     *
     * @param string $passed
     * @param array  $returned
     * @param bool   $expected
     */
    public function testIsViewType(string $passed, array $returned, bool $expected): void
    {
        $this->configurationResolver->expects($this->once())
            ->method('getViewTypes')
            ->willReturn($returned);

        $this->assertEquals($expected, $this->getTypeResolver()->isViewType($passed));
    }

    public function isViewTypeProvider(): array
    {
        return [
            ['foo', ['bar', 'foo'], true],
            ['type', [], false],
            ['1', [1, 2], false]
        ];
    }

    private function getTypeResolver(): TypeResolver
    {
        return new TypeResolver($this->requestHandler, $this->configurationResolver);
    }
}