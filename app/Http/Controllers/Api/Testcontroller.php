<?php
namespace App\Http\Controllers\Api;

use App\Services\Api\UserTokenService;

class Testcontroller
{
    public function testToekn()
    {
        $token = new UserTokenService("021NvyvE1kn6e80KnZvE1AHAvE1Nvyvz");

        dd($token->getUserToken());
    }
}