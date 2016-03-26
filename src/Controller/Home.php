<?php


namespace App\Controller;

use \Core\Controllers\Controller;
use \Core\Controllers\Response;
use \Core\Models\Entity\Users;
use \Core\Controllers\Request;
use \Core\Models\Lists\Messages;
use \Core\Models\Entity\Posts;
use \Core\Models\Entity\Comments;
use \Core\Models\Entity\FriendsList;
use \Core\Models\Entity\DilogList;

class Home extends Controller
{

    protected $autification = true;

    public function start()
    {
        echo 'word';
        $user = new Users();

        deBug($user, '$user', 0, 0, 0, __FILE__, __LINE__, __METHOD__); // [debugBacktrace], dump, echo, stop
    }

    public function index()
    {

        $email = (isset(Request::get('request')['email']))? Request::get('request')['email']: false ;
        $password = (isset(Request::get('request')['password']))? Request::get('request')['password']: false ;
        $user = new Users();

        $user = $user->login($email, $password);
        if ($user!= false) {
                $_SESSION['user'] = serialize($user);
        }
        if ($_POST['remember'] == 'on') {
            $this->rememberUser();
        }


        Response::template('homePage', ['message' => 'Hello World']);
    }

    public function login()
    {
        if (isset($_SESSION['user']) && !empty(isset($_SESSION['user']) && $_SESSION['user'])) {
            $user = unserialize($_SESSION['user']);
            $str = 'Location: http://train.local/id'.$user->ID();
            header($str);
        }


        $message = 'Authentifation failed';
        $email = (isset(Request::get('request')['email']))? Request::get('request')['email']: false ;
        $password = (isset(Request::get('request')['password']))? Request::get('request')['password']: false ;
        $user = new Users();
        if ($email && $password) {
            $user = $user->login($email, $password);
            if ($user!= false) {
                $_SESSION['user'] = serialize($user);
                $str = 'Location: http://train.local/id'.$user->ID();
                header($str);
            } else {
                Response::template('login', ['message', $message]);
            }
        } else {
            Response::template('login', ['message', $message]);
        }
    }

    public function registration()
    {
        Response::template('registration');
    }

    public function accept()
    {
        $email = isset(Request::get('request')['email'])? Request::get('request')['email'] : false;
        $password = isset(Request::get('request')['password']) ? Request::get('request')['password'] : false;
        $firstName = isset(Request::get('request')['firstName']) ? Request::get('request')['firstName'] : false;
        $lastName = isset(Request::get('request')['firstName']) ? Request::get('request')['lastName'] : false;
        $avatar = isset($_FILES['avatar']) ? $_FILES['avatar'] : false;
        $description = isset(Request::get('request')['description']) ? Request::get('request')['description'] : false;

        if (empty($email) and empty($password)) {
            $message = 'Fields password and email are blank';
        }

        if (!Users::checkEmail($email)) {
            $message = 'Email already exist';
        } else {
            $user = new Users();
            $user->setFirstName($firstName)
                ->setLastName($lastName)
                ->setEmail($email)
                ->setPassword($password)
                ->setDescription($description)
                ->setCreateDate()->setFriendsList(0);
                $user->save();
            if (empty($avatar)) {
                $user->uploadAvatar($avatar);
            } else {
                $user->defaultAvatar();
            }
            $user->save();
            $message = 'User was created';
        }
        Response::template('accept', ['message' => $message]);
    }

    public function about()
    {
        $this->view->render('aboutPage', ['message' => 'Страница о нас']);
    }

    public function pageNotFound()
    {
        Response::template('404', ['message' => 'Все сообщения']);
    }

    public function messageShow()
    {

        $user = unserialize($_SESSION['user']);
        $user->getAllDialogs();
        Response::template('message', ['message' => 'Все сообщения', 'user' => $user]);

    }

    public function userChat()
    {
        $dialog = Request::get('url')['dialog'];
        $user = unserialize($_SESSION['user']);
        $user->dialogs = $dialog;
        $dilog = DilogList::fetchByID($dialog);
        $_SESSION['user'] = serialize($user);

        Response::template('messageShow', ['message' => 'Все сообщения', 'user' => $user]);

        if (isset(Request::get('request')['message']) && !empty(Request::get('request')['message'])) {
            $text = Request::get('request')['message'];
            $user->sendMessage($text, $userTo);
        }

    }

    public function showId($id)
    {

        if (!isset($_SESSION['user']) or empty($_SESSION['user'])) {
            $user = unserialize($_SESSION['user']);
            $str = 'Location: http://train.local/login';
            header($str);
        }

        $id = Request::get('url')['id'];

        $user = Users::fetchByID($id);

        if (isset(Request::get('query')['action']) && !empty(Request::get('query')['action']) && Request::get('query')['action'] === 'add-comment') {
            $postID = Request::get('request')['post-id'];
            $content = Request::get('request')['comment'];
            $userId = $user->ID();
            $comment = new Comments();
            $comment->setPostID($postID)
                ->setUserId($userId)
                ->setContent($content)
                ->save();
        }

        if (isset(Request::get('query')['action']) && !empty(Request::get('query')['action']) && Request::get('query')['action'] === 'post-rate') {
            $postID = Request::get('request')['post-id'];
            $postRate = Request::get('request')['rate'];
            $post = Posts::fetchByID($postID);
            $postLikes = $post->likes();
            $postDisLikes = $post->disLikes();
            if ($postRate == 'like') {
                $postLikes ++;
                $post->setLikes($postLikes);
            } elseif ($postRate == 'dislike') {
                $postDisLikes++;
                $post->setDisLikes($postDisLikes);
            }
            $post->save();
        }

        $posts = Posts::getUserPost($user->ID(), 10, 'CreatedDate', 'DESC');

        if (isset(Request::get('request')['title'])) {
            $title = Request::get('request')['title'];
            $content = Request::get('request')['content'];
            $type = Request::get('request')['type'];
            $newPost = new Posts();
            $newPost->setUserId($user->ID())
                    ->setTitle($title)
                    ->setType($type)
                    ->setContent($content)
                    ->setCreatedDate()
                    ->setLikes(0)
                    ->setDisLikes(0)
                    ->save();
        }
        Response::template('userProfile', ['user' => $user, 'posts'=>$posts, 'id' => $id]);
    }

    public function addFriend()
    {
        if (!isset($_SESSION['user']) or empty($_SESSION['user'])) {
            $user = unserialize($_SESSION['user']);
            $str = 'Location: http://train.local/login';
            header($str);
        }
            $message = "your add friend";
            $user = unserialize($_SESSION['user']);
            $friendId = !empty(Request::get('query')['user']) ? Request::get('query')['user'] : false;
            $friend = new FriendsList();
            $friend->setID($user->ID());
            $friend->setFriendID($friendId);
            $friend->setCreatedDate();
            $friend->rawSave();
            Response::template('addFriend', ['message' => $message, 'user' => $user, 'friend' => $friend]);
    }

    public function showAllUsers()
    {
        $user = unserialize($_COOKIE['user']);

        $users = $user->getUsersForAdd();
        Response::template('allUsers', ['users' => $users]);
    }

    public function editProfile()
    {
        $this->view->render('editProfile', ['id' => $this->params['id'], 'name' => $this->params['name']]);
    }

    public function showFriends()
    {
        if (isset(Request::get('url')['id']) && !empty(Request::get('url')['id'])) {
            $id = Request::get('url')['id'];
        }
        $user = Users::fetchByID($id);
        $user->getAllFriends();
        Response::template('friends', ['user' => $user]);
    }

    public function logout()
    {
        session_destroy();
        $str = 'Location: http://train.local/login';
        header($str);
    }
}
