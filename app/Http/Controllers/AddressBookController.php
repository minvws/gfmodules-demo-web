<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AddressBookSearchRequest;
use App\Services\AddressingService;

class AddressBookController extends Controller
{
    public function __construct(
        protected AddressingService $addressingService,
    ) {
    }

    public function index(AddressBookSearchRequest $request)
    {
        $results = [];

        if ($request->isSearchPerformed()) {
            // Perform search
            $results = $this->addressingService->findOrganizations();
        }

        return view('address-book.index')
            ->with('results', $results);
    }
}
