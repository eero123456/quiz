<?php

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    header("Location:/");
    die();
}

require_once "../user/auth.php";
$userID = requireUserID();

if (!isset($_POST['quizID']) || !is_numeric($_POST['quizID'])) {
    header("Location:/");
    die();
}

if (!isset($_POST['questionID']) || !is_numeric($_POST['questionID'])) {
    header("Location:/");
    die();
}

$quizID = $_POST["quizID"];
$questionID = $_POST['questionID'];

include_once("../db/conn.php");
include_once "queries.php";

$data = getQuizForEdit($quizID);

if (is_null($data) || $data['owner'] != $userID) {
    header("Location:/");
    die();
}

$form_questions = json_decode($data['json'], true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    exit;
}

for ($i = 0; $i < count($form_questions); $i++) {
    if ($form_questions[$i]['id'] == $questionID) {
        array_splice($form_questions, $i, 1);
        break;
    }
}

$updated = json_encode($form_questions);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    exit;
}

$a=updateQuestions($quizID, $updated);
var_dump($a);
// js request
if (isset($_SERVER["HTTP_X_REQUESTED_WITH"])) {
    exit;
}

// no js
header("Location:/quiz/edit.php?id=" . $quizID);
