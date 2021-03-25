<?php declare(strict_types = 1);

namespace Zfegg\MezzioHelper\JsonSchema\Middleware;

use JsonSchema\Constraints\Factory;
use JsonSchema\Validator;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ContentValidation implements MiddlewareInterface
{
    private Factory $factory;

    /** @var callable */
    protected $invalidHandler;

    private ResponseFactoryInterface $responseFactory;

    public function __construct(
        Factory $factory,
        ResponseFactoryInterface $responseFactory,
        ?callable $invalidHandler = null
    ) {
        $this->factory = $factory;
        $this->invalidHandler = $invalidHandler ?: $this->getDefaultInvalidHandler();
        $this->responseFactory = $responseFactory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = $request->getAttribute(RouteResult::class);
        $options = $route->getMatchedRoute()->getOptions();

        if (isset($options['json-schema'])) {
            $schema = $options['json-schema'];
        } elseif (isset($options['json-schema:' . $request->getMethod()])) {
            $schema = $options['json-schema:' . $request->getMethod()];
        }

        if (! isset($schema)) {
            return $handler->handle($request);
        }

        if ($request->getMethod() == 'GET') {
            $data = $request->getQueryParams();
        } elseif (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'])) {
            $data = $request->getParsedBody();
        } else {
            return $handler->handle($request);
        }

        $validator = new Validator($this->factory);
        $validator->validate($data, $schema);

        if ($validator->isValid()) {
            $request = $request->withParsedBody($data);
            return $handler->handle($request);
        }

        return ($this->invalidHandler)(
            $validator,
            $request,
            $handler
        );
    }


    private function getDefaultInvalidHandler(): callable
    {
        return function (
            Validator $validator,
            ServerRequestInterface $request,
            RequestHandlerInterface $handler
        ) {
            $response = $this->responseFactory->createResponse(422);
            $response = $response->withHeader(
                'Content-Type',
                'application/json'
            );
            $response->getBody()->write(
                json_encode(
                    [
                        'status' => 422,
                        'detail' => 'Failed Validation',
                        'errors' => $validator->getErrors(),
                    ]
                )
            );

            return $response;
        };
    }
}
