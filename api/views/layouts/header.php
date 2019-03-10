<?php
global $CONF;
echo '<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="' . $CONF->URLROOT . '/assets/css/style.css">
    <link rel="stylesheet" href="' . $CONF->URLROOT . '/assets/css/bootstrap.min.css">
  <title>' . $CONF->SITENAME .'</title>
</head>
<body>
<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
      <a class="navbar-brand" href="#">CORE</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item active">
            <a class="nav-link" href=" ' . $CONF->URLROOT . '/dashboard">Dashboard <span class="sr-only">(current)</span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href=" ' . $CONF->URLROOT . '/index/about">About</a>
          </li>
        </ul>
        <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <a class="nav-link" href=" ' . $CONF->URLROOT . '/users/login">Login</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href=" ' . $CONF->URLROOT . '/users/register">Register</a>
          </li>
        </ul>
      </div>
    </nav>
     <main role="main">';
