<?php

namespace Species\App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Twig\Extension\DebugExtension;

/**
 * Twig debug middleware.
 */
final class TwigDebugMiddleware
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
        if ($this->twig->getEnvironment()->isDebug()) {
            $this->twig->addExtension(new DebugExtension());
        }

        return $next($request, $response);
    }

}
