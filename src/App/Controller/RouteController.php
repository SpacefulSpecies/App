<?php

namespace Species\App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Slim\Http\Stream;
use Slim\Interfaces\RouterInterface;

/**
 * Abstract route controller.
 */
abstract class RouteController
{

    /** @var RouterInterface */
    private $router;



    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }



    /**
     * @param Response $response
     * @param string   $location
     * @param int      $status
     * @return Response
     */
    final protected function redirect(Response $response, string $location, int $status): Response
    {
        return $response
            ->withStatus($status)
            ->withHeader('Location', $location);
    }

    /**
     * @param Response $response
     * @param string   $location
     * @return Response
     */
    final protected function permanentRedirect(Response $response, string $location): Response
    {
        return $this->redirect($response, $location, 301);
    }

    /**
     * @param Response $response
     * @param string   $location
     * @return Response
     */
    final protected function temporaryRedirect(Response $response, string $location): Response
    {
        return $this->redirect($response, $location, 302);
    }



    /**
     * @param string $name
     * @param array  $arguments       = []
     * @param array  $queryParameters = []
     * @return string
     */
    final protected function pathFor(string $name, array $arguments = [], array $queryParameters = []): string
    {
        return $this->router->pathFor($name, $arguments, $queryParameters);
    }



    /**
     * @param Response $response
     * @param mixed    $data
     * @return Response
     */
    final protected function json(Response $response, $data): Response
    {
        $body = new Stream(fopen('php://temp', 'r+'));
        $body->write(json_encode($data, JSON_THROW_ON_ERROR));

        return $response
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withBody($body);
    }

}
