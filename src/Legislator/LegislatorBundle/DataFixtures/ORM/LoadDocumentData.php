<?php

namespace Legislator\LegislatorBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;

use Legislator\LegislatorBundle\Entity\Document;

class LoadDocumentData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = $this->getReference('test-user');

        $document = new Document();
        $document->setName('test document');
        $document->setDescription('loaded using fixtures');
        $document->setCreatedBy($user);
        $document->setModifiedOn(new \DateTime());
        $document->setCreatedOn(new \DateTime());

        $manager->persist($document);

        $document = new Document();
        $document->setName('test document for comments');
        $document->setDescription('loaded using fixtures, ready to be commented');
        $document->setCreatedBy($user);
        $document->setModifiedOn(new \DateTime());
        $document->setCreatedOn(new \DateTime());
        $document->setStatus(Document::STATUS_COMMENTING);

        $manager->persist($document);

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2;
    }
}
