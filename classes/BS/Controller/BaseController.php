<?php
/**
 * Created by PhpStorm.
 * User: karmis
 * Date: 01.10.14
 * Time: 7:36
 */

namespace BS\Controller;

use BS\Common\Request;
use BS\Common\Response;



class BaseController {
    protected $request;
    protected $response;

    public function __construct($request)
    {
        $this->response = new Response();
        $this->request = new Request($request);
    }
} 