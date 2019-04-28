<?php

namespace Species\App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Interfaces\RouteInterface;
use Slim\Interfaces\RouterInterface;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;

/**
 * Twig router middleware.
 */
final class TwigRouterMiddleware
{

    /** @var Twig */
    private $twig;

    /** @var RouterInterface */
    private $router;

    /** @var string */
    private $baseUrl;



    /**
     * @param Twig            $twig
     * @param RouterInterface $router
     * @param string          $baseUrl
     */
    public function __construct(Twig $twig, RouterInterface $router, string $baseUrl)
    {
        $this->twig = $twig;
        $this->router = $router;
        $this->baseUrl = $baseUrl;
    }



    /**
     * @param Request  $request
     * @param Response $response
     * @param callable $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        $this->twig->getEnvironment()->addGlobal('route_name', $this->getRouteName($request));
        $this->twig->addExtension(new TwigExtension($this->router, $this->baseUrl));

        return $next($request, $response);
    }



    /**
     * @param Request $request
     * @return string|null
     */
    private function getRouteName(Request $request): ?string
    {
        $route = $request->getAttribute('route');

        return $route instanceof RouteInterface ? $route->getName() : null;
    }

}
