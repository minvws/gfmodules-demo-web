<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use MinVWS\OpenIDConnectLaravel\Http\Responses\LoginResponseHandlerInterface;
use Symfony\Component\HttpFoundation\Response;

class DigidMockController extends Controller
{
    public function __construct(
        private readonly LoginResponseHandlerInterface $loginResponseHandler
    ) {
    }

    public function login(): Response
    {
        return $this->loginResponseHandler->handleLoginResponse(
            (object)[
                "relations" => [(object)['entity_name' => "BossEntity", 'ura' => "12341234", 'roles' => ["01.000"]]],
                "initials" => "m",
                "surname" => "Boss",
                "surname_prefix" => "",
                "uzi_id" => "12312123",
                "loa_uzi" => "Basis",
            ]
        );
    }
}
