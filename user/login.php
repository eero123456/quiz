<?php


require("../partials/header.php");

$errors = [];

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    renderForm();
    exit;
}

include("../db/conn.php");


if (!isset($_POST["username"]) || empty($_POST["username"])) {
    $errors[] = "Username missing";

}

if (!isset($_POST["password"]) || empty($_POST["password"])) {
    $errors[] = "Password missing";

}

$username = $_POST["username"];
$password = $_POST["password"];

if (count($errors) > 0) {
    renderForm();
    exit;
}


$id = loginUser($username, $password);

if ($id > 0) {
    session_start();
    $_SESSION["userID"] = $id;
    $_SESSION["username"] = $username;
    header("Location:/");
    exit;
} else {
    $errors[] = "Tunnus tai salasana ei täsmää";
    renderForm();

}



function loginUser($username, $password)
{

    global $db;
    $sql = "SELECT id,password FROM users WHERE username=? LIMIT 1";

    $stmt = $db->prepare($sql);

    $stmt->execute([$username]);

    $userData = $stmt->get_result()->fetch_row();
    if ($userData === null) {
        return 0;
        //die("could not retrieve user data");
    }
    $correctHash = $userData[1];
    if (!$userData) {
        //die("could not retrieve user data");
        return 0;
    }

    if (password_verify($password, $correctHash)) {
        //echo "login ok";
        //die();
        return $userData[0];
    }

    return 0;


}





function renderForm()
{
    global $errors;

    ?>

    <form method="POST" class="user-form">

        <fieldset>
            <legend>Kirjautuminen</legend>

            <label for="username">Käyttäjätunnus</label>
            <input id="username" name="username" required minlength="6">

            <label for="password">Salasana</label>
            <input id="password" type="password" name="password" required minlength="8">

            <?php foreach ($errors as $error): ?>
                <p class="error">
                    <?= $error ?>
                </p>

            <?php endforeach ?>

            <button type="submit">Kirjaudu</button>
        </fieldset>

        <div class="form-message">
            <p>Eikö sinulla ole käyttäjätiliä? <a href='register.php'>Rekisteröidy tästä</a></p>
        </div>
    </form>

    <?php
}