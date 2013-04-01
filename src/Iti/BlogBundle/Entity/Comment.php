<?php

namespace Iti\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Comment
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Iti\BlogBundle\Entity\CommentRepository")
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
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="comment", type="text")
     */
    private $comment;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * the post of the comment
     * @var \Iti\BlogBundle\Entity\Post $post
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="\Iti\BlogBundle\Entity\Post", inversedBy="comments")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $post;
    
    /**
     * the user of the comment
     * @var \Iti\UserBundle\Entity\User $user
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="\Iti\UserBundle\Entity\User", inversedBy="comments")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $user;
    
    public function __construct() {
        $this->createdAt = new \DateTime();
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
     * Set comment
     *
     * @param string $comment
     * @return Comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    
        return $this;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Comment
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
     * Set post
     *
     * @param \Iti\BlogBundle\Entity\Post $post
     * @return Comment
     */
    public function setPost(\Iti\BlogBundle\Entity\Post $post)
    {
        $this->post = $post;
    
        return $this;
    }

    /**
     * Get post
     *
     * @return \Iti\BlogBundle\Entity\Post 
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * Set user
     *
     * @param \Iti\UserBundle\Entity\User $user
     * @return Comment
     */
    public function setUser(\Iti\UserBundle\Entity\User $user)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \Iti\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}