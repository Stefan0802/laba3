<?php

require_once 'db.php';
require_once 'User.php';

session_start();

if(!isset($_SESSION['username'])){
    header('Location: login.php');
}


session_destroy();

header('Location: login.php');

