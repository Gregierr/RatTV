<?php

namespace App\Exception;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class VideoNotFoundException extends \Exception
{
    const MESSAGE = "Video name: %d does not exist";

    #[Pure] public function __construct(string $videoName, int $code = Response::HTTP_BAD_REQUEST)
    {
        $message = sprintf(self::MESSAGE, $videoName);
        parent::__construct($message, $code);
    }
}