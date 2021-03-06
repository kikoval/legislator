<?php

namespace Legislator\LegislatorBundle\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\GroupableInterface;
use FOS\UserBundle\Model\GroupInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Document
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Document implements GroupableInterface
{
    const STATUS_NEW = 0;
    const STATUS_COMMENTING = 1;
    const STATUS_PROCESSING_COMMENTS = 2;
    const STATUS_FINISHED = 3;

    private static $status_messages = array(
            self::STATUS_NEW => 'document.statuses.new',
            self::STATUS_COMMENTING => 'document.statuses.commenting',
            self::STATUS_PROCESSING_COMMENTS =>'document.statuses.processing_comments',
            self::STATUS_FINISHED =>'document.statuses.finished');

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="Group")
     * @ORM\JoinTable(name="document_group",
     *      joinColumns={@ORM\JoinColumn(name="document_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    private $groups;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=128)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="version", type="smallint")
     */
    private $version = 1;

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
     * @ORM\JoinColumn(nullable=false)
     */
    private $createdBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modifiedOn", type="datetime")
     */
    private $modifiedOn;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="smallint")
     */
    private $status = self::STATUS_NEW;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="document", cascade={"remove"})
     */
    private $comments;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $path;

    /**
     * @Assert\File(maxSize="3000000")
     */
    private $file;
    private $temp_path;

    /**
     * @Assert\File(maxSize="3000000")
     */
    private $file_substantiation;

    /**
     * @var string
     *
     * @ORM\Column(name="pathSubstantiation", type="string", length=255,
     *             nullable=true)
     */
    private $path_substantiation;
    private $temp_path_substantiation;

    /**
     *
     * @var Document
     *
     * @ORM\OneToOne(targetEntity="Document", mappedBy="next_version")
     * @ORM\JoinColumn(nullable=true)
     */
    private $previous_version;

    /**
     * @var Document
     *
     * @ORM\OneToOne(targetEntity="Document", inversedBy="previous_version")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $next_version;

    /**
     *
     * @var boolean
     *
     * @ORM\Column(name="is_final_version", type="boolean", nullable=true)
     */
    private $is_final_version = FALSE;

    /**
     *
     * @var boolean
     *
     * @ORM\Column(name="is_archived", type="boolean")
     */
    private $is_archived = FALSE;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="comment_until", type="datetime", nullable=true)
     */
    private $comment_until;

    /* METHODS */

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
     * Gets the groups granted to the user.
     *
     * @return Collection
     */
    public function getGroups()
    {
        return $this->groups ?: $this->groups = new ArrayCollection();
    }

    public function getGroupNames()
    {
        $names = array();
        foreach ($this->getGroups() as $group) {
            $names[] = $group->getName();
        }

        return $names;
    }

    public function hasGroup($name)
    {
        return in_array($name, $this->getGroupNames());
    }

    public function addGroup(GroupInterface $group)
    {
        if (!$this->getGroups()->contains($group)) {
            $this->getGroups()->add($group);
        }

        return $this;
    }

    public function removeGroup(GroupInterface $group)
    {
        if ($this->getGroups()->contains($group)) {
            $this->getGroups()->removeElement($group);
        }

        return $this;
    }

    public function canBeAccessed(User $user)
    {
        if ($user === null) return FALSE;

        $groups = $this->getGroups();
        if (count($groups) == 0) return TRUE;

        $has_access = FALSE;
        foreach ($groups as $group) {
            $has_access |= $user->hasGroup($group);
        }

        return $has_access;
    }

    /**
     * Set name
     *
     * @param  string   $name
     * @return Document
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param  string   $description
     * @return Document
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set version
     *
     * @param  integer  $version
     * @return Document
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return integer
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set createdOn
     *
     * @param  \DateTime $createdOn
     * @return Document
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
     * @param  User     $createdBy
     * @return Document
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
     * @param  \DateTime $modifiedOn
     * @return Document
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
     * Set status
     *
     * @param  integer  $status
     * @return Document
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get absolute path to the main file.
     *
     * @return string
     */
    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    /**
     * Get web path to the main file.
     *
     * @return string
     */
    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir().'/'.$this->path;
    }

    /**
     * Get absolute path to the substantiation file.
     *
     * @return string
     */
    public function getAbsolutePathSubstantiation()
    {
        return null === $this->path_substantiation
        ? null
        : $this->getUploadRootDir().'/'.$this->path_substantiation;
    }

    /**
     * Get web path to the substantiation file.
     *
     * @return string
     */
    public function getWebPathSubstantiation()
    {
        return null === $this->path_substantiation
        ? null
        : $this->getUploadDir().'/'.$this->path_substantiation;
    }

    /**
     * Get upload root directory.
     *
     * @return string
     */
    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    /**
     * Get upload directory.
     *
     * @return string
     */
    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/documents';
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        // check if we have an old image path
        if (isset($this->path)) {
            // store the old name to delete after the update
            $this->temp_path = $this->path;
            $this->path = null;
        } else {
            $this->path = 'initial';
        }
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFileSubstantiation(UploadedFile $file = null)
    {
        $this->file_substantiation = $file;
        // check if we have an old image path
        if (isset($this->path_substantiation)) {
            // store the old name to delete after the update
            $this->temp_path_substantiation = $this->path_substantiation;
            $this->path_substantiation = null;
        } else {
            $this->path_substantiation = 'initial';
        }
    }


    /**
     * Get main file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Get substantiation file.
     *
     * @return UploadedFile
     */
    public function getFileSubstantiation()
    {
        return $this->file_substantiation;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->getFile()) {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));
            $this->path = $filename.'.'.$this->getFile()->guessExtension();
        }

        if (null !== $this->getFileSubstantiation()) {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));
            $this->path_substantiation = $filename.'.'.$this->getFileSubstantiation()->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null !== $this->getFile()) {
            // if there is an error when moving the file, an exception will
            // be automatically thrown by move(). This will properly prevent
            // the entity from being persisted to the database on error
            $this->getFile()->move($this->getUploadRootDir(), $this->path);

            // check if we have an old file
            if (isset($this->temp_path)) {
                // delete the old image
                unlink($this->getUploadRootDir().'/'.$this->temp_path);
                // clear the temp image path
                $this->temp_path = null;
            }

            $this->file = null;

            // making sure we have correct permissions
            chmod($this->getAbsolutePath(), 0644);
        }

        if (null !== $this->getFileSubstantiation()) {
            $this->getFileSubstantiation()->move($this->getUploadRootDir(), $this->path_substantiation);

            // check if we have an old file
            if (isset($this->temp_path_substantiation)) {
                // delete the old image
                unlink($this->getUploadRootDir().'/'.$this->temp_path_substantiation);
                // clear the temp image path
                $this->temp_path_substantiation = null;
            }

            $this->file_substantiation = null;

            // making sure we have correct permissions
            chmod($this->getAbsolutePathSubstantiation(), 0644);
        }
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        // removing main file
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
        // removing substrantiation ifle
        if ($file = $this->getAbsolutePathSubstantiation()) {
            unlink($file);
        }
    }

    /**
     * Get canBeCommented
     *
     * @return boolean
     */
    public function getCanBeCommented()
    {
        return $this->getStatus() == self::STATUS_COMMENTING
                && !$this->getIsFinalVersion();
    }

    /**
     * Set canBeCommented
     *
     * @param  unknown  $value
     * @return Document
     */
    public function setCanBeCommented($value = self::STATUS_COMMENTING)
    {
        $value = $value == self::STATUS_COMMENTING;
        $this->setStatus($value);

        return $this;
    }

    // Statuses
    public function isStatusCommenting()
    {
        return $this->getStatus() == self::STATUS_COMMENTING;
    }

    public function isStatusProcessingComments()
    {
        return $this->getStatus() == self::STATUS_PROCESSING_COMMENTS;
    }

    public function isStatusFinishedProcessingComments()
    {
        return $this->getStatus() == self::STATUS_FINISHED;
    }

    public function isStatusNew()
    {
        return $this->getStatus() == self::STATUS_NEW;
    }

    /**
     * Get previous version of the document
     *
     * @return Document
     */
    public function getPreviousVersion()
    {
        return $this->previous_version;
    }

    /**
     * Set previous version of the document
     *
     * @param  Document $document
     * @return Document
     */
    public function setPreviousVersion(Document $document)
    {
        $this->previous_version = $document;
        $document->setNextVersion($this);

        return $this;
    }

    /**
     * Get next version of the document
     *
     * @return Document
     */
    public function getNextVersion()
    {
        return $this->next_version;
    }

    /**
     * Set next version of the document
     *
     * @param  Document $document
     * @return Document
     */
    public function setNextVersion(Document $document)
    {
        $this->next_version = $document;

        return $this;
    }

    /**
     * Get is final version
     *
     * @return bool
     */
    public function getIsFinalVersion()
    {
        return $this->is_final_version;
    }

    /**
     * Set is final version
     *
     * @param  bool     $is_final_version
     * @return Document
     */
    public function setIsFinalVersion($is_final_version)
    {
        $this->is_final_version = $is_final_version;

        return $this;
    }

    /**
     * Check if a user is the owner of the document
     *
     * @param  User    $user
     * @return boolean
     */
    public function isOwner($user)
    {
        if (!$user) {
            return FALSE;
        } else {
            return $user->getID() == $this->getCreatedBy()->getID();
        }
    }

    public function mapFromStatusCode($code)
    {
        return self::$status_messages[$code];
    }

    public static function getStatusMessages()
    {
        return self::$status_messages;
    }

    public function getIsArchived()
    {
        return $this->is_archived;
    }

    public function setIsArchived($is_archived)
    {
        $this->is_archived = $is_archived;

        return $this;
    }

    /**
     * Set comment_until datetime.
     *
     * @param  \DateTime $createdOn
     * @return Document
     */
    public function setCommentUntil($comment_until)
    {
        $this->comment_until = $comment_until;

        return $this;
    }

    /**
     * Get comment_until
     *
     * @return \DateTime
     */
    public function getCommentUntil()
    {
        return $this->comment_until;
    }
}
