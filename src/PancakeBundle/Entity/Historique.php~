<?php

namespace PancakeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use PancakeBundle\Entity\Pancake;
use PancakeBundle\Entity\User;

/**
 * Historique
 *
 * @ORM\Table(name="historique")
 * @ORM\Entity(repositoryClass="PancakeBundle\Repository\HistoriqueRepository")
 */
class Historique
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="PancakeBundle\Entity\Pancake")
     * ORM\JoinColumn(nullable=true)
     */
    private $pancakeArray;

    /**
     * @ORM\ManyToOne(targetEntity="PancakeBundle\Entity\User", inversedBy="purchases")
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datetime", type="datetime")
     */
    private $date;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

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
     * Get pancakeArray
     *
     * @return array
     */
    public function getPancakeArray()
    {
        return $this->pancakeArray;
    }

    /**
     * Set pancakeArray
     *
     * @param array() $array
     */
    public function setPancakeArray(Pancake $array)
    {
        $this->pancakeArray = $array;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate(\DateTime $date)
    {
        $this->date = $date;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return Historique
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }
}
