<?php
session_start();


$_SESSION = [];


session_destroy();

header('Location: index.html?msg=logged_out');
exit;

