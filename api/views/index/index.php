<?php
/**
 * Created by PhpStorm.
 * User: sankaran.m
 * Date: 03-03-2019
 * Time: 19:09
 */

global $OUTPUT;
$OUTPUT->header();
echo '<div class="jumbotron">
        <div class="container">' . $data['title'];
echo $data['description'] . ' </div>
      </div>';
$OUTPUT->footer();
