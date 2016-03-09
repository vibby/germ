<?php

namespace GermBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use GermBundle\Entity\Person;

/**
 * @ORM\Entity
 * @ORM\Table(name="account")
 */
class Account extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Person", inversedBy="account")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id", nullable=false, onDelete="cascade")
     * @Assert\Type(type="GermBundle\Entity\Person")
     * @Assert\Valid()
     */
    protected $person;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    public function getPerson()
    {
        return $this->person;
    }

    public function setPerson(Person $person = null)
    {
        $this->person = $person;
    }
}
