<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Exceptions\DeziLoginException;
use App\Models\DeziUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use MinVWS\OpenIDConnectLaravel\Http\Responses\LoginResponseHandlerInterface;
use Symfony\Component\HttpFoundation\Response;

class OidcLoginResponseHandler implements LoginResponseHandlerInterface
{
    /**
     * @param object{
     *     jti: string,
     *     iat: int,
     *     exp: int,
     *     iss: string,
     *     aud: string,
     *     loa_authn: string,
     *     json_schema: string,
     *     verklaring_id: string,
     *     verklaring: string
     * } $userInfo
     *
     * @throws DeziLoginException
     */
    public function handleLoginResponse(object $userInfo): Response
    {
        $user = DeziUser::deserializeFromObject($userInfo);

        $user->setJwt(Session::get('oidc_jwt') ?? '');

        if (empty($user->getJwt())) {
            return redirect()
                ->route('index')
                ->with('error', __('Something went wrong with logging in, please try again.'));
        }

        Auth::setUser($user);

        return new RedirectResponse(route('flow'));
    }
}
