<?php

namespace Species\App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Interfaces\RouteInterface;
use Slim\Interfaces\RouterInterface;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Twig\Extension\DebugExtension;

/**
 * Twig helpers middleware.
 */
final class TwigHelpersMiddleware
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
        // debug extension
        if ($this->twig->getEnvironment()->isDebug()) {
            $this->twig->addExtension(new DebugExtension());
        }

        // route helpers
        $this->twig->addExtension(new TwigExtension($this->router, $this->baseUrl));
        $this->addRouteNameToGlobals($request);

        return $next($request, $response);
    }



    /**
     * @param Request $request
     */
    private function addRouteNameToGlobals(Request $request): void
    {
        $route = $request->getAttribute('route');
        $routeName = $route instanceof RouteInterface ? $route->getName() : '';

        $this->twig->getEnvironment()->addGlobal('route_name', $routeName);
    }

}
