<?php

namespace App\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class QuotesRequest extends FormRequest
{
    public function rules()
    {
        return [
            'symbol' => 'required|string',
            'from' => 'required|date|before_or_equal:' . Carbon::now()->format('m/d/Y'),
            'to' => 'required|date|after_or_equal:from|before_or_equal:' . Carbon::now()->format('m/d/Y'),
            'email' => 'required|email'
        ];
    }

    public function messages()
    {
        return [
            'from.required' => 'Start date is required',
            'to.required' => 'End date is required',
            'email.required' => 'Email is required',
            'from.date' => 'Start date must be a valid date',
            'to.date' => 'End date must be a valid date',
            'email.email' => 'Invalid email',
            'from.before_or_equal' => 'Start date must be a date before or equal to current date',
            'to.before_or_equal' => 'End date must be a date before or equal to current date',
            'to.after_or_equal' => 'Start date cannot be greater than end date',
        ];
    }
}
