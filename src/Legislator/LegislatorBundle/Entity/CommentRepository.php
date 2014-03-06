<?php
namespace Legislator\LegislatorBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CommentRepository extends EntityRepository
{
    public function findMaxPosition(Document $d)
    {
        $c = $this->getEntityManager()
                ->getRepository('LegislatorBundle:Comment')
                ->findOneBy(
                    array('document' => $d),
                    array('position' => 'DESC')
                );
        if ($c === NULL) return 0;
        return $c->getPosition();
    }
}

