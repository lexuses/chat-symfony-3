<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    use EntityTestTrait;

    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Welcome to Symfony', $crawler->filter('#container h1')->text());
    }

    public function testRegister()
    {
        $client = static ::createClient();
        $user = [
            'email' => 'admin@example.com',
            'username' => 'admin',
            'plainPassword' => 'password'
        ];

        $this->postJson($client, '/api/register/', $user);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent());
        $this->assertObjectHasAttribute('token', $data);
    }

    public function testRegisterValidationError()
    {
        $client = static ::createClient();
        $userData = [
            'email' => 'admin@example.com',
            'username' => 'admin',
            'plainPassword' => 'password'
        ];

        // First add user
        $this->createUser('admin');
        // Repeat and get error
        $this->postJson($client, '/api/register/', $userData);

        $this->assertEquals(422, $client->getResponse()->getStatusCode());
    }

    public function testLogin()
    {
        $client = static ::createClient();

        $this->createUser('admin');
        $client->request('POST', '/api/login/', [
            '_username' => 'admin',
            '_password' => 'password'
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent());
        $this->assertObjectHasAttribute('token', $data);
    }

    public function testLoginBadCredentials()
    {
        $client = static ::createClient();

        $this->createUser('admin');
        $client->request('POST', '/api/login/', [
            '_username' => 'admin',
            '_password' => 'pass'
        ]);

        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    public function testAuth()
    {
        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/api/user/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $data = json_decode($client->getResponse()->getContent());
        $this->assertObjectHasAttribute('username', $data->data);
    }
}
