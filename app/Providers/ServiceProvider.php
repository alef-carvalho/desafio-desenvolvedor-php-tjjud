<?php

namespace App\Providers;

use App\Service\AuthorService;
use App\Service\AuthService;
use App\Service\BookService;
use App\Service\Interface\IAuthorService;
use App\Service\Interface\IAuthService;
use App\Service\Interface\IBookService;
use App\Service\Interface\ISubjectService;
use App\Service\SubjectService;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{

    public function register(): void
    {
        $this->app->bind(IAuthService::class, AuthService::class);
        $this->app->bind(IAuthorService::class, AuthorService::class);
        $this->app->bind(IBookService::class, BookService::class);
        $this->app->bind(ISubjectService::class, SubjectService::class);
    }
}
