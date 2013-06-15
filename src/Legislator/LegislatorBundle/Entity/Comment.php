<?php

namespace Legislator\LegislatorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comment
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Comment
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Document
     *
     * @ORM\ManyToOne(targetEntity="Document")
     * @ORM\JoinColumn(name="document_id", referencedColumnName="id", nullable=false)
     */
    private $document;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="substantiation", type="text")
     */
    private $substantiation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdOn", type="datetime")
     */
    private $createdOn;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $createdBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modifiedOn", type="datetime", nullable=true)
     */
    private $modifiedOn;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isPrincipal", type="boolean")
     */
    private $isPrincipal;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isTechnical", type="boolean")
     */
    private $isTechnical;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isAccepted", type="boolean", nullable=true)
     */
    private $isAccepted = null;

    /**
     * @var string
     *
     * @ORM\Column(name="reply", type="text", nullable=true)
     */
    private $reply;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\Column(nullable=true)
     */
    private $repliedBy;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set document
     *
     * @param Document $document
     * @return Comment
     */
    public function setDocument(Document $document)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Get document
     *
     * @return Document
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Comment
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set substantiation
     *
     * @param string $substantiation
     * @return Comment
     */
    public function setSubstantiation($substantiation)
    {
        $this->substantiation = $substantiation;

        return $this;
    }

    /**
     * Get substantiation
     *
     * @return string
     */
    public function getSubstantiation()
    {
        return $this->substantiation;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return Comment
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get createdOn
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set createdBy
     *
     * @param User $createdBy
     * @return Comment
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set modifiedOn
     *
     * @param \DateTime $modifiedOn
     * @return Comment
     */
    public function setModifiedOn($modifiedOn)
    {
        $this->modifiedOn = $modifiedOn;

        return $this;
    }

    /**
     * Get modifiedOn
     *
     * @return \DateTime
     */
    public function getModifiedOn()
    {
        return $this->modifiedOn;
    }

    /**
     * Set isPrincipal
     *
     * @param boolean $isPrincipal
     * @return Comment
     */
    public function setIsPrincipal($isPrincipal)
    {
        $this->isPrincipal = $isPrincipal;

        return $this;
    }

    /**
     * Get isPrincipal
     *
     * @return boolean
     */
    public function getIsPrincipal()
    {
        return $this->isPrincipal;
    }

    /**
     * Set isTechnical
     *
     * @param boolean $isTechnical
     * @return Comment
     */
    public function setIsTechnical($isTechnical)
    {
        $this->isTechnical = $isTechnical;

        return $this;
    }

    /**
     * Get isTechnical
     *
     * @return boolean
     */
    public function getIsTechnical()
    {
        return $this->isTechnical;
    }

    /**
     * Get isAccepted
     *
     * @return boolean
     */
    public function getIsAccepted()
    {
        return $this->isAccepted;
    }

    /**
     * Set isAccepted
     *
     * @param boolean $isAccepted
     * @return Comment
     */
    public function setIsAccepted($isAccepted)
    {
        $this->isAccepted = $isAccepted;

        return $this;
    }

    /**
     * Get reply
     *
     * @return string
     */
    public function getReply()
    {
        return $this->reply;
    }

    /**
     * Set reply
     *
     * @param string $reply
     * @return Comment
     */
    public function setReply($reply)
    {
        $this->reply = $reply;

        return $this;
    }

    /**
     * Get repliedBy
     *
     * @return User
     */
    public function getRepliedBy()
    {
        return $this->repliedBy;
    }

    /**
     * Set repliedBy
     *
     * @param User $repliedBy
     * @return Comment
     */
    public function setRepliedBy($repliedBy)
    {
        $this->repliedBy = $repliedBy;

        return $this;
    }
}