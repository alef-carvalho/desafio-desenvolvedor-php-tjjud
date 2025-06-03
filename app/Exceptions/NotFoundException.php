<?php

namespace App\Exceptions;

class NotFoundException extends HttpException
{
    protected int $status = 404;
}
