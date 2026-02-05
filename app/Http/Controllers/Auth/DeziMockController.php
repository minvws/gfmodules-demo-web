<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use MinVWS\OpenIDConnectLaravel\Http\Responses\LoginResponseHandlerInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Services\Dezi\MockService;
use App\Exceptions\DeziLoginException;

class DeziMockController extends Controller
{
    public function __construct(
        private readonly LoginResponseHandlerInterface $loginResponseHandler,
        private readonly MockService $mockService
    ) {
    }

    public function login(): Response
    {
        // A mock JWS is placed in session when DEZI Mock is enabled
        // If normal flow with Dezi login is enabled, we don't need to do anything special:
        // setting JWS in session is then handled by JweDecryptService
        $jws = $this->mockService->createMockEnvelopeJws();
        $serializedJws = $this->mockService->serializeJws($jws);

        Session::put('oidc_jwt', $serializedJws);

        $payload = $jws->getPayload();

        if ($payload === null) {
            throw new DeziLoginException('Failed to get mock JWS payload');
        }

        $object = json_decode($payload, false);

        if ($object === null) {
            throw new DeziLoginException('Failed to decode mock JWS payload');
        }

        return $this->loginResponseHandler->handleLoginResponse(
            $object
        );
    }
}
