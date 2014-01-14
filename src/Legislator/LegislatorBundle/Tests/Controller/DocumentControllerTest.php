<?php

namespace Legislator\LegislatorBundle\Tests\Controller;

use Legislator\LegislatorBundle\Entity\Document;
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
        $form = $crawler->filter('tbody tr:first-child form')->eq(0)->form();

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        // there is only one document left
        $this->assertCount(1, $crawler->filter('tbody tr'));

    }

    public function testNewVersion()
    {
        $crawler = $this->client->request('GET', $this->getUrl('legislator_homepage'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $num_documents = $crawler->filter('tbody tr')->count();

        // go to a document
        $link = $crawler->filter('a:contains("test document for comments")')->eq(0)->link();
        $crawler = $this->client->click($link);
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        // got to edit form
        $link = $crawler->filter('a.edit')->eq(0)->link();
        $crawler = $this->client->click($link);
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        // set status to finished to enable adding new version
        $form = $crawler->filter('input[type=submit].save')->eq(0)->form();
        $form['legislator_document[status]']->select(Document::STATUS_FINISHED);
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertCount(1, $crawler->filter('a.add'));

        // go to new version form
        $link = $crawler->filter('a.add')->eq(0)->link();
        $crawler = $this->client->click($link);
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        // add new version
        $form = $crawler->filter('input[type=submit].add')->eq(0)->form();
        $temp = tmpfile();  // creates tmp file
        fwrite($temp, 'new version test file');
        $meta_data = stream_get_meta_data($temp);
        $form['legislator_document[file]']->upload($meta_data['uri']);
        $form['legislator_document[description]'] = '++new version++';
        fclose($temp);  // deletes tmp file
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertCount($num_documents + 1, $crawler->filter('tbody tr'));
        $this->assertCount(1, $crawler->filter('td:contains("++new version++")'));

    }
}
