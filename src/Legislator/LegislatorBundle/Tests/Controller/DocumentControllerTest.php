<?php

namespace Legislator\LegislatorBundle\Tests\Controller;

use Legislator\LegislatorBundle\Tests\MyWebTestCase;

class DocumentControllerTest extends MyWebTestCase
{
	public function setUp()
	{
		parent::setUp();

		$this->loadFixtures();
		$this->login();
	}

	public function testAdd()
	{
		$crawler = $this->client->request('GET', $this->getUrl('legislator_homepage'));
		$link = $crawler->filter('p a.add.button')->eq(0)->link();
		$crawler = $this->client->click($link);

		$form = $crawler->filter('input[type=submit]')->eq(0)->form();
		$form['legislator_document[name]'] = 'add document test';
		$form['legislator_document[description]'] = 'automated test';

		$temp = tmpfile();  // creates tmp file
		fwrite($temp, 'test file');
		$meta_data = stream_get_meta_data($temp);
		$form['legislator_document[file]']->upload($meta_data['uri']);
		fclose($temp);  // deletes tmp file

		$this->client->submit($form);
		$crawler = $this->client->followRedirect();

		$this->assertCount(3, $crawler->filter('tbody > tr'));  // 3 documents
		$this->assertCount(1, $crawler->filter('a:contains("add document test")'));
	}

    public function testShow()
    {
    	$crawler = $this->client->request('GET', $this->getUrl('legislator_homepage'));

		$this->assertTrue($this->client->getResponse()->isSuccessful());

		$link = $crawler->filter('a:contains("test document for comments")')->eq(0)->link();
		$crawler = $this->client->click($link);

		$this->assertCount(1, $crawler->filter('h1.header:contains("test document for comments")'));
    }

	public function testDelete()
	{
		$crawler = $this->client->request('GET', $this->getUrl('legislator_homepage'));
		$form = $crawler->filter('tbody tr:first-child form')->eq(1)->form();

		$this->client->submit($form);
		$crawler = $this->client->followRedirect();

		// there is only one document left
		$this->assertCount(1, $crawler->filter('tbody tr'));

	}
}
