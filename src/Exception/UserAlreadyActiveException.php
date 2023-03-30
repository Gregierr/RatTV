<?php

namespace App\Exception;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\Response;

class UserAlreadyActiveException extends \Exception
{
    const MESSAGE = "User already active";
    #[Pure] public function __construct(int $code = Response::HTTP_BAD_REQUEST)
    {
        parent::__construct(self::MESSAGE, $code);
    }
}