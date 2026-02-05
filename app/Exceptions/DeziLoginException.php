<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DeziLoginException extends Exception
{
    /**
     * Report the exception.
     */
    public function report(): ?bool
    {
        return true;
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @return Response
     */
    public function render(Request $request)
    {
        return response()
            ->view('errors.dezi-login-exception', [], 403);
    }
}
