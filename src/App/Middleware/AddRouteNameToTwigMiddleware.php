<?php

namespace Species\App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Interfaces\RouteInterface;
use Slim\Views\Twig;

/**
 * Add route name to twig middleware.
 */
final class AddRouteNameToTwigMiddleware
{

    /** @var Twig */
    private $twig;



    /**
     * @param Twig $twig
     */
    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }



    /**
     * @param Request  $request
     * @param Response $response
     * @param callable $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        $route = $request->getAttribute('route');
        $routeName = $route instanceof RouteInterface ? $route->getName() : null;

        $this->twig->getEnvironment()->addGlobal('routeName', $routeName);

        return $next($request, $response);
    }

}
