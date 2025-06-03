<?php

namespace App\Exceptions;

class DatabaseConnectionException extends HttpException
{
    protected int $status = 500;
}
