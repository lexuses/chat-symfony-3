<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RoomControllerTest extends WebTestCase
{
    use EntityTestTrait;

    public function testCreate()
    {
        $client = $this->createAuthenticatedClient();
        $this->createUser('user');

        $room = [
            'name' => 'New room',
            'users' => [1, 2],
        ];

        $this->postJson($client, '/api/rooms/?include[]=users', $room);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $data = json_decode($client->getResponse()->getContent());
        $this->assertObjectHasAttribute('name', $data->data);
        $this->assertCount(2, $data->data->users->data);
    }

    public function testShowRoomWithoutAuthUserError()
    {
        $client = $this->createAuthenticatedClient();
        $room = $this->createRoom('room', $addAuth=false);

        $client->request('GET', '/api/rooms/'.$room->getId().'/');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testShowRoom()
    {
        $client = $this->createAuthenticatedClient();

        $room = $this->createRoom('room');

        $client->request('GET', '/api/rooms/'.$room->getId().'/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testRoomList()
    {
        $client = $this->createAuthenticatedClient();

        for ($i=0; $i<5; $i++) {
            $this->createRoom('room'.$i);
        }
        $this->createRoom('room without auth user', $addAuth=false);

        $client->request('GET', '/api/rooms/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertCount(5, json_decode($client->getResponse()->getContent())->data);
    }
}
