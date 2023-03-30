<?php

namespace App\Exception;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\Response;

class ValidatorException extends \Exception
{
    const MESSAGE = "Validation fail";
    #[Pure] public function __construct(int $code = Response::HTTP_BAD_REQUEST)
    {
        parent::__construct(self::MESSAGE, $code);
    }
}