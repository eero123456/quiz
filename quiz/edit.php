<?php

require_once "../user/auth.php";


$userID=requireUserID();

require_once("../db/conn.php");
require_once "./queries.php";


require("../partials/header.php");

require_once "../question/base.php";

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    if (isset($_POST['quizID']) && is_numeric($_POST['quizID'])) {
        $quizID = (int) $_POST['quizID'];
        editQuiz();
        header("Location:/quiz/edit.php?id=" . $quizID);
        die();
    } else {

        $data = parseQuizdata();
        $data['owner'] = $userID;
        $insertedID = addQuiz($data);

        if ($insertedID === 0) {
            header("Location:/quiz/edit.php");
            die();
        }


        header("Location:/quiz/edit.php?id=" . $insertedID);
        die();
    }
}


// new blank form
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    include "./empty-form.html";
    die();
}



// Load existing quiz
$quizID = (int) $_GET['id'];

$data = getQuizForEditor($quizID);


if (is_null($data) ) {
    header("Location:/quiz/edit.php");
    die();
}

$questionsData = json_decode($data['json'], true);


$questions=createQuestions($questionsData);

//var_dump($questions);
function editQuiz()
{
    $data = parseQuizdata();
    updateQuiz($data);
}

function parseQuizdata()
{

    $quizID = (int) $_POST['quizID'] ?? 0;
    $title = htmlspecialchars($_POST['title']);

    $description = htmlspecialchars($_POST['description']);
    $accept_answers = isset($_POST['accepting_answers']) ? 1:0;
    $auth_only = isset($_POST['auth_only']) ?1:0;
    $json = '[]';

    return [
        "id" => $quizID,
        "title" => $title,
        "description" => $description,
        "json" => $json,
        "accepting_answers" => $accept_answers,
        "auth_only" => $auth_only
    ];

}

?>

<h4>Muokkaa kyselyä </h4>

<form method="POST" id="quiz-form">
    <input type="number" name="quizID" hidden value="<?= $quizID ?>">
    <div>
        <label for="title">Otsikko:</label>
        <input name="title" id="title" value="<?= $data['title'] ?>">
    </div>
    <div>
        <label for="description">Kuvaus:</label>
        <input name="description" id="description" value="<?= $data['description'] ?>">
    </div>

    <div>
        <label>URL:</label>
        <input disabled value="<?= $data['url'] ?>">
    </div>


    <div>
        <label for="accepting_answers">Avoinna:</label>
        <input type="checkbox" name="accepting_answers" id="accepting_answers" <?= $data['accepting_answers'] ? 'checked' : '' ?>>
    </div>

    <button type="submit">Tallenna</button>

</form>

<p><a href="/quiz/view.php?url=<?= $data['url'] ?>" target="_blank">Esikatselu </a></p>

<p><a href="/quiz/responses.php?id=<?= $quizID ?>">Vastaukset </a></p>




<div id="questions-wrapper">
    <h4>Lisää kysymys</h4>
    <form action="/quiz/addquestion.php" method="POST">
        <input type="number" name="quizID" hidden value="<?= $quizID ?>">

        <select name="questionType">
            <option value="" disabled selected>Select type</option>

            <?php foreach (QUESTION_TYPES as $type): ?>

                <option value="<?= $type ?>">
                    <?= $type ?>
                </option>
            <?php endforeach ?>
        </select>

        <button type="submit">Lisää</button>

    </form>


    <h3>Kysymykset</h3>


    <form action="editquestion.php" method="POST">

        <input name="quizID" value="<?= $quizID ?>" hidden>
        <div id="question-list">

            <?php foreach ($questions as $v): ?>                
                <div class="question">                    
                    <?php $v->renderEdit() ?>                    
                </div>

            <?php endforeach; ?>



        </div>

        <div>
            <p>updates</p>
            <button type="submit">Tallenna muutokset</button>
        </div>

    </form>





</div>