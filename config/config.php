<?php
/**
 * Created by PhpStorm.
 * User: sankaran.m
 * Date: 12-02-2019
 * Time: 19:00
 */

if (isset($CONF)) {
    return;
}
global $CONF;
$CONF = new stdClass();
// DB Params
$CONF->DB_HOST = 'localhost';
$CONF->DB_USER = 'root';
$CONF->DB_PASS = 'root';
$CONF->DB_NAME = 'core';

// App Root
$CONF->APPROOT = dirname(dirname(__FILE__));
// URL Root
$CONF->URLROOT = 'http://localhost:8080';
// Site Name
$CONF->SITENAME = 'PHPCORE';

// Startup global variables & Set up
require_once('../lib/Setup.php');
new Setup();