<?php

namespace Megasoft\EntangleBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class createTangleControllerTest extends WebTestCase
{
    public function testCreatetangle()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'tangle/');
    }

}
