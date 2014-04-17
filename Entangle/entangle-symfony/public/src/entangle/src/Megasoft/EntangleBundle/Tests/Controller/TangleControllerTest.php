<?php

namespace Megasoft\EntangleBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TangleControllerTest extends WebTestCase
{
    public function testCheckmembership()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/tangle/{tangleId}/check-membership');
    }

    public function testInvite()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/tangle/{tangleId}/invite');
    }

}
