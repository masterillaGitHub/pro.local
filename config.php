<?php

return $routes = [
                    '/' => 'Home/start',
                    '/home' => 'Home/index',
                    '/about' => 'Home/about',
                    '/404' => 'Home/pageNotFound',
                    '/id{id}/edit/{name}' => 'Home/editProfile',
                    '/start{id}/edit/{name}' => 'Home/start',
                    '/id{id}' => 'Home/showId',
                    '/login' => 'Home/login',
                    '/registration' => 'Home/registration',
                    '/im' => 'Home/messageShow',
                    '/users' => 'Home/showAllUsers',
                    '/addfriend' => 'Home/addFriend',
                    '/id{id}/friends' => 'Home/showFriends',
                    '/accept' => 'Home/accept',
                    '/logout' => 'Home/logout',
                    '/msg/dialog{dialog}' => 'Home/userChat',
                    '/api/sendmsg' => 'Api/sendMsg',
                    '/api/getmsg' => 'Api/getMSg',
                    '/api/sendpost' => 'Api/sendPost'
               ];
