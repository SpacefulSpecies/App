<?php

namespace Species\App;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Species\App\Exception\UnableToRunApp;

/**
 * App interface.
 */
interface Application
{

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface;



    /**
     * @throws UnableToRunApp
     */
    public function run(): void;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @return ResponseInterface
     * @throws UnableToRunApp
     */
    public function process(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface;

}
