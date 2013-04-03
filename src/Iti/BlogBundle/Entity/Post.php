<?php

namespace Iti\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Post
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Iti\BlogBundle\Entity\PostRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Post
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
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * the category of the post
     * @var \Iti\BlogBundle\Entity\Category category
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="\Iti\BlogBundle\Entity\Category", inversedBy="posts")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $category;
    
    /**
     * the comments of the post
     * @var \Doctrine\Common\Collections\ArrayCollection $comments
     * @ORM\OneToMany(targetEntity="\Iti\BlogBundle\Entity\Comment", mappedBy="post", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $comments;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection $tags
     * @ORM\ManyToMany(targetEntity="\Iti\BlogBundle\Entity\Tag")
     * @ORM\JoinTable(name="post_tag",
     *     joinColumns={@ORM\JoinColumn(name="post_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)},
     *     inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)}
     * )
     */
    private $tags;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;
    
    /**
     * @var string $image
     *
     * @ORM\Column(name="image", type="string", length=20, nullable=true)
     */
    private $image;

    /**
     * a temp variable for storing the old image name to delete the old image after the update
     * @var string $temp
     */
    private $temp;

    /**
     * this flag is for detecting if the image has been handled
     * @var boolean $imageHandeled
     */
    private $imageHandeled = FALSE;

    /**
     * @Assert\Image
     * @var \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    public $file;

    public function __construct() {
        $this->createdAt = new \DateTime();
        $this->comments = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    /**
     * Set image
     *
     * @param string $image
     */
    public function setImage($image) {
        $this->image = $image;
    }

    /**
     * Get image
     *
     * @return string 
     */
    public function getImage() {
        return $this->image;
    }

    /**
     * this function is used to delete the current image
     */
    public function removeImage() {
        //check if we have an old image
        if ($this->image) {
            //store the old name to delete the image on the upadate
            $this->temp = $this->image;
            //delete the current image
            $this->setImage(NULL);
        }
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload() {
        if (NULL !== $this->file && !$this->imageHandeled) {
            //get the image extension
            $extension = $this->file->guessExtension();
            //generate a random image name
            $img = uniqid();
            //get the image upload directory
            $uploadDir = $this->getUploadRootDir();
            //check if the upload directory exists
            if (!@is_dir($uploadDir)) {
                //get the old umask
                $oldumask = umask(0);
                //not a directory probably the first time for this category try to create the directory
                $success = @mkdir($uploadDir, 0755, TRUE);
                //reset the umask
                umask($oldumask);
                //check if we created the folder
                if (!$success) {
                    //could not create the folder throw an exception to stop the insert
                    throw new \Exception("Can not create the image directory $uploadDir");
                }
            }
            //check that the file name does not exist
            while (@file_exists("$uploadDir/$img.$extension")) {
                //try to find a new unique name
                $img = uniqid();
            }
            //check if we have an old image
            if ($this->image) {
                //store the old name to delete the image on the upadate
                $this->temp = $this->image;
            }
            //set the image new name
            $this->image = "$img.$extension";
            //set the flag to indecate that the image has been handled
            $this->imageHandeled = TRUE;
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload() {
        if (NULL !== $this->file) {
            // you must throw an exception here if the file cannot be moved
            // so that the entity is not persisted to the database
            // which the UploadedFile move() method does
            $this->file->move($this->getUploadRootDir(), $this->image);
            //remove the file as you do not need it any more
            $this->file = NULL;
        }
        //check if we have an old image
        if ($this->temp) {
            //try to delete the old image
            @unlink($this->getUploadRootDir() . '/' . $this->temp);
        }
    }

    /**
     * @ORM\PostRemove()
     */
    public function postRemove() {
        //check if we have an image
        if ($this->image) {
            //try to delete the image
            @unlink($this->getAbsolutePath());
        }
    }

    /**
     * @return string the path of image starting of root
     */
    public function getAbsolutePath() {
        return $this->getUploadRootDir() . '/' . $this->image;
    }

    /**
     * @return string the relative path of image starting from web directory 
     */
    public function getWebPath() {
        return NULL === $this->image ? '/images/post-default-img.jpg' : '/' . $this->getUploadDir() . '/' . $this->image;
    }

    /**
     * @return string the path of upload directory starting of root
     */
    public function getUploadRootDir() {
        // the absolute directory path where uploaded documents should be saved
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    /**
     * @param $width the desired image width
     * @param $height the desired image height
     * @return string the htaccess file url pattern which map to timthumb url
     */
    public function getTimThumbUrl($width = 50, $height = 50) {
        return NULL === $this->image ? '/images/post-default-img.jpg' : "$this->image";
    }

    /**
     * @return string the document upload directory path starting from web folder
     */
    private function getUploadDir() {
        return 'images/posts-images';
    }
    
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
     * Set title
     *
     * @param string $title
     * @return Post
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Post
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Post
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set category
     *
     * @param \Iti\BlogBundle\Entity\Category $category
     * @return Post
     */
    public function setCategory(\Iti\BlogBundle\Entity\Category $category)
    {
        $this->category = $category;
    
        return $this;
    }

    /**
     * Get category
     *
     * @return \Iti\BlogBundle\Entity\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Add tags
     *
     * @param \Iti\BlogBundle\Entity\Tag $tags
     * @return Post
     */
    public function addTag(\Iti\BlogBundle\Entity\Tag $tags)
    {
        $this->tags[] = $tags;
    
        return $this;
    }

    /**
     * Remove tags
     *
     * @param \Iti\BlogBundle\Entity\Tag $tags
     */
    public function removeTag(\Iti\BlogBundle\Entity\Tag $tags)
    {
        $this->tags->removeElement($tags);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Add comments
     *
     * @param \Iti\BlogBundle\Entity\Comment $comments
     * @return Post
     */
    public function addComment(\Iti\BlogBundle\Entity\Comment $comments)
    {
        $this->comments[] = $comments;
    
        return $this;
    }

    /**
     * Remove comments
     *
     * @param \Iti\BlogBundle\Entity\Comment $comments
     */
    public function removeComment(\Iti\BlogBundle\Entity\Comment $comments)
    {
        $this->comments->removeElement($comments);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComments()
    {
        return $this->comments;
    }
}