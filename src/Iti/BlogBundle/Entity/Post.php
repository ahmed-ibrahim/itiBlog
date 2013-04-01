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
     * @Assert\NotBlank()
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

    public function __construct() {
        $this->createdAt = new \DateTime();
        $this->comments = new ArrayCollection();
        $this->tags = new ArrayCollection();
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