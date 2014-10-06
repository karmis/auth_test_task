<?php
/**
 * Created by PhpStorm.
 * User: karmis
 * Date: 01.10.14
 * Time: 7:46
 */

namespace BS\Entity;

use BS\Common\Config;
use BS\Entity\BaseEntity;

class UserEntity extends BaseEntity
{
    private $username;
    private $user_id;

    private $is_authorized = false;

    public function __construct()
    {
        parent::__construct();
    }

    public static function isAuthorized($bool = true)
    {
        if (!empty($_SESSION["user_id"])) {
            if ($bool) {
                return (bool)$_SESSION["user_id"];
            } else {
                return $_SESSION["user_id"];
            }

        }
        return false;
    }

    public static function getAction()
    {
        return !empty($_GET['action']) ? $_GET['action'] : 'login';
    }

    public function passwordHash($password, $salt = null, $iterations = 10)
    {
        $salt || $salt = uniqid();
        $hash = md5(md5($password . md5(sha1($salt))));

        for ($i = 0; $i < $iterations; ++$i) {
            $hash = md5(md5(sha1($hash)));
        }

        return array('hash' => $hash, 'salt' => $salt);
    }

    public function getSalt($username)
    {
        $query = "select salt from users where username = :username limit 1";
        $sth = $this->db->prepare($query);
        $sth->execute(
            array(
                ":username" => $username
            )
        );
        $row = $sth->fetch();
        if (!$row) {
            return false;
        }
        return $row["salt"];
    }

    public function getEmail($email)
    {
        $query = "select salt from users where email = :email limit 1";
        $sth = $this->db->prepare($query);
        $sth->execute(
            array(
                ":email" => $email
            )
        );
        $row = $sth->fetch();
        if (!$row) {
            return false;
        }
        return $row["email"];
    }

    public function getUser()
    {
        $id = $this->isAuthorized(false);
        if (false !== $id) {
            $query = "select * from users where id = :id limit 1";
            $sth = $this->db->prepare($query);
            $sth->execute(
                array(
                    ":id" => $id,
                )
            );

            $user = $sth->fetch();
            return $user;
        }
    }

    public function authorize($username, $password, $remember = false)
    {
        $query = "select id, username from users where
            username = :username and password = :password limit 1";
        $sth = $this->db->prepare($query);
        $salt = $this->getSalt($username);

        if (!$salt) {
            return false;
        }

        $hashes = $this->passwordHash($password, $salt);
        $sth->execute(
            array(
                ":username" => $username,
                ":password" => $hashes['hash'],
            )
        );
        $this->user = $sth->fetch();

        if (!$this->user) {
            $this->is_authorized = false;
        } else {
            $this->is_authorized = true;
            $this->user_id = $this->user['id'];
            $this->saveSession($remember);
        }

        return $this->is_authorized;
    }

    public function logout()
    {
        if (!empty($_SESSION["user_id"])) {
            unset($_SESSION["user_id"]);
        }
    }

    public function saveSession($remember = false, $http_only = true, $days = 7)
    {
        $_SESSION["user_id"] = $this->user_id;

        if ($remember) {
            // Save session id in cookies
            $sid = session_id();

            $expire = time() + $days * 24 * 3600;
            $domain = ""; // default domain
            $secure = false;
            $path = "/";

            $cookie = setcookie("sid", $sid, $expire, $path, $domain, $secure, $http_only);
        }
    }

    public function create($username, $password, $email, $news, $avatarPath)
    {
        $user_exists = $this->getSalt($username);
        $email_exists = $this->getSalt($email);

        if ($user_exists) {
            throw new \Exception("Такой пользователь уже существует: " . $username, 1);
        }

        if ($email_exists) {
            throw new \Exception("Такой email уже существует: " . $email, 1);
        }

        $query = "insert into users (username, password, email, news, salt, avatarPath)
            values (:username, :password, :email, :news, :salt, :avatarPath)";
        $hashes = $this->passwordHash($password);
        $sth = $this->db->prepare($query);

        try {
            $this->db->beginTransaction();
            $result = $sth->execute(
                array(
                    ':username' => $username,
                    ':email' => $email,
                    ':news' => $news,
                    ':password' => $hashes['hash'],
                    ':salt' => $hashes['salt'],
                    ':avatarPath' => $avatarPath
                )
            );
            $this->db->commit();
        } catch (\PDOException $e) {
            $this->db->rollback();
            die("Database error: " . $e->getMessage());
        }

        if (!$result) {
            $info = $sth->errorInfo();
            printf("Database error %d %s", $info[1], $info[2]);
            die();
        }

        return $result;
    }

    public function upload($avatar)
    {
        $config = Config::get('file');
        $ext = strtolower(pathinfo($avatar['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $config['exts']) AND $avatar['size'] < $config['max_size']) {
            $path = $config['path'] . uniqid() . '.' . $ext;
            if (move_uploaded_file($avatar['tmp_name'], $path)) {
                return $path;
            }
        } else {
            throw new \Exception("Недопустмый файл: ", 1);
        }
    }
}
