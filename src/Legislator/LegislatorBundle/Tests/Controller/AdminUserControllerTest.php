<?php

namespace Legislator\LegislatorBundle\Tests\Controller;

use Legislator\LegislatorBundle\Tests\MyWebTestCase;

class AdminUserControllerTest extends MyWebTestCase
{
    public function testList()
    {
        $crawler = $this->client->request('GET', $this->getUrl('legislator_user_list'));

        // we are not logged in
        $this->assertTrue($this->client->getResponse()->isRedirect());

        $this->client->restart();

        $this->loadFixtures();
        $this->logIn();

        $crawler = $this->client->request('GET', $this->getUrl('legislator_user_list'));
        $this->assertTrue($this->client->getResponse()->isForbidden());
    }

}
