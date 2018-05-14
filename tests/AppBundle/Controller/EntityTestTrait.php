<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Room;
use AppBundle\Entity\User;
use Carbon\Carbon;
use Symfony\Component\HttpKernel\Client;

trait EntityTestTrait
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var User
     */
    private $authUser;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    protected function createUser($username, $password='password'): User
    {
        $data = [
            'username' => $username,
            'email' => $username.'@example.com',
            'plainPassword' => $password,
            'enabled' => True,
        ];

        $user = new User();
        foreach ($data as $k => $v) {
            $user->{'set'.ucfirst($k)}($v);
        }
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    protected function createAuthenticatedClient($username = 'admin', $password = 'password'): Client
    {
        $this->authUser = $this->createUser($username, $password);

        $client = static::createClient();
        $client->request(
            'POST',
            '/api/login/',
            [
                '_username' => $username,
                '_password' => $password,
            ]
        );

        $data = json_decode($client->getResponse()->getContent(), true);

        $client = static::createClient();
        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        return $client;
    }

    protected function createRoom($name='room', $addAuth=true): Room
    {
        $random_user = $this->createUser('user_'.rand(1, 900000));

        $room = new Room();
        $room->setName($name);
        $room->setCreatedAt(Carbon::now());
        $room->addUser($random_user);

        if ($addAuth) {
            $user = $this->em->find(User::class, $this->authUser->getId());
            $room->addUser($user);
        }

        $this->em->persist($room);
        $this->em->flush();

        return $room;
    }

    protected function postJson($client, $path, $data)
    {
        return $client->request(
            'POST',
            $path,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );
    }
}