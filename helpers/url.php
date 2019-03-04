<?php
// Page redirect
function redirect($page) {
    global $CONF;
    header('location: ' . $CONF->URLROOT . '/' . $page);
}