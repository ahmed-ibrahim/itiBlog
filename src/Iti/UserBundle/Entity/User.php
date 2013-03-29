<?php

namespace Iti\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

/**
 * User
 * @UniqueEntity(fields={"useremail"}, groups={"signup", "edit"})
 * @UniqueEntity(fields={"username"}, groups={"signup", "edit"})
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Iti\UserBundle\Entity\UserRepository")
 * @ORM\HasLifecycleCallbacks
 */
class User implements UserInterface {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="\Iti\UserBundle\Entity\Role")
     * @ORM\JoinTable(name="user_role",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)},
     *     inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)}
     * )
     * @var \Doctrine\Common\Collections\ArrayCollection $userRoles
     */
    private $userRoles;

    /**
     * @var string
     * @Assert\NotNull(groups={"signup", "edit"})
     * @ORM\Column(name="username", type="string", length=255)
     */
    private $username;

    /**
     * @var string
     * @Assert\NotNull(groups={"signup", "edit"})
     * @Assert\Email(groups={"signup", "edit"})
     * @ORM\Column(name="useremail", type="string", length=255)
     */
    private $useremail;

    /**
     * @var string
     * @Assert\MinLength(limit=6, groups={"signup", "edit"})
     * @Assert\NotNull(groups={"signup"})
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string $salt
     * @ORM\Column(name="salt", type="string", length=255)
     */
    private $salt;

    /**
     * @var string $confirmationCode
     *
     * @ORM\Column(name="confirmationCode", type="string", length=255)
     */
    private $confirmationCode;

    /**
     * Constructor
     */
    public function __construct() {
        $this->userRoles = new \Doctrine\Common\Collections\ArrayCollection();
        $this->confirmationCode = md5(uniqid(rand()));
        $this->salt = md5(time());
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username) {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password) {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * this function will set a valid random password for the user
     */
    public function setRandomPassword() {
        $this->setPassword(rand());
    }

    /**
     * this function will set the valid password for the user
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function setValidPassword() {
        //check if we have a password
        if ($this->getPassword()) {
            //hash the password
            $this->setPassword($this->hashPassword($this->getPassword()));
        } else {
            //check if the object is new
            if ($this->getId() === NULL) {
                //new object set a random password
                $this->setRandomPassword();
                //hash the password
                $this->setPassword($this->hashPassword($this->getPassword()));
            }
        }
    }

    /**
     * this function will hash a password and return the hashed value
     * the encoding has to be the same as the one in the project security.yml file
     * @param string $password the password to return it is hash
     */
    private function hashPassword($password) {
        //create an encoder object
        $encoder = new MessageDigestPasswordEncoder('sha1', false, 1);
        //return the hashed password
        return $encoder->encodePassword($password, $this->getSalt());
    }

    public function eraseCredentials() {
        //remove the user password
        $this->setPassword(NULL);
    }

    public function getRoles() {
        return $this->getUserRoles()->toArray();
    }

    public function getSalt() {
        return $this->salt;
    }

    public function __toString() {
        return $this->username;
    }

    /**
     * Add userRoles
     *
     * @param \Iti\UserBundle\Entity\Role $userRoles
     * @return User
     */
    public function addUserRole(\Iti\UserBundle\Entity\Role $userRoles) {
        $this->userRoles[] = $userRoles;

        return $this;
    }

    /**
     * Remove userRoles
     *
     * @param \Iti\UserBundle\Entity\Role $userRoles
     */
    public function removeUserRole(\Iti\UserBundle\Entity\Role $userRoles) {
        $this->userRoles->removeElement($userRoles);
    }

    /**
     * Get userRoles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserRoles() {
        return $this->userRoles;
    }

    /**
     * Set useremail
     *
     * @param string $useremail
     * @return User
     */
    public function setUseremail($useremail) {
        $this->useremail = $useremail;

        return $this;
    }

    /**
     * Get useremail
     *
     * @return string 
     */
    public function getUseremail() {
        return $this->useremail;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt) {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Set confirmationCode
     *
     * @param string $confirmationCode
     * @return User
     */
    public function setConfirmationCode($confirmationCode) {
        $this->confirmationCode = $confirmationCode;

        return $this;
    }

    /**
     * Get confirmationCode
     *
     * @return string 
     */
    public function getConfirmationCode() {
        return $this->confirmationCode;
    }

}