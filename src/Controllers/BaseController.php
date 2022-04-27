<?php declare(strict_types = 1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface;

/**
 * Class BaseController
 * @package App\Controllers
 */
class BaseController
{
    /**
     * BaseController constructor.
     * @param ResponseInterface $response
     */
    public function __construct(protected ResponseInterface $response)
    {
    }

    /**
     * @param int $httpStatus
     * @param string $body
     * @return ResponseInterface
     */
    public function getResponse(int $httpStatus, string $body = ''): ResponseInterface
    {
        $response = $this->response
            ->withStatus($httpStatus)
            ->withHeader('Content-Type', 'text/plain')
        ;
        $response->getBody()
            ->write($body);

        return $response;
    }
}
