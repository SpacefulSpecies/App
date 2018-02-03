<?php

namespace Species\App\Controller;

use Psr\Http\Message\ResponseInterface;
use Slim\Views\Twig;

/**
 * A Twig view controller helper.
 */
abstract class TwigViewController
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
     * @param ResponseInterface $response
     * @param string            $template
     * @param array|null        $data = null (default: [])
     * @return ResponseInterface
     */
    final protected function view(ResponseInterface $response, string $template, ?array $data = null): ResponseInterface
    {
        $charset = $this->twig->getEnvironment()->getCharset();

        return $this->twig->render($response, $template, $data ?? [])
            ->withHeader('Content-Type', "text/html; charset=$charset");
    }

}
