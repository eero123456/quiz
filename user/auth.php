<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Get authenticated user
 * @return int userID or 0 
 */

function getUserID(): int
{

    if (!isset($_SESSION["userID"])) {
        return 0;
    }

    return (int) $_SESSION["userID"];

}
/**
 * Get authenticated user or redirect
 * @param string redirectURL redirect to login page unless another url is given
 * @return int userID
 */
function requireUserID(string $redirectURL = "/user/login.php")
{
    $userID = getUserID();
    if ($userID == 0) {
        header("Location:$redirectURL");
        exit;
    }

    return $userID;

}


function getUserName()
{

    if (!isset($_SESSION["username"])) {
        return null;
    }

    return $_SESSION["username"];
}