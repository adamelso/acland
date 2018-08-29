<?php

namespace App\Entity;

use FOS\UserBundle\Model\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_account")
 */
class UserAccount extends User
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Message", mappedBy="author")
     */
    private $messages;

    /**
     * @return Message[]
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
