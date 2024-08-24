<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BuyNowPriceBiggerThanStartPrice implements ValidationRule
{
    protected $start_price;

    public function __construct($start_price)
    {
        $this->start_price = $start_price;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if($value <= $this->start_price){
            $fail('The buy now price has to be bigger than the start price');
        }
    }
}
