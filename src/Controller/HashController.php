<?php

namespace App\Controller;

use JetBrains\PhpStorm\ArrayShape;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class HashController extends AbstractController
{

    #[Route('/hash/{string}')]
    public function index($string, Request $request, RateLimiterFactory $anonymousApiLimiter): Response
    {
        $limiter = $anonymousApiLimiter->create($request->getClientIp());
        if (false === $limiter->consume(1)->isAccepted()) {
            throw new TooManyRequestsHttpException();
        }

        $hashInfo = $this->hashGenerate($string);
        return $this->json(['hash' => $hashInfo['hash'], 'key' => $hashInfo['key'], 'Attempts' => $hashInfo['attempts']]);
    }

    #[ArrayShape(['hash' => "string", 'key' => "string", 'attempts' => "int"])]
    public function hashGenerate($string): array
    {
        $key = substr(md5(Rand()), 0, 8);
        $hash = md5($string . $key);
        for ($attempts = 0; !str_starts_with($hash, '0000'); $attempts++){
            $key = substr(md5(Rand()), 0, 8);
            $hash = md5($string . $key);
            $attempts += 1;
        }
        $hash = md5($string . $key);

        return array('hash' => $hash, 'key' => $key, 'attempts' => $attempts);
    }



}