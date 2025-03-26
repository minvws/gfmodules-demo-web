<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressBookSearchRequest extends FormRequest
{
    protected $redirectRoute = 'address-book';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'ura' => ['nullable', 'integer', 'max_digits:8'],
        ];
    }

    public function isSearchPerformed(): bool
    {
        return $this->query('name') || $this->query('ura');
    }

    /**
     * @return array{name: string|null, ura: string|null}
     */
    public function getSearchValues(): array
    {
        return [
            'name' => $this->query('name'),
            'ura' => $this->query('ura'),
        ];
    }
}
