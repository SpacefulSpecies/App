<?php

namespace Species\App\TwigExtension;

use Slim\Interfaces\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;

/**
 * Router twig extension.
 */
final class RouterTwigExtension extends AbstractExtension implements GlobalsInterface
{

    /** @var RouterInterface */
    private $router;

    /** @var string */
    private $baseUrl;



    /**
     * @param RouterInterface $router
     * @param string          $baseUrl
     */
    public function __construct(RouterInterface $router, string $baseUrl)
    {
        $baseUrl = rtrim($baseUrl, '/');

        $this->router = $router;
        $this->baseUrl = $baseUrl;
    }



    /**
     * @return array
     */
    public function getGlobals(): array
    {
        return [
            'baseUrl' => $this->baseUrl,
        ];
    }

    /** @inheritDoc */
    public function getFunctions(): array
    {
        return [

            new TwigFunction('pathFor', function (string $name, array $data = [], array $queryParams = []) {
                return $this->router->pathFor($name, $data, $queryParams);
            }),

            new TwigFunction('urlFor', function (string $name, array $data = [], array $queryParams = []) {
                return $this->baseUrl . $this->router->pathFor($name, $data, $queryParams);
            }),

        ];
    }

}
