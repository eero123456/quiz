<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/style.css">
    <script src="/js/asd.js" defer></script>
    <title>
        <?= $title ?? "Otsikko"; ?>
    </title>
</head>

<body>

    <header>Sivusto X</header>


    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    ?>

    <label class="menu-toggle" for="menu-btn">
        <div class="bar1"></div>
        <div class="bar2"></div>
        <div class="bar3"></div>
    </label>

    <input class="menu-toggle" type="checkbox" id="menu-btn" hidden />

    <nav id="nav-menu">

        <a href="/index.php">Etusivu</a>
        <a href="/quiz/edit.php">Luo uusi kysely</a>
        <a href="/debug.php">dev info</a>

        <?php


        if (!isset($_SESSION["userID"])) {

            echo '<a class="nav-left" href="/user/login.php">Login</a>';
            echo '<a href="/user/register.php">Register</a>';
        } else {
            echo '<a href="/user/profile.php">Profile</a>';
            echo '<a class="nav-left" href="/user/logout.php">Logout</a>';
        }

        ?>

    </nav>

    <?php 

if (isset($_SESSION["username"])) {
    $user=$_SESSION['username'];
    echo "<span style='text-align:center;'>Kirjautuneena käyttäjänä $user </span>";
}

    ?>