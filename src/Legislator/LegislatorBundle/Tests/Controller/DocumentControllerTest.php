<?php

namespace Legislator\LegislatorBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DocumentControllerTest extends WebTestCase
{
    public function testShow()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/document/{id}');
    }

    public function testComment()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/document/{id}/comment');
    }

}
