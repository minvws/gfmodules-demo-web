<?php

declare(strict_types=1);

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function home(Request $request): View
    {
        return view('landing.home')->with('user', $request->user());
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();

        return redirect()->route('index');
    }
}
