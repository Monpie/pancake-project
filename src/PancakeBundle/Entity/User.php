<?php

namespace PancakeBundle\Entity;

use PancakeBundle\Entity\Pancake;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;
use PancakeBundle\Entity\Historique;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="PancakeBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="lastName", type="string", length=255)
     * @Assert\NotBlank(message="Please enter your last name.", groups={"Registration", "Profile"})
     */
    private $lastName;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank(message="Please enter your name.", groups={"Registration", "Profile"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=15, nullable=true)
     */
    private $phone;

    /**
     * @ORM\OneToMany(targetEntity="PancakeBundle\Entity\Historique", mappedBy="user" )
     * @ORM\JoinColumn(nullable=false)
     */
    private $purchases;

    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return User
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
     * Set phone
     *
     * @param string $phone
     *
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Is the user a staff member ?
     * 
     * @return boolean
     */
    public function isStaff()
    {
        if($this->hasRole("ROLE_STAFF"))
        {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Define the user as staff member
     *
     * @param boolean $staff
     */
    public function setStaff($staff)
    {
        if($staff && !$this->hasRole("ROLE_STAFF"))
        {
            $this->addRole("ROLE_STAFF");
        } else {
            $this->removeRole("ROLE_STAFF");
        }
    }

    /**
     * Is the user an admin ?
     * 
     * @return boolean
     */
    public function isAdmin()
    {
        if($this->hasRole("ROLE_ADMIN"))
        {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Define the user as an admin
     *
     * @param boolean $admin
     */
    public function setAdmin($admin)
    {
        if($admin && !$this->hasRole("ROLE_ADMIN"))
        {
            $this->addRole("ROLE_ADMIN");
        } else {
            $this->removeRole("ROLE_ADMIN");
        }
    }

    /**
     * Add purchase
     *
     * @param \PancakeBundle\Entity\Historique $purchase
     *
     * @return User
     */
    public function addPurchase(Historique $purchase)
    {
        $this->purchases[] = $purchase;

        return $this;
    }

    /**
     * Remove purchase
     *
     * @param \PancakeBundle\Entity\Historique $purchase
     */
    public function removePurchase(Historique $purchase)
    {
        $this->purchases->removeElement($purchase);
    }

    /**
     * Get purchases
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPurchases()
    {
        return $this->purchases;
    }
}
