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
	const TYPE_STANDARD = 0;
	const TYPE_TECHNICAL = 1;
	const TYPE_PRINCIPAL = 2;

	private static $types = array(
			self::TYPE_STANDARD => 'comment.types.standard',
			self::TYPE_TECHNICAL => 'comment.types.technical',
			self::TYPE_PRINCIPAL => 'comment.types.principal',
	);

	const RESULT_ACCEPTED = 0;
	const RESULT_PARTLY_ACCEPTED = 1;
	const RESULT_NOT_ACCEPTED = 2;

	private static $results = array(
			self::RESULT_ACCEPTED => 'comment.results.accepted',
			self::RESULT_PARTLY_ACCEPTED => 'comment.results.partly_accepted',
			self::RESULT_NOT_ACCEPTED => 'comment.results.not_accepted',
	);

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
     * @ORM\ManyToOne(targetEntity="Document", inversedBy="comments")
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

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="smallint")
     */
    private $type;

    /**
     * @var boolean
     *
     * @ORM\Column(name="result", type="smallint", nullable=true)
     */
    private $result = null;

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
     * Set type
     *
     * @param int $type
     * @return Comment
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    public function isTechnical()
    {
    	return $this->getType() == self::TYPE_TECHNICAL;
    }

    public function isPrincipal()
    {
    	return $this->getType() == self::TYPE_PRINCIPAL;
    }

    public function getResult()
    {
    	return $this->result;
    }

    /**
     * @return boolean
     */
    public function isAccepted()
    {
        return $this->result === self::RESULT_ACCEPTED;
    }

    /**
     * @return boolean
     */
    public function isPartlyAccepted()
    {
    	return $this->result === self::RESULT_PARTLY_ACCEPTED;
    }


    /**
     * @return boolean
     */
    public function isRejected()
    {
        return $this->result === self::RESULT_NOT_ACCEPTED;
    }

    /**
     * Set isAccepted
     *
     * @param boolean $isAccepted
     * @return Comment
     */
    public function setResult($result)
    {
        $this->result = $result;

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

    public static function getTypes()
    {
    	return self::$types;
    }

    /**
     * Check if a user is the owner of the document
     *
     * @param User $user
     * @return boolean
     */
    public function isOwner(User $user)
    {
    	if (!$user) {
    		return FALSE;
    	} else {
    		return $user->getId() == $this->getCreatedBy()->getId();
    	}
    }

    /**
     * Get the array of choices for the result (whether it is accepted, etc.).
     *
     * @return array
     */
    public static function getResultChoices()
    {
    	return self::$results;
    }
}
