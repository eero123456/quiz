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

if (!isset($_POST['questionType']) || empty($_POST['questionType'])) {
    header("Location:/");
    die();
}

$quizID = $_POST["quizID"];
$type = $_POST['questionType'];

require_once("../db/conn.php");
require_once "queries.php";

$data = getQuizForEdit($quizID);

if (is_null($data) || $data['owner'] != $userID) {
    header("Location:/");
    die();
}

$questions = json_decode($data['json'], true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    exit;
}

$nextID = getNextQuestionID($questions);


include_once "../question/base.php";

switch ($type) {

    case TEXT_QUESTION:
        $newQuestion = new TextQuestion($nextID);
        break;
    case MULTIPLE_CHOICE:
        $newQuestion = new MultipleChoice($nextID);
        break;

    default:
        http_response_code(400);
        exit;
}

$questions[] = $newQuestion->toData();

$updated = json_encode($questions);

if (json_last_error() !== JSON_ERROR_NONE) {
    //
}

updateQuestions($quizID, $updated);

// js request
if (isset($_SERVER["HTTP_X_REQUESTED_WITH"])) {
    $newQuestion->renderEdit();
    die();
}

// no js
header("Location:/quiz/edit.php?id=" . $quizID);

function getNextQuestionID($questions): int
{
    $used = array_map(function ($q) {
        return $q['id'];
    }, $questions);

    if (count($used) === 0) {
        return 1;
    }
    return max($used) + 1;
}


