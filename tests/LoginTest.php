<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class LoginTest extends TestCase
{
    /**
     * A basic test ShouldReturnMethodNotAllowed.
     *
     * @return void
     */
    public function testShouldReturnMethodNotAllowed()
    {
        $this->get('/auth/login');

        $this->seeStatusCode(405);
    }

    /**
     * A basic test testShouldReturnAccessToken.
     *
     * @return void
     */
    public function testShouldReturnAccessToken() {
        $parameters = [
            'username' => 'webdev@mailinator.com',
            'password' => 'secret@123',
        ];
        $this->post('/auth/login', $parameters, []);

        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'data' => [
                'access_token'
            ]
        ]);
    }

    /**
     * A basic test testShouldReturnInvalidEmailFormat.
     *
     * @return void
     */
    public function testShouldReturnInvalidEmailFormat() {
        $parameters = [
            'username' => 'webdevmailinator.com',
            'password' => 'secret@123',
        ];
        $this->post('/auth/login', $parameters, []);

        $this->seeStatusCode(422);
        $this->seeJsonStructure([
            'exceptions' => [
                'username'
            ]
        ]);
    }

    /**
     * A basic test testShouldReturnWrongPassword.
     *
     * @return void
     */
    public function testShouldReturnWrongPassword() {
        $parameters = [
            'username' => 'webdev@mailinator.com',
            'password' => 'secret123',
        ];
        $this->post('/auth/login', $parameters, []);

        $this->seeStatusCode(422);
        $this->seeJsonStructure([
            'message'
        ]);
    }

    /**
     * A basic test testShouldReturnRequiredFields.
     *
     * @return void
     */
    public function testShouldReturnRequiredFields() {
        $this->post('/auth/login', [], []);

        $this->seeStatusCode(422);
        $this->seeJsonStructure([
            'message'
        ]);
    }
}
