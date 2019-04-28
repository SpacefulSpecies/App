<?php

namespace Species\App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Interfaces\RouteInterface;
use Slim\Interfaces\RouterInterface;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Species\App\TwigExtension\ReflectionTwigExtension;
use Twig\Extension\DebugExtension;
use Twig\Extension\ExtensionInterface;

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
            $this->addExtension(new DebugExtension());
        }

        // route helpers
        $this->addExtension(new TwigExtension($this->router, $this->baseUrl));
        $this->addExtension(new ReflectionTwigExtension());
        $this->addGlobal('route_name', $this->getRouteName($request));

        return $next($request, $response);
    }



    /**
     * @param ExtensionInterface $extension
     */
    private function addExtension(ExtensionInterface $extension): void
    {
        $this->twig->addExtension($extension);
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    private function addGlobal(string $name, $value): void
    {
        $this->twig->getEnvironment()->addGlobal($name, $value);
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
