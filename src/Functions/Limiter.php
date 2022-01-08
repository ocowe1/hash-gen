<?php

namespace App\Functions;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class Limiter
{

    /**
     * This function if requested will return the 429 code for Too Many Requests
     *
     * @param RequestStack $request
     * @param RateLimiterFactory $anonymousApiLimiter
     * @return JsonResponse
     */
    public function limiter(RequestStack $request, RateLimiterFactory $anonymousApiLimiter): JsonResponse
    {
        $limiter = $anonymousApiLimiter->create($request->getClientIp());
        if (false === $limiter->consume(1)->isAccepted()) {
            return throw new TooManyRequestsHttpException();
        }
        return new JsonResponse(['Retry-After' => '1 minute'], Response::HTTP_TOO_MANY_REQUESTS, [
            'Access-Control-Allow-Origin: ' => '*',
            'Content-Type: ' => 'application/json; charset=UTF-8',
            'Access-Control-Allow-Methods: ' => 'GET',
            'Access-Control-Allow-Headers: ' => 'Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With'
        ]);

    }

}
