<?php

namespace Legislator\LegislatorBundle\Tests\Controller;

use Legislator\LegislatorBundle\Tests\MyWebTestCase;

class DefaultControllerTest extends MyWebTestCase
{
    public function testIndex()
    {
    	$this->client->request('GET', $this->getUrl('legislator_homepage'));
    	// we are not logged in
    	$this->assertTrue($this->client->getResponse()->isRedirect());

    	$this->loadFixtures();
        $this->logIn();

        $crawler = $this->client->request('GET', $this->getUrl('legislator_homepage'));

        // make sure we are on main page
        $this->assertCount(1, $crawler->filter('h1'));
    }
}
