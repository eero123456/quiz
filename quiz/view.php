<?php

if (!isset($_GET['url']) || empty($_GET['url'])) {
    header("Location:/");
    die();
}

require_once "../user/auth.php";


$userID = getUserID();
$username=getUserName();


require("../partials/header.php");

require_once("../db/conn.php");
require_once "../question/base.php";
require_once "queries.php";

$url = $_GET['url'];

$data = getQuizForResponder($url);


if (is_null($data)) {
    header("Location:/");
    die();
}

if (!$data['accepting_answers']) {
    
    echo "<p>Tämä kysely on suljettu eikä siihen voi vastata</p>";
    exit;
}

$questions = json_decode($data['json'], true);


if (count($questions)==0) {
    
    echo "<p>Virhe kyselyssä, kysymyksiä ei ole lisätty.</p>";
    exit;
}

$state;

if (!isset($_SESSION[$url])) {
    $state = createQuestionState($questions);
} else if ($data['owner']==$userID) {
    $state = createQuestionState($questions);
} else {
    loadQuestionState();
}

function loadQuestionState()
{
    global $url;
    global $questions;
    global $state;
    global $data;

    if (!isset($_SESSION[$url]['version']) || $_SESSION[$url]['version'] < $data['version']) {
        $state = createQuestionState($questions);
        return;
    }

    echo "<p>loading from session</p>";

    $state = $_SESSION[$url];

}

function createQuestionState($questions)
{
    global $url;
    global $data;
    $state=[];
   
    foreach($questions as $question) {
        
        if ($question['type'] ==MULTIPLE_CHOICE) {           
            shuffle($question['options']);                       
        }

        $state['questions'][]=$question;
    }

    $state['version']=$data['version'];
    $_SESSION[$url] = $state;

    return $state;


}


$questions=createQuestions($state['questions']);


?>

<div>

    <h3>Kysely</h3>

    <p>
        <?= $data['title'] ?>
    </p>


    <?php if (!$userID): ?>
        <div>
            <label for="name">Nimi:</label>
            <input name="name" id="name" required>
        </div>
    <?php else: ?>

        <div>
            <label for="name">Kirjautuneena käyttäjänä</label>
            <input name="name" id="name" required disabled value="<?= $username ?>">
        </div>
    <?php endif ?>

    

    <form action="respond.php" method="POST">
        <input name="url" hidden value="<?= $url ?>">
        <hr>

        <div id="question-list">
            <?php foreach ($questions as $v): ?>


                <?php $v->render(); ?>


            <?php endforeach; ?>


        </div>


        <button type="submit" formaction="respond.php?save">Tallenna</button>
        <button type="submit">Lähetä vastaus</button>

    </form>

</div>