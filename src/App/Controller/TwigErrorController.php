<?php

namespace Species\App\Controller;

use Psr\Http\Message\{ResponseInterface as Response, ServerRequestInterface as Request};
use Psr\Log\LoggerInterface;
use Slim\Exception\MethodNotAllowedException;
use Slim\Interfaces\RouterInterface;
use Slim\Views\Twig;

/**
 * Twig error controller.
 */
final class TwigErrorController extends TwigController
{

    /** @var LoggerInterface|null */
    private $logger;



    /**
     * @param RouterInterface      $router
     * @param Twig                 $twig
     * @param LoggerInterface|null $logger = null
     */
    public function __construct(RouterInterface $router, Twig $twig, ?LoggerInterface $logger = null)
    {
        parent::__construct($router, $twig);

        $this->logger = $logger;
    }



    /**
     * @param Request  $request
     * @param Response $response
     * @return Response
     */
    public function notFound(Request $request, Response $response): Response
    {
        return $this->twig($response->withStatus(404), 'error-404.twig', [
            'request' => $request,
            'response' => $response,
        ]);
    }



    /**
     * @param Request    $request
     * @param Response   $response
     * @param \Throwable $error
     * @return Response
     */
    public function phpError(Request $request, Response $response, \Throwable $error): Response
    {
        if ($this->logger) {
            $this->logger->error($error);
        }

        return $this->twig($response->withStatus(500), 'error-500.twig', [
            'request' => $request,
            'response' => $response,
            'error' => $error,
        ]);
    }



    /**
     * @param Request    $request
     * @param Response   $response
     * @param \Exception $exception
     * @return Response
     */
    public function error(Request $request, Response $response, \Exception $exception): Response
    {
        return $this->phpError($request, $response, $exception);
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $methods
     * @return Response
     */
    public function notAllowed(Request $request, Response $response, array $methods): Response
    {
        $e = new MethodNotAllowedException($request, $response, $methods);

        return $this->phpError($request, $response, $e);
    }

}
