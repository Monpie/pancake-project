<?php
// src/PancakeBundle/Entity/Enquiry.php

namespace PancakeBundle\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

class Enquiry
{
    protected $name;

    protected $email;

    protected $subject;

    protected $body;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }


    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('name', new Assert\NotBlank());

        $metadata->addPropertyConstraint('email', new Assert\Email(array(
            'message' => 'l email "{{ value }}" n est pas valide.',
            'checkMX' => true,
        )));

        $metadata->addPropertyConstraint('subject', new Assert\NotBlank());
        $metadata->addPropertyConstraint('subject', new Assert\Length(array('min'=>0,
                 'max'=>50,
                 'minMessage' => 'Les charactères sont limités à {{ limit }} minimum',
            'maxMessage' => 'Les charactères sont limités à {{ limit }} maximum',)));

        $metadata->addPropertyConstraint('body', new Assert\Length(array('min'=>0,
                 'max'=>50,
             'minMessage' => 'Les charactères sont limités à {{ limit }} minimum',
            'maxMessage' => 'Les charactères sont limités à {{ limit }} maximum',)));
    }
}