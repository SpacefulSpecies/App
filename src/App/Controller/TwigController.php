<?php

namespace Species\App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Slim\Interfaces\RouterInterface;
use Slim\Views\Twig;

/**
 * Abstract twig controller.
 */
abstract class TwigController extends RouteController
{

    /** @var Twig */
    private $twig;



    /**
     * @param RouterInterface $router
     * @param Twig            $twig
     */
    public function __construct(RouterInterface $router, Twig $twig)
    {
        parent::__construct($router);
        $this->twig = $twig;
    }



    /**
     * @param Response $response
     * @param string   $template
     * @param array    $data = []
     * @return Response
     */
    final protected function twig(Response $response, string $template, array $data = []): Response
    {
        // no idea why twig capitalize charset... just revert to lower case.
        $charset = strtolower($this->twig->getEnvironment()->getCharset());

        return $this->twig->render($response, $template, $data)
            ->withHeader('Content-Type', "text/html; charset=$charset");
    }

}
