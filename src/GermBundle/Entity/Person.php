<?php

namespace GermBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use \Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Person
 *
 * @ORM\Table(name="person")
 * @ORM\Entity(repositoryClass="GermBundle\Repository\PersonRepository")
 */
class Person
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
     * @ORM\Column(name="first_name", type="string", nullable=true)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", nullable=false)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", nullable=true)
     */
    private $email;

    /**
     * @var array
     *
     * @ORM\Column(name="phone", type="simple_array", nullable=true)
     */
    private $phone;

    /**
     * @var array
     *
     * @ORM\Column(name="address", type="string", nullable=true)
     */
    private $address;

    /**
     * @var array
     *
     * @ORM\Column(name="birth_date", type="date", nullable=true)
     */
    private $birthDate;


    /**
     * @ORM\OneToMany(targetEntity="Person", mappedBy="family")
     */
    protected $children;

    /**
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $family;

    /**
    * @ORM\OneToOne(targetEntity="Account", mappedBy="person", cascade={"persist"})
    */
    private $account;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public function __tostring()
    {
        return $this->getName();
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
     * Get lastName
     *
     * @return array
     */
    public function getName()
    {
        return $this->getFirstName().' '.$this->getLastName();
    }

    /**
     * Set lastName
     *
     * @param array $lastName
     *
     * @return Person
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return array
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set firstName
     *
     * @param array $firstName
     *
     * @return Person
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return array
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set email
     *
     * @param array $email
     *
     * @return Person
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return array
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param array $phone
     *
     * @return Person
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return array
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set address
     *
     * @param array $address
     *
     * @return Person
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return array
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Get account
     *
     * @return array
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Get account
     *
     * @return array
     */
    public function setAccount(Account $account)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Set birth date
     *
     * @param \DateTime $birthDate
     *
     * @return Person
     */
    public function setBirthDate(\DateTime $birthDate = null)
    {
        $this->birthDate = $birthDate;
    }

    /**
     * Get birth date
     *
     * @return array
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    public function getFamily() {
        return $this->family;
    }

    public function setFamily(Person $family) {
        $this->family = $family;
    }

    public function getChildren() {
        return $this->children;
    }

    public function addChild(Person $child) {
       $this->children[] = $child;
       $child->setFamily($this);
    }

    public function setParent(Person $family) {
       $this->getFamily = $family;
    }
}

