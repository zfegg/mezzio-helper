<?php declare(strict_types = 1);

namespace ZfeggTest\MezzioHelper\Middleware;

use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Mezzio\Router\Route;
use Mezzio\Router\RouteResult;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zfegg\MezzioHelper\JsonSchema\Factory\ValidatorFactoryFactory;
use Zfegg\MezzioHelper\JsonSchema\Middleware\ContentValidation;

class ContentValidationTest extends TestCase
{

    public function testProcess(): void
    {
        $request = (new ServerRequestFactory)->createServerRequest('POST', '/');
        $request = $request->withParsedBody([
            'email' => 'test@test.com',
            'password' => '123123',
            'status' => '2',
            'admin' => '0',
            'test' => '1',
            'roles' => ['1', 2, '3'],
            'real_name' => '123123',
        ]);
        $route = $this->createMock(Route::class);
        $route->method('getOptions')
            ->willReturn(['json-schema' => ['$ref' => 'file://' . __DIR__ . '/../users.create.json']]);
        $request = $request->withAttribute(RouteResult::class, RouteResult::fromRoute($route));

        $factory = (new ValidatorFactoryFactory())();

        $middleware = new ContentValidation($factory, new ResponseFactory());

        $handler = new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new EmptyResponse();
            }
        };

        $response = $middleware->process($request, $handler);

        $this->assertEquals(204, $response->getStatusCode());
    }
}
