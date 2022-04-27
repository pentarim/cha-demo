<?php

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

class UserApiTest extends TestCase
{
    private ?Client $http;

    public function setUp(): void
    {
        $baseUrl = $_ENV['APP_BASE_URL'] ?? 'http://localhost:8080/';
        $this->http = new Client(['base_uri' => $baseUrl]);
    }

    public function testCreateUserSuccess()
    {
        $response = $this->http->request('POST','user', [
            'form_params' => [
                'name' => 'JohnDoe',
                'yearOfBirth' => 1984,
            ]
        ]);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertMatchesRegularExpression('/New user with id/i', $response->getBody());

        $userId = filter_var($response->getBody(), FILTER_SANITIZE_NUMBER_INT);

        $this->assertTrue(is_numeric($userId));

        return (int)$userId;
    }

    public function testCreateUserFailure()
    {
        try {
            $response = $this->http->request('POST', 'user', [
                'form_params' => [
                    'name' => 'John&Does',
                    'yearOfBirth' => 1985,
                ]
            ]);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $this->assertEquals(400, $e->getResponse()->getStatusCode());
        }

        if (isset($response)) {
            $this->fail('RequestException with 400 status code was expected');
        }
    }

    /**
     * @depends testCreateUserSuccess
     */
    public function testUpdateUserSuccess($userId)
    {
        $response = $this->http->request('POST','user/' . $userId, [
            'form_params' => [
                'name' => 'JohnDoes',
                'yearOfBirth' => 1962,
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertMatchesRegularExpression('/User has been updated/i', $response->getBody());
    }

    /**
     * @depends testCreateUserSuccess
     */
    public function testUpdateUserFailure($userId)
    {
        try {
            $response = $this->http->request('POST', 'user/' . $userId, [
                'form_params' => [
                    'name' => 'JohnDoes1',
                    'yearOfBirth' => 'aa',
                ]
            ]);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $this->assertEquals(400, $e->getResponse()->getStatusCode());
        }

        if (isset($response)) {
            $this->fail('RequestException was expected');
        }
    }

    /**
     * @depends testCreateUserSuccess
     */
    public function testShowUserSuccess($userId)
    {
        $response = $this->http->request('GET','user/' . $userId);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertMatchesRegularExpression('/born in/i', $response->getBody());
    }

    public function testShowUser404Failure()
    {
        try {
            $response = $this->http->request('GET','user/999999');
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $this->assertEquals(404, $e->getResponse()->getStatusCode());
        }

        if (isset($response)) {
            $this->fail('RequestException with status code 404 was expected');
        }
    }

    public function tearDown(): void
    {
        $this->http = null;
    }
}
