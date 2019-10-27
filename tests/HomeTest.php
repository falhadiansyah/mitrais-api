<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class HomeTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testResponseStructure()
    {
        $this->get('/');

        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'success',
            'message',
            'data',
            'exceptions'
        ]);
    }

    public function testLumenVersion() {
        $this->get('/');

        $response = json_decode($this->response->getContent());
        $this->assertEquals(
            $this->app->version(), $response->data->version
        );
    }
}
