<?php
require_once 'classes/BS/Common/Autoload.php';

use BS\Controller\UserController;
use BS\Entity\UserEntity;

session_start();
if (!empty($_COOKIE['sid'])) {
    session_id($_COOKIE['sid']);
}

if (isset($_POST['act'])) {
    $controller = new UserController($_REQUEST);
} else {
    $action = UserEntity::getAction();
    $isAuth = UserEntity::isAuthorized();

    $tpl = '';
    if ($isAuth == true) {
        $user = new UserEntity();
        $user = $user->getUser();
        $tpl = 'tpl/content.tpl.html';
    } else {
        if ($action == 'register') {
            $tpl = 'tpl/register.tpl.html';
        } else {
            $tpl = 'tpl/login.tpl.html';
        }
    }

    require_once('tpl/base.tpl.html');
}


?>
