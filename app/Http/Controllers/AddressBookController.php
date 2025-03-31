<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\AddressingResponseException;
use App\Exceptions\AddressingUnavailableException;
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
        $result = null;
        $error = null;

        try {
            $result = $this->addressingService->findOrganizations(
                searchValues: $request->getSearchValues(),
            );
        } catch (AddressingUnavailableException $e) {
            $error = __('Addressing service is unavailable.');
            report($e);
        } catch (AddressingResponseException $e) {
            $error = __('Addressing service returned an error.');
            report($e);
        }

        return view('address-book.index')
            ->with('result', $result)
            ->with('error', $error);
    }

    public function orgInfo(string $ref, AddressingService $addressingService): View
    {
        $org = $addressingService->findOrganization($ref, includeEndpoints: true);

        return view('address-book.org_info')
            ->with('organization', $org['organization'])
            ->with('endpoints', $org['endpoints'] ?? []);
    }
}
