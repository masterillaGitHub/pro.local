<?php

namespace Core\Models\Traits;

use \Core\Models\Entity\User;

trait UserDependence
{
    protected $_userID;

    private $user = null;

    public function userID()
    {
        return $this->_userID;
    }

    public function setUserID($userID)
    {
        $this->_userID = $userID;

        return $this;
    }

    public function user()
    {
        if (empty($this->user)) {
            $this->user = User::fetchByID($this->_userID);
        }

        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;

        $this->_userID = $user->ID();

        return $this;
    }
}
