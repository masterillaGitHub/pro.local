<?php
namespace Core\Models\Entity;

use \Core\Models\EntityModel;
use \Core\Models\Entity\FriendsList;
use \Core\Models\Entity\DilogList;
use \Core\Utils\DB;

/**
*
*/
class Users extends EntityModel
{

    protected $_firstName;
    protected $_lastName;
    protected $_email;
    protected $_password;
    protected $_avatar;
    protected $_createdDate;
    protected $_description;
    protected $_friendsCount;
    protected $friends;
    public $dilogs;

    public function friends()
    {
        return $this->friends;
    }

    public function firstName()
    {
        return $this->_firstName;
    }

    public function lastName()
    {
        return $this->_lastName;
    }

    public function email()
    {
        return $this->_email;
    }

    public function password()
    {
        return $this->_password;
    }

    public function avatar()
    {
        return $this->_avatar;
    }

    public function createdDate()
    {
        return $this->_createdDate;
    }

    public function description()
    {
        return $this->_description;
    }

    public function friendsCount()
    {
        return $this->_friendsCount;
    }

    public function getID()
    {
        $id = parent::ID();
        return $id;
    }

    public function setLastName($lastName)
    {
        $this->_lastName = $lastName;
        return $this;
    }

    public function setFirstName($firstName)
    {
        $this->_firstName = $firstName;
        return $this;
    }

    public function setEmail($email)
    {

        $this->_email = $email;
        return $this;
    }

    public function setPassword($_password)
    {
        $this->_password = $this->hashPassword($_password);
        return $this;
    }

    public function setAvatar($avatar)
    {
        $this->_avatar = $avatar;
        return $this;
    }

    public function setCreateDate()
    {
        $this->_createdDate = date("Y-m-d H:i:s");
        return $this;
    }

    public function setDescription($text)
    {
        $this->_description = $text;
        return $this;
    }

    public function setFriendsList($frinedsId)
    {
        $this->_friendsCount = $frinedsId;
        return $this;
    }

    private function hashPassword($password)
    {
        return sha1($password);
    }

    public function login($email, $password)
    {
        $password = sha1($password);
        $data = [['condition' => 'AND',
                  'name' => 'Email',
                  'value' => $email],
                 ['condition' => 'AND',
                  'name' => 'Password',
                  'value' =>$password],
            ];

        if (!empty(self::search($data))) {
            return self::search($data)[0];
        } else {
            return false;
        }

    }

    public function uploadAvatar($avatar)
    {
        $dir = ROOT_DIR.'web/img/id'.$this->_id;
        mkdir($dir);

        $extantion = pathinfo($avatar['name'], PATHINFO_EXTENSION);
        $file = 'id'.$this->_id.'.'.$extantion;
        $fileUpload = $dir.'/'.$file;
        $this->setAvatar($file);
        move_uploaded_file($avatar["tmp_name"], $fileUpload);
    }

    public function defaultAvatar()
    {
        $dir = ROOT_DIR.'web/img/id'.$this->ID();
        mkdir($dir);
        $file = 'id'.$this->ID();
        $file.= '.jpg';
        $this->_avatar = $file;
        $fileUpload = $dir.'/'.$file;
        $defaultAvatar = ROOT_DIR.'web/img/default.jpg';
        $this->_avatar = $file;
        if (copy($defaultAvatar, $fileUpload)) {
            return true;
        } else {
            return false;
        }

    }

    public static function checkEmail($email)
    {
        $data = [['condition' => 'AND',
                  'name' => 'Email',
                  'value' => $email]
            ];
        if (!empty(self::search($data))) {
            return false;
        } else {
            return true;
        }
    }

    public function addFriend($userId)
    {

        if ($userId == $this->_id) {
            return false;
        }

        if (empty(self::fetchByID($userId))) {
            return false;
        }

        $friend = new FriendsList();
        $friend->setID($this->_id);
        $friend->setFriendID($userId);
        $friend->setCreatedDate();
        $friend->rawSave();

        return $this;
    }

    public function getAllFriends()
    {
        $id = $this->_id;
        $data = [['condition' => 'AND',
                  'name' => 'UserID',
                  'value' => $this->_id]];

        $friendsList = FriendsList::search($data);

        foreach ($friendsList as $key => $friend) {
            $this->friends[] = Users::fetchByID($friend->friendID());
        }
        return $this;
    }

    public function createUser($firstName, $lastName, $email, $password, $avatar, $date, $text)
    {
        $this->setFirstName($firstName)
                ->setLastName($lastName)
                ->setEmail($email)
                ->setAvatar($avatar)
                ->setDescription($text)
                ->save();

    }

    public function sendMessage($text, $toId)
    {
        $message = new Messages;
        $message->setText($text)
                ->setCreatedDate()
                ->setDialogId($this->dilog->ID())
                ->save();
    }

    public function getUsersForAdd()
    {
        $users = Users::fetchAll();
        $this->getAllFriends();
        foreach ($users as $userKey => $user) {

            foreach ($this->friends as $key => $friend) {
                if ($this->ID() == $user->ID() || $friend->ID() == $user->ID()) {
                    unset($users[$userKey]);
                }
            }
        }
        return $users;
    }

    public function getAllDialogs()
    {
        $data = [['condition' => 'AND',
                  'name' => 'FromUserID',
                  'value' => $this->_id],
                 ['condition' => 'OR',
                  'name' => 'ToUserID',
                  'value' => $this->_id],
            ];

        $this->dilogs = DilogList::search($data, 'CreatedDate', 'DESC');
        $this->dilogs[0]->getMsgFromDilog();
        foreach ($this->dilogs as $key => $dilog) {
            $dilog->messages = Messages::getByDialog($dilog->ID());
        }


        return $this;
    }
}
