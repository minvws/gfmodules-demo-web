<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AddressBookSearchRequest;

class AddressBookController extends Controller
{
    public function index(AddressBookSearchRequest $request)
    {
        if ($request->isSearchPerformed()) {
            // Perform search
//            dd('Search performed');
        }

        return view('address-book.index');
    }
}
