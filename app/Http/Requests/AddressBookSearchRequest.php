<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Dto\AddressBookSearchValues;
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
//            '_count' => ['nullable', 'integer', 'min:1', 'max:20'],
            '_getpagesoffset' => ['nullable', 'integer'],
        ];
    }

    public function getSearchValues(): AddressBookSearchValues
    {
        $name = $this->query('name');
        $ura = $this->query('ura');
        $offset = (int) $this->query('_getpagesoffset', '0');

        if (!is_string($name)) {
            $name = null;
        }

        if (!is_string($ura)) {
            $ura = null;
        }

        return new AddressBookSearchValues(
            name: $name,
            ura: $ura,
            offset: $offset,
        );
    }
}
