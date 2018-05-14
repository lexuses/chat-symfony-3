<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RoomMessageControllerTest extends WebTestCase
{
    use EntityTestTrait;

    public function testCreateMessage()
    {
        $client = $this->createAuthenticatedClient();
        $room = $this->createRoom();
        $data = [ 'text' => 'hello' ];

        $this->postJson($client, '/api/rooms/'.$room->getId().'/messages/', $data);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $data = json_decode($client->getResponse()->getContent());
        $this->assertObjectHasAttribute('id', $data->data);
    }
}