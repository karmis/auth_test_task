<?php
/**
 * Created by PhpStorm.
 * User: karmis
 * Date: 01.10.14
 * Time: 5:55
 */
namespace BS\Controller;

use BS\Entity\UserEntity;

class UserController extends BaseController
{
    public function __construct($request)
    {
        parent::__construct($request);
        $action = $this->request->getRequestParam("act");
        $methodName = $action . 'Action';
        if (method_exists($this, $methodName)) {
            call_user_func(array(&$this, $methodName), $_POST);
        } else {
            $this->response->response("error", 500, "Неверный запрос! Попытка вызвать несуществующий метод.");
        }
    }

    private function registerAction($post)
    {
        setcookie("sid", "");

        $username = $this->request->getRequestParam("username");
        $password = $this->request->getRequestParam("password");
        $passwordRepeat = $this->request->getRequestParam("password_repeat");
        $email = $this->request->getRequestParam("email");
        $news = $this->request->getRequestParam("news");
        $avatar = $this->request->getRequestParam("avatar");

        if (empty($username)) {
            $this->response->response("validate", 500, "Введите имя пользователя");
            return;
        }

        if (empty($password)) {
            $this->response->response("validate", 500, "Введите пароль");
            return;
        }

        if (empty($passwordRepeat)) {
            $this->response->response("validate", 500, "Введетие подтверждение пароля");
            return;
        }

        if (empty($email)) {
            $this->response->response("validate", 500, "Введите email");
            return;
        }

        if ($password !== $passwordRepeat) {
            $this->response->response("validate", 500, "Введенные пароли не совпадают");
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->response->response("validate", 500, "Введен некорректный email");
            return;
        }



        $user = new UserEntity();
        if (!empty($_FILES['avatar'])) {
            $avatarPath = $user->upload($_FILES['avatar']);
        } else {
            $avatarPath = null;
        }

        try {
            $new_user_id = $user->create($username, $password, $email, $news, $avatarPath);
        } catch (\Exception $e) {
            $this->response->response("username", $e->getMessage());
            return;
        }
        $user->authorize($username, $password);

        $this->response->response("success", 200, "ok");
    }

    private function loginAction($post)
    {
        setcookie("sid", "");
        $username = $this->request->getRequestParam("username");
        $password = $this->request->getRequestParam("password");
        $remember = !!$this->request->getRequestParam("remember-me");

        if (empty($username)) {
            $this->response->response('validate', 500, 'Введите имя пользователя');
            return;
        }

        if (empty($password)) {
            $this->response->response('validate', 500, 'Введите пароль');
            return;
        }

        $user = new UserEntity();
        $isAuth = $user->authorize($username, $password, $remember);

        if (!$isAuth) {
            $this->response->response('error', 500, 'Неверное имя пользователя или пароль');
            return;
        }

        $this->response->response('success', 200, 'ok');
    }

    private function logoutAction()
    {
        setcookie("sid", "");
        $user = new UserEntity();
        $user->logout();
        $this->response->response('success', 200, 'ok');
    }


}