<?php

namespace Legislator\LegislatorBundle\Tests\Controller;

use Legislator\LegislatorBundle\Tests\MyWebTestCase;


class CommentControllerTest extends MyWebTestCase
{

	private $crawler = null;

	/**
	 * Prepare each test
	 */
	public function setUp()
	{
		parent::setUp();

		// load document
		$this->loadFixtures();

		$this->logIn();

		// open document ready for commenting, there are no comments
		$crawler = $this->client->request('GET', $this->getUrl('legislator_homepage'));

		$this->assertTrue($this->client->getResponse()->isSuccessful());

		$link = $crawler->filter('a:contains("test document for comments")')->eq(0)->link();
		$crawler = $this->client->click($link);
		$this->assertCount(2, $crawler->filter('form'));
	}

	public function testAdd()
	{
		$crawler = $this->client->getCrawler();

		$form = $crawler->filter('#legislator_comment')->parents()->eq(0)->form();
		$this->client->submit($form,
				array('legislator_comment[content]' => 'standard test comment',
					  'legislator_comment[substantiation]' => 'because it has to be tested :)'));

		$this->assertTrue($this->client->getResponse()->isRedirect());
		$crawler = $this->client->followRedirect();
		$this->assertCount(1, $crawler->filter('table'));
		$this->assertCount(2, $crawler->filter('tr')); // only header and one comment
	}

	public function testEdit()
	{
		// add a comment
		$this->testAdd();

		$crawler = $this->client->getCrawler();

		// make sure we are on the right page
		$this->assertCount(1, $crawler->filter('h1:contains("test document for comments")'));
		$this->assertCount(1, $crawler->filter('#comments'));

		// edit the comment
                $edit_link = $crawler->filter('tbody a[class~="edit"]')->eq(0)->link();
		$crawler = $this->client->click($edit_link);

		$form = $crawler->filter('#legislator_comment')->parents()->eq(0)->form();
		$this->assertEquals($form['legislator_comment[content]']->getValue(), 'standard test comment');

		$form['legislator_comment[content]'] = 'changed comment';
		$this->client->submit($form);
		$this->assertTrue($this->client->getResponse()->isRedirect());
		$crawler = $this->client->followRedirect();

		$this->assertEquals($crawler->filter('tbody tr:first-child td:first-child')->text(), 'changed comment');

	}
	
	public function testDelete()
	{
		// add a comment
		$this->testAdd();
		
		$crawler = $this->client->getCrawler();
		
		// make sure we are on the right page
		$this->assertCount(1, $crawler->filter('h1:contains("test document for comments")'));
		$this->assertCount(1, $crawler->filter('#comments'));
		
		// delete comment
		$form = $crawler->filter('tbody button[name="delete"]')->eq(0)->form();
		$crawler = $this->client->submit($form);
		$this->assertCount(0, $crawler->filter('table')); // there is not comment
	}
}
