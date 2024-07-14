<?php

namespace App\Interceptor;

use PhpBoot\Di\Attribute\Service;
use PhpBoot\Http\Common\HeaderMap;
use PhpBoot\Http\Request\Request;
use PhpBoot\Security\Authentication;
use PhpBoot\Security\SecurityContextHolder;
use PhpBoot\Starter\Web\Interceptor\InterceptorChain;
use PhpBoot\Starter\Web\Interceptor\PreRequestInterceptor;

#[Service]
class SecurityInterceptor extends PreRequestInterceptor
{
    private const array USERS = [
        'user' => ['username' => 'user', 'password' => 'pass', 'authorities' => ['ROLE_USER']],
        'admin' => ['username' => 'admin', 'password' => 'pass', 'authorities' => ['ROLE_ADMIN']],
    ];

    public function __construct(
        InterceptorChain $interceptorChain
    )
    {
        $interceptorChain->registerInterceptor($this);
    }

    public function preRequest(Request $request): void
    {
        if (!$request->getHeaders()->has(HeaderMap::AUTHORIZATION_HEADER)) return;

        $header = $request->getHeaders()->get(HeaderMap::AUTHORIZATION_HEADER);

        if (!str_starts_with(strtolower($header), 'basic')) return;

        list($username, $password) = explode(':', base64_decode(substr($header, 6)));

        $foundUser = null;
        foreach (self::USERS as $user) {
            if ($user['username'] === $username) {
                $foundUser = $user;
                break;
            }
        }

        if ($foundUser !== null && $foundUser['password'] === $password) {
            $auth = new Authentication($foundUser['authorities'], $foundUser);
            SecurityContextHolder::getInstance()->setAuthentication($auth);
        }
    }

    public function order(): int
    {
        return 9000;
    }
}