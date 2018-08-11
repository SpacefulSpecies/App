<?php

namespace Species\App;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Species\App\Exception\AppRuntimeException;

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
     * @throws AppRuntimeException
     */
    public function run(): void;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @return ResponseInterface
     * @throws AppRuntimeException
     */
    public function process(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface;

}
