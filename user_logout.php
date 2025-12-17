<?php
session_start();
unset($_SESSION['user_id'], $_SESSION['user_username'], $_SESSION['user_email']);
header('Location: index.php');
exit;
