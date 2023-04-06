<?php

namespace Tests\Feature;

use Tests\TestCase;

class QuotesTest extends TestCase
{
    public function test_the_application_returns_a_successful_response(): void
    {
        $this->get('/')->assertStatus(200);
    }

    public function test_request_validation_errors(): void
    {
        $invalidInputs = $this->invalidInput();

        foreach($invalidInputs as $invalidInput) {

            $invalidData = $invalidInput[0];
            $invalidFields = $invalidInput[1];

            $this->post('/quotes', $invalidData)
                ->assertSessionHasErrors($invalidFields)
                ->assertStatus(302);
        }
    }

    public function test_request_validation(): void
    {
        $this->post('/quotes', ['symbol' => 'GOOG',
                                    'from' => fake()->dateTimeBetween('-3 months', '-2 months')->format('m/d/Y'),
                                    'to' => fake()->dateTimeBetween('-1 month', 'now')->format('m/d/Y'),
                                    'email' => fake()->email()])
            ->assertStatus(200);
    }

    public function invalidInput()
    {
        return
            [
                [
                    [
                        'symbol' => null,
                        'from' => null,
                        'to' => null,
                        'email' => null
                    ],
                    ['symbol', 'from', 'to', 'email']
                ],
                [
                    [
                        'symbol' => fake()->randomNumber(),
                        'from' => fake()->randomNumber(),
                        'to' => fake()->randomNumber(),
                        'email' => fake()->randomNumber()
                    ],
                    ['symbol', 'from', 'to', 'email']
                ],
                [
                    [
                        'symbol' => fake()->word(),
                        'from' => fake()->sentence(),
                        'to' => fake()->sentence(),
                        'email' => fake()->sentence()
                    ],
                    ['from', 'to', 'email']
                ],
                [
                    [
                        'symbol' => 'GOOG',
                        'from' => fake()->dateTimeBetween('+20 years', '+39 years')->format('m/d/Y'),
                        'to' => fake()->dateTimeBetween('+30 years', '+40 years')->format('m/d/Y'),
                        'email' => fake()->email()
                    ],
                    ['from', 'to']
                ],
                [
                    [
                        'symbol' => 'GOOG',
                        'from' => fake()->dateTimeBetween('+2 years', '+3 years')->format('m/d/Y'),
                        'to' => fake()->dateTimeBetween('-30 years', '-20 years')->format('m/d/Y'),
                        'email' => fake()->email()
                    ],
                    ['from', 'to']
                ],
            ];
    }
}
