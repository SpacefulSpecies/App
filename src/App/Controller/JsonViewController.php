<?php

namespace Species\App\Controller;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Body as SlimBody;

/**
 * A json view controller helper.
 */
abstract class JsonViewController
{

    /**
     * @param ResponseInterface $response
     * @param mixed             $data
     * @return ResponseInterface
     */
    final protected function view(ResponseInterface $response, $data): ResponseInterface
    {
        $body = new SlimBody(fopen('php://temp', 'r+'));
        $body->write(json_encode($data));

        return $response
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withBody($body);
    }

}
