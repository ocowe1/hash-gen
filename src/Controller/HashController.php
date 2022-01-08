<?php

namespace App\Controller;

use App\Functions\Limiter;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\Mapping\Annotation;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class HashController extends AbstractController
{

    /**
     * This controller has a request limiter that limits the number of requests to 10 per minute.
     *
     * @param RequestStack $request
     * @param RateLimiterFactory $anonymousApiLimiter
     */
    public function __construct(RequestStack $request, RateLimiterFactory $anonymousApiLimiter)
    {
        $limiter = new Limiter();
        $limiter->limiter($request, $anonymousApiLimiter);
    }

    /**
     * @Route("/hash/{string}", name="hash_generation")
     * @param $string
     * @return JsonResponse
     */
    public function index($string): JsonResponse
    {
        $hashInfo = $this->hashGenerate($string);

        return new JsonResponse([
            'hash' => $hashInfo['hash'],
            'key' => $hashInfo['key'],
            'attempts' => $hashInfo['attempts'],
            'string' => $hashInfo['string']
        ], Response::HTTP_OK, [
            'Access-Control-Allow-Origin: ' => '*',
            'Content-Type: ' => 'application/json; charset=UTF-8',
            'Access-Control-Allow-Methods: ' => 'GET',
            'Access-Control-Allow-Headers: ' => 'Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With'
        ]);
    }

    /**
     * @param $string
     * @return array
     */
    #[ArrayShape(['hash' => "string", 'key' => "string", 'attempts' => "int", 'string' => 'string'])]
    public function hashGenerate($string): array
    {
        $key = substr(md5(Rand()), 0, 8);
        $hash = md5($string . $key);
        for ($attempts = 0; !str_starts_with($hash, '0000'); $attempts++){
            $key = substr(md5(Rand()), 0, 8);
            $hash = md5($string . $key);
            $attempts += 1;
        }

        return array('hash' => $hash, 'key' => $key, 'attempts' => $attempts, 'string' => $string);
    }

}