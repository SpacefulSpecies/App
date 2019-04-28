<?php

namespace Species\App\Middleware;

use Aura\Session\Session;
use Psr\Http\Message\{ResponseInterface as Response, ServerRequestInterface as Request};
use Species\App\Middleware\Exception\InvalidCsrfToken;

/**
 * Csrf validation middleware.
 */
final class CsrfValidationMiddleware
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



    /**
     * @param Request  $request
     * @param Response $response
     * @param callable $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        if (!$this->isValid($request)) {
            $this->session->regenerateId();
            $this->session->clear();

            throw new InvalidCsrfToken();
        }

        return $next($request, $response);
    }



    /**
     * @param Request $request
     * @return bool
     */
    private function isValid(Request $request): bool
    {
        if ($request->getMethod() !== 'POST') {
            return true;
        }

        $token = $request->getParsedBody()['csrfToken'] ?? '';

        return $this->session->getCsrfToken()->isValid($token);
    }

}
