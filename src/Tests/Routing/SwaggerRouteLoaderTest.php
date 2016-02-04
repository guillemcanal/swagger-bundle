<?php
/*
 * This file is part of the KleijnWeb\SwaggerBundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KleijnWeb\SwaggerBundle\Tests\Dev\Routing;

use KleijnWeb\SwaggerBundle\Routing\SwaggerRouteLoader;
use Symfony\Component\Routing\Route;

/**
 * @author John Kleijn <john@kleijnweb.nl>
 */
class SwaggerRouteLoaderTest extends \PHPUnit_Framework_TestCase
{
    const DOCUMENT_PATH = '/totally/non-existent/path';

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $repositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $documentMock;

    /**
     * @var SwaggerRouteLoader
     */
    private $loader;

    /**
     * Create mocks
     */
    protected function setUp()
    {
        $this->documentMock = $this
            ->getMockBuilder('KleijnWeb\SwaggerBundle\Document\SwaggerDocument')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repositoryMock = $this
            ->getMockBuilder('KleijnWeb\SwaggerBundle\Document\DocumentRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repositoryMock
            ->expects($this->any())
            ->method('get')
            ->willReturn($this->documentMock);

        $this->loader = new SwaggerRouteLoader($this->repositoryMock);
    }

    /**
     * @test
     */
    public function supportSwaggerAsRouteTypeOnly()
    {
        $this->assertFalse($this->loader->supports('/a/b/c'));
        $this->assertTrue($this->loader->supports('/a/b/c', 'swagger'));
    }

    /**
     * @test
     */
    public function canLoadMultipleDocuments()
    {
        $this->documentMock
            ->expects($this->any())
            ->method('getPathDefinitions')
            ->willReturn([]);

        $this->loader->load(self::DOCUMENT_PATH);
        $this->loader->load(self::DOCUMENT_PATH . '2');
    }

    /**
     * @test
     */
    public function loadingMultipleDocumentWillPreventRouteKeyCollisions()
    {
        $pathDefinitions = [
            '/a'     => ['get' => []],
            '/a/b'   => ['get' => [], 'post' => []],
            '/a/b/c' => ['put' => []],
        ];

        $this->documentMock
            ->expects($this->exactly(2))
            ->method('getPathDefinitions')
            ->willReturn($pathDefinitions);

        $routes1 = $this->loader->load(self::DOCUMENT_PATH);
        $routes2 = $this->loader->load(self::DOCUMENT_PATH . '2');
        $this->assertSame(count($routes1), count(array_diff_key($routes1->all(), $routes2->all())));
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function cannotTryToLoadSameDocumentMoreThanOnce()
    {
        $this->documentMock
            ->expects($this->any())
            ->method('getPathDefinitions')
            ->willReturn([]);

        $this->loader->load(self::DOCUMENT_PATH);
        $this->loader->load(self::DOCUMENT_PATH);
    }

    /**
     * @test
     */
    public function willReturnRouteCollection()
    {
        $this->documentMock
            ->expects($this->once())
            ->method('getPathDefinitions')
            ->willReturn([]);

        $routes = $this->loader->load(self::DOCUMENT_PATH);
        $this->assertInstanceOf('Symfony\Component\Routing\RouteCollection', $routes);
    }

    /**
     * @test
     */
    public function routeCollectionWillContainOneRouteForEveryPathAndMethod()
    {
        $pathDefinitions = [
            '/a' => ['get' => [], 'post' => []],
            '/b' => ['get' => []],
        ];

        $this->documentMock
            ->expects($this->once())
            ->method('getPathDefinitions')
            ->willReturn($pathDefinitions);

        $routes = $this->loader->load(self::DOCUMENT_PATH);

        $this->assertCount(3, $routes);
    }

    /**
     * @test
     */
    public function routeCollectionWillIncludeSeparateRoutesForSubPaths()
    {
        $pathDefinitions = [
            '/a'     => ['get' => []],
            '/a/b'   => ['get' => []],
            '/a/b/c' => ['get' => []],
        ];

        $this->documentMock
            ->expects($this->once())
            ->method('getPathDefinitions')
            ->willReturn($pathDefinitions);

        $routes = $this->loader->load(self::DOCUMENT_PATH);

        $this->assertCount(3, $routes);
    }

    /**
     * @test
     */
    public function canUseDiKeyAsOperationId()
    {
        $expected = 'my.controller.key:methodName';
        $pathDefinitions = [
            '/a' => [
                'get'  => [],
                'post' => [
                    'operationId' => $expected
                ]
            ],
            '/b' => ['get' => []],
        ];

        $this->documentMock
            ->expects($this->any())
            ->method('getPathDefinitions')
            ->willReturn($pathDefinitions);

        $routes = $this->loader->load(self::DOCUMENT_PATH);

        $actual = $routes->get('swagger.path.a.my.controller.key:methodName');
        $this->assertNotNull($actual);
        $this->assertSame($expected, $actual->getDefault('_controller'));
    }

    /**
     * @test
     */
    public function routeCollectionWillIncludeSeparateRoutesForSubPathMethodCombinations()
    {
        $pathDefinitions = [
            '/a'     => ['get' => []],
            '/a/b'   => ['get' => [], 'post' => []],
            '/a/b/c' => ['put' => []],
        ];

        $this->documentMock
            ->expects($this->once())
            ->method('getPathDefinitions')
            ->willReturn($pathDefinitions);

        $routes = $this->loader->load(self::DOCUMENT_PATH);

        $this->assertCount(4, $routes);
    }

    /**
     * @test
     */
    public function routeCollectionWillContainPathFromSwaggerDoc()
    {
        $pathDefinitions = [
            '/a'                => ['get' => []],
            '/a/b'              => ['get' => []],
            '/a/b/c'            => ['get' => []],
            '/d/f/g'            => ['get' => []],
            '/1/2/3'            => ['get' => []],
            '/foo/{bar}/{blah}' => ['get' => []],
            '/z'                => ['get' => []],
        ];

        $this->documentMock
            ->expects($this->once())
            ->method('getPathDefinitions')
            ->willReturn($pathDefinitions);

        $routes = $this->loader->load(self::DOCUMENT_PATH);

        $definitionPaths = array_keys($pathDefinitions);
        sort($definitionPaths);
        $routePaths = array_map(function ($route) {
            return $route->getPath();
        }, $routes->getIterator()->getArrayCopy());
        sort($routePaths);
        $this->assertSame($definitionPaths, $routePaths);
    }

    /**
     * @test
     */
    public function willAddRequirementsForIntegerPathParams()
    {
        $pathDefinitions = [
            '/a' => [
                'get' => [
                    'parameters' => [
                        ['name' => 'foo', 'in' => 'path', 'type' => 'integer']
                    ]
                ]
            ],
        ];

        $this->documentMock
            ->expects($this->once())
            ->method('getPathDefinitions')
            ->willReturn($pathDefinitions);

        $this->documentMock
            ->expects($this->once())
            ->method('getOperationDefinition')
            ->with('/a', 'get')
            ->willReturn($pathDefinitions['/a']['get']);

        $routes = $this->loader->load(self::DOCUMENT_PATH);
        $actual = $routes->get('swagger.path.a.get');
        $this->assertNotNull($actual);
        $requirements = $actual->getRequirements();
        $this->assertNotNull($requirements);

        $this->assertSame($requirements['foo'], '\d+');
    }

    /**
     * @test
     */
    public function willAddRequirementsForStringPatternParams()
    {
        $expected = '\d{2}hello';
        $pathDefinitions = [
            '/a' => [
                'get' => [
                    'parameters' => [
                        ['name' => 'aString', 'in' => 'path', 'type' => 'string', 'pattern' => $expected]
                    ]
                ]
            ],
        ];

        $this->documentMock
            ->expects($this->once())
            ->method('getPathDefinitions')
            ->willReturn($pathDefinitions);

        $this->documentMock
            ->expects($this->once())
            ->method('getOperationDefinition')
            ->with('/a', 'get')
            ->willReturn($pathDefinitions['/a']['get']);

        $routes = $this->loader->load(self::DOCUMENT_PATH);
        $actual = $routes->get('swagger.path.a.get');
        $this->assertNotNull($actual);
        $requirements = $actual->getRequirements();
        $this->assertNotNull($requirements);

        $this->assertSame($expected, $requirements['aString']);
    }

    /**
     * @test
     */
    public function willAddRequirementsForStringEnumParams()
    {
        $enum = ['a', 'b', 'c'];
        $expected = '(a|b|c)';
        $pathDefinitions = [
            '/a' => [
                'get' => [
                    'parameters' => [
                        ['name' => 'aString', 'in' => 'path', 'type' => 'string', 'enum' => $enum]
                    ]
                ]
            ],
        ];

        $this->documentMock
            ->expects($this->once())
            ->method('getPathDefinitions')
            ->willReturn($pathDefinitions);

        $this->documentMock
            ->expects($this->once())
            ->method('getOperationDefinition')
            ->with('/a', 'get')
            ->willReturn($pathDefinitions['/a']['get']);

        $routes = $this->loader->load(self::DOCUMENT_PATH);
        $actual = $routes->get('swagger.path.a.get');
        $this->assertNotNull($actual);
        $requirements = $actual->getRequirements();
        $this->assertNotNull($requirements);

        $this->assertSame($expected, $requirements['aString']);
    }
}