<?php
/**
 * Created by PhpStorm.
 * User: sankaran.m
 * Date: 03-03-2019
 * Time: 19:18
 */

require_once('Output.php');
require_once('Database.php');
class Setup {
    /**
     * Setup constructor.
     */
    public function __construct() {
        global $CONF, $OUTPUT, $DB;
        $OUTPUT = new Output();
        $DB = new Database();
    }
}