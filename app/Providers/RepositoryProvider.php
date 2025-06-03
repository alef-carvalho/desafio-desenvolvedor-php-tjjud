<?php

namespace App\Providers;

use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Repository\Interface\IAuthorRepository;
use App\Repository\Interface\IBookRepository;
use App\Repository\Interface\ISubjectRepository;
use App\Repository\Interface\IUserRepository;
use App\Repository\SubjectRepository;
use App\Repository\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(IAuthorRepository::class, AuthorRepository::class);
        $this->app->bind(IBookRepository::class, BookRepository::class);
        $this->app->bind(ISubjectRepository::class, SubjectRepository::class);
        $this->app->bind(IUserRepository::class, UserRepository::class);
    }
}
