<?php

namespace App\Exceptions;

class DatabaseQueryException extends HttpException
{
    protected int $status = 500;
}
