<?php

namespace Legislator\LegislatorBundle\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Document
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Document
{
    const STATUS_NEW = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="version", type="smallint")
     */
    private $version;

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
    private $status;

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
     * @var boolean
     *
     * @ORM\Column(name="canBeCommented", type="boolean")
     */
    private $can_be_commented;

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
     * Set name
     *
     * @param string $name
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
     * @param string $description
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
     * @param integer $version
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
     * @param \DateTime $createdOn
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
     * @param User $createdBy
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
     * @param \DateTime $modifiedOn
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
     * @param integer $status
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
        if (null === $this->getFile() || null == $this->getFileSubstantiation()) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getFile()->move($this->getUploadRootDir(), $this->path);
        $this->getFileSubstantiation()->move($this->getUploadRootDir(), $this->path_substantiation);

        // check if we have an old file
        if (isset($this->temp_path)) {
            // delete the old image
            unlink($this->getUploadRootDir().'/'.$this->temp_path);
            // clear the temp image path
            $this->temp_path = null;
        }
        // check if we have an old file
        if (isset($this->temp_path_substantiation)) {
            // delete the old image
            unlink($this->getUploadRootDir().'/'.$this->temp_path_substantiation);
            // clear the temp image path
            $this->temp_path_substantiation = null;
        }
        $this->file = null;
        $this->file_substantiation = null;
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
        return $this->can_be_commented;
    }

    /**
     * Set canBeCommented
     *
     * @param unknown $value
     * @return Document
     */
    public function setCanBeCommented($value)
    {
        $this->can_be_commented = $value;

        return $this;
    }
}
