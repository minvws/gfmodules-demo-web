<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AddressBookSearchRequest;
use App\Services\AddressingService;
use Illuminate\View\View;

class AddressBookController extends Controller
{
    public function __construct(
        protected AddressingService $addressingService,
    ) {
    }

    public function index(AddressBookSearchRequest $request): View
    {
        [$organizations, $endpoints] = $this->addressingService->findOrganizations(
            searchValues: $request->getSearchValues(),
        );

        return view('address-book.index')
            ->with('organizations', $organizations)
            ->with('endpoints', $endpoints);
    }

    public function orgInfo(string $ref, AddressingService $addressingService): View
    {
        $org = $addressingService->findOrganization($ref, includeEndpoints: true);

        return view('address-book.org_info')
            ->with('organization', $org['organization'])
            ->with('endpoints', $org['endpoints'] ?? []);
    }
}
