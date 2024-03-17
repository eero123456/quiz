<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

setcookie("PHPSESSID", "", time() - 1, "/");
header("Location:/");


