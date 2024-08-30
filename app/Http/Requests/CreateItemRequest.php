<?php

namespace App\Http\Requests;

use App\Rules\BuyNowPriceBiggerThanStartPrice;
use Illuminate\Foundation\Http\FormRequest;

class CreateItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|integer',
            'name' => 'required|string',
            'description' => 'required|string|max:255',
            'current_price' => 'required|numeric|min:1',
            'buy_now_price' => ['required', 'numeric', new BuyNowPriceBiggerThanStartPrice($this->start_price)],
            'payment' => 'required',
            'delivery' => 'required',
            'end_time' => 'required|integer|min:1|max:30',
            'images' => 'required|array|min:1|max:10',
            'images.*' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }
}
