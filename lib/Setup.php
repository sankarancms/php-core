<?php
/**
 * Created by PhpStorm.
 * User: sankaran.m
 * Date: 03-03-2019
 * Time: 19:18
 */

define('CORE', true);
require_once('../config/config.php');
require_once('Output.php');
class Setup {
    /**
     * Setup constructor.
     * To setup global variables
     */
    public function __construct() {
        global $CONF, $OUTPUT, $DB;
        $CONF->libdir = $CONF->APPROOT . '/lib';
        $OUTPUT = new Output();
        require_once($CONF->libdir .'/database/' . $CONF->DB_TYPE . '/Database.php');
        $DB = new Database();
    }
}