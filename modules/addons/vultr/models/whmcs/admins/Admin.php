<?php
namespace MGModule\vultr\models\whmcs\admins;

use MGModule\vultr as main;

/**
 * Description of Admin
 *
 * @Table(name=tbladmins,preventUpdate,prefixed=false)
 */
class Admin extends main\mgLibs\models\Orm
{
    /**
     *
     * @Column(id)
     * @var int
     */
    protected $id;

    /**
     *
     * @Column(name=roleid,as=roleId)
     * @var int
     */
    protected $roleId;

    /**
     *
     * @Column(name=username)
     * @var int
     */
    protected $username;

    /**
     *
     * @Column(name=firstname,as=firstName)
     * @var int
     */
    protected $firstName;
    /**
     *
     * @Column(name=lastname,as=lastName)
     * @var int
     */
    protected $lastName;

    /**
     *
     * @Column(name=email)
     * @var int
     */
    protected $email;

    public function getId()
    {
        return $this->id;
    }

    public function getRoleId()
    {
        return $this->roleId;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function getEmail()
    {
        return $this->email;
    }
}
