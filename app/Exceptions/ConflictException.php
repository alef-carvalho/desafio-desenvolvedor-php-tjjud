<?php

namespace App\Exceptions;

class ConflictException extends HttpException
{
    protected int $status = 409;
}
