<?php
try {
    // Load Config and Startup global variables
    require_once '../config/config.php';
    // Autoload Core Libraries
    spl_autoload_register(function ($className) {
        require_once '../lib/' . $className . '.php';
    });
} catch (Exception $ex) {
    die($ex->getMessage());
}
  
