<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class RegisterTest extends TestCase
{
    /**
     * A test testResponseStructure.
     *
     * @return void
     */
    public function testResponseStructure()
    {
        $this->post('/auth/register');

        $this->seeStatusCode(422);
        $this->seeJsonStructure([
            'status',
            'success',
            'message',
            'data',
            'exceptions'
        ]);
    }

    /**
     * A test testShouldReturnInvalidPhoneNumber.
     *
     * @return void
     */
    public function testShouldReturnInvalidPhoneNumber()
    {
        $parameters = [
            "phone" => "+++081234567893",
            "email" => "userdev2@mailinator.com",
            "password" => "secret@123",
            "password_confirmation" => "secret@123",
            "first_name" => "userdev2",
            "last_name" => "test",
            "birth_date" => "2000-02-02"
        ];
        $this->post('/auth/register', $parameters, []);

        $this->seeStatusCode(422);
        $this->seeJsonStructure([
            'exceptions' => [
                'phone',
            ]
        ]);
    }
}
