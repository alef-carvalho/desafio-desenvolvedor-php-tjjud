<?php

namespace App\Http\Controllers\API;

use App\Traits\JsonController;
use App\Http\Controllers\Controller;
use App\Service\Interface\IAuthService;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;
use App\Http\Requests\API\Auth\LoginFormRequest;

#[Prefix('api/v1/auth')]
class AuthController extends Controller
{
    use JsonController;

    public function __construct(private readonly IAuthService $service)
    {
        //
    }

    #[Post('/')]
    public function logi(LoginFormRequest $request)
    {
        $credentials = $request->validated();
        $token = $this->service->login($credentials);

        return $this->success($token);
    }
}
