<?php

namespace App\Responses;

class CommonResponseEntity
{
    public int $statusCode = 200;
    public string $message = "";
    public $data = null;
}