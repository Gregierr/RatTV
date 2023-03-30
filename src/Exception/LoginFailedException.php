<?php

namespace App\Exception;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\Response;

class LoginFailedException extends  \Exception
{
    const MESSAGE = "Login failed";
    #[Pure] public function __construct(int $code = Response::HTTP_BAD_REQUEST)
    {
        parent::__construct(self::MESSAGE, $code);
    }
}