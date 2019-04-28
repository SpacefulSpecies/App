<?php

namespace Species\App\TwigExtension;

use Aura\Session\Session;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Csrf twig extension.
 */
final class CsrfTwigExtension extends AbstractExtension
{

    /** @var Session */
    private $session;



    /**
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }



    /** @inheritDoc */
    public function getFunctions(): array
    {
        return [

            new TwigFunction('csrfToken', function () {
                return $this->getCsrfToken();
            }),

            new TwigFunction('csrfTokenInput', function () {
                return sprintf(
                    '<input type="hidden" name="csrfToken" value="%s">',
                    htmlspecialchars($this->getCsrfToken())
                );
            }, ['is_safe' => 'html']),

        ];
    }



    /**
     * @return string
     */
    private function getCsrfToken(): string
    {
        return $this->session->getCsrfToken()->getValue();
    }

}
