<?php
/**
 * Created by PhpStorm.
 * User: sankaran.m
 * Date: 03-03-2019
 * Time: 19:23
 */

class Output {
    private $header;
    private $footer;

    /**
     * Output constructor.
     */
    public function __construct() {
        global $CONF;
        $this->header = $CONF->APPROOT . '/api/views/layouts/header.php';
        $this->footer = $CONF->APPROOT . '/api/views/layouts/footer.php';
    }

    /**
     * Include the header
     */
    public function header() {
        if (file_exists($this->header)) {
            require($this->header);
        }
        return;
    }

    /**
     * Include the footer
     */
    public function footer() {
        if (file_exists($this->footer)) {
            require($this->footer);
        }
        return;
    }

    /**
     * Flash message
     */
    public function flash($name = '', $message = '', $class = 'alert alert-success') {
        global $CONF;
        require_once($CONF->APPROOT . '/helpers/session.php');
        flash($name, $message, $class);
    }

    /**
     * Redirect
     */
    function redirect($page) {
        global $CONF;
        require_once($CONF->APPROOT . '/helpers/url.php');
        redirect($page);
    }

}