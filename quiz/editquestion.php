<?php

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    header("Location:/");
    die();
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["userID"])) {
    header("Location:/user/login.php");
    die();
}

$userID = $_SESSION["userID"];

if (!isset($_POST['quizID']) || !is_numeric($_POST['quizID'])) {
    header("Location:/");
    die();
}

/*
if (!isset($_POST['questionType']) || empty($_POST['questionType'])) {
    header("Location:/");
    die();
}

*/

$quizID = $_POST["quizID"];
//$type = $_POST['questionType'];

require_once("../db/conn.php");

unset($_POST["quizID"]);

$form_questions = [];

foreach ($_POST as $k => $v) {

    $parts = explode("-", $k, 3);
    if (count($parts) < 3) {
        continue;
    }
    $id = $parts[1];
    $trimmedKey = $parts[2];
    //echo $parts[1];
    //echo "<br>";
    $form_questions[$id][$trimmedKey] = $v;
}

require_once "../question/base.php";

require_once "queries.php";

$quizData=getQuizForEditor($quizID);

$questionData=json_decode($quizData['json'],true);
//var_dump($questionData);
//exit;
$questions=createQuestions($questionData);

//var_dump($form_questions);

$a = parseQuestions($form_questions);

$json = json_encode($a);

if (json_last_error() !== JSON_ERROR_NONE) {

    die(json_last_error_msg());
}



updateQuestions($quizID,$json);

header("Location:/quiz/edit.php?id=" . $quizID);

function parseQuestions($formData)
{

    $questions = [];
    foreach ($formData as $id => $data) {

        switch ($data['type']) {

            case MULTIPLE_CHOICE:
                $question=new MultipleChoice($id);                
                break;

            case TEXT_QUESTION:
                $question=new TextQuestion($id);                
                break;
        }

        
        $question->update($data);
        
        $questions[] = $question->toData();


    }


    return $questions;

}





//var_dump($questions);
//var_dump($_POST);


/*

$data = getQuizForEdit($quizID);




if (is_null($data) || $data['owner'] != $userID) {
    header("Location:/");
    die();
}

$currentQuestions = json_decode($data['json'], true);

$used = array_map(function ($q) {
    return $q['id'];
}, $currentQuestions);


if (json_last_error() !== JSON_ERROR_NONE) {
    //
}

// get next ID

$used = array_map(function ($q) {
    return $q['id'];
}, $currentQuestions);
$used[] = 0;

$nextID = max($used) + 1;



include "../question/factory.php";


switch ($type) {

    case TEXT_QUESTION:
        $newQuestion = createTextQuestion($nextID);
        break;
    case MULTIPLE_CHOICE:
        $newQuestion = createMultipleChoiceQuestion($nextID);
        break;

    default:
        http_response_code(400);
        exit;
}

$currentQuestions[] = $newQuestion;

$updated = json_encode($currentQuestions);


if (json_last_error() !== JSON_ERROR_NONE) {
    //
}

updateQuestions($quizID, $updated);

// js request
if (isset($_SERVER["HTTP_X_REQUESTED_WITH"])) {
    $json = json_encode($newQuestion);

    header("Content-Type:application/json");
    echo $json;
    die();
}

// no js
header("Location:/quiz/edit.php?id=" . $quizID);

function getQuizForEdit($id)
{
    global $db;
    global $userID;
    $sql = "SELECT owner,json FROM quizzes WHERE id=?;";

    $stmt = $db->prepare($sql);

    $stmt->execute([$id]);

    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        return null;
    }

    $data = $result->fetch_assoc();

    return $data;
}


function updateQuestions($quizID, $questionsJSON)
{
    global $userID;
    global $db;

    $sql = "UPDATE quizzes SET json=? WHERE id=?";

    $stmt = $db->prepare($sql);

    if (!$stmt->execute([$questionsJSON, $quizID])) {
        return 0;
    }

    return 1;
}


*/