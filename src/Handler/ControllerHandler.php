<?php declare(strict_types = 1);

namespace Zfegg\MezzioHelper\Handler;

use Mezzio\Handler\NotFoundHandler;
use Mezzio\Router\RouteResult;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ControllerHandler implements RequestHandlerInterface
{
    private ContainerInterface $container;
    private NotFoundHandler $notFoundHandler;

    public function __construct(ContainerInterface $container, NotFoundHandler $notFoundHandler)
    {
        $this->container = $container;
        $this->notFoundHandler = $notFoundHandler;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $route = $request->getAttribute(RouteResult::class);
        $action = $request->getAttribute('action');

        $ctrl = $route->getMatchedRoute()->getOptions()['controller'];
        if (method_exists($ctrl, $action)) {
            return $this->container->get("$ctrl@$action")->handler($request);
        }

        return $this->notFoundHandler->handle($request);
    }
}
