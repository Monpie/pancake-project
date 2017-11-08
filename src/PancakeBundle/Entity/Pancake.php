<?php

namespace PancakeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pancake
 *
 * @ORM\Table(name="pancake")
 * @ORM\Entity(repositoryClass="PancakeBundle\Repository\PancakeRepository")
 */
class Pancake
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="price", type="decimal", scale=2)
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255)
     */
    private $image;

    /**
     * @var bool
     *
     * @ORM\Column(name="avaibility", type="boolean")
     */
    private $avaibility;


     /**
     * @var bool
     *
     * @ORM\Column(name="promotion", type="boolean")
     */
    private $promotion;


     /**
     * @var bool
     *
     * @ORM\Column(name="pancake", type="boolean")
     */
    private $pancake;


    

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
     * @return Pancake
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
     * Set price
     *
     * @param integer $price
     *
     * @return Pancake
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Pancake
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
     * Set image
     *
     * @param string $image
     *
     * @return Pancake
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set avaibility
     *
     * @param boolean $avaibility
     *
     * @return Pancake
     */
    public function setAvaibility($avaibility)
    {
        $this->avaibility = $avaibility;

        return $this;
    }

    /**
     * Get avaibility
     *
     * @return bool
     */
    public function getAvaibility()
    {
        return $this->avaibility;
    }



       

    /**
     * Set avaibility
     *
     * @param boolean $promotion
     *
     * @return Pancake
     */
    public function setPromotion($promotion)
    {
        $this->promotion = $promotion;

        return $this;
    }


    /**
     * Get promotion
     *
     * @return bool
     */
    public function getPromotion()
    {
        return $this->promotion;
    }




    /**
     * Set avaibility
     *
     * @param boolean $pancake
     *
     * @return Pancake
     */
    public function setPancake($pancake)
    {
        $this->pancake = $pancake;
    }

   




    /**
     * Get pancake
     *
     * @return bool
     */
    public function getPancake()
    {
        return $this->pancake;
    }

}
