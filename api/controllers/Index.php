<?php
/**
 * Created by PhpStorm.
 * User: sankaran.m
 * Date: 12-02-2019
 * Time: 19:00
 */
class Index extends Controller {
    public function __construct(){
    }

    public function index(){
        $data = [
            'title' => '<h1 class="display-3">CORE FRAMEWORK</h1><br>',
            'description' => '<p>Welcome to CORE framework, This is MVC Framework built on PHP.</p>
                                Read the document for use.<br>'
        ];

        $this->view('index/index', $data);
    }

    public function about(){
        $data = [
            'title' => '<h1 class="display-3">ABOUT CORE</h1><br>',
            'description' => '<p>CORE framework for PHP, This is MVC Framework built on PHP & integrated with MySQL DB.</p>
                              Read the document for use. <b>(Open Source)</b>'
        ];

        $this->view('index/about', $data);
    }
}