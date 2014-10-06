<?php
/**
 * Created by PhpStorm.
 * User: karmis
 * Date: 01.10.14
 * Time: 6:24
 */
namespace BS\Common;

/**
 * @property mixed res
 */
class Response {
    private $data;
    private $code;
    private $message;
    private $status;

    public function response($status='error', $code=null, $message='')
    {
        $response = $this->toJSON($status, $code, $message);
        header("Content-Type: application/json; charset=UTF-8");
        echo $response;
    }


    protected function toJSON($status, $code, $message)
    {
        $json = array(
            "status" => $status,
            "code" => $code,
            "message" => $message,
        );
        return json_encode($json, ENT_NOQUOTES);
    }

}