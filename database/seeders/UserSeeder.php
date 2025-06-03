<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Repository\Interface\IUserRepository;

class UserSeeder extends Seeder
{
    protected array $users = [
        [
            'id'    => 1,
            'name'  => 'Alef Carvalho',
            'email' => 'csilva.alef@gmail.com',
        ]
    ];

    public function __construct(private readonly IUserRepository $repository)
    {
    }

    public function run(): void
    {
        $password = Hash::make("123456");
        foreach ($this->users as $user) {
            $this->repository->updateOrCreate(['id' => $user['id']], array_merge($user, ['password' => $password]));
        }
    }
}
