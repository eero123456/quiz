<?php

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    http_response_code(405);
    die();
}

require_once "user/auth.php";
$userID = requireUserID();

if (!isset($_POST['quizID']) || !is_numeric($_POST['quizID'])) {
    http_response_code(400);
    die();
}

if (!isset($_POST['questionID']) || empty($_POST['questionID'])) {
    http_response_code(400);
    die();
}

$quizID = (int) $_POST["quizID"];
$questionID = (int) $_POST['questionID'];

require_once("db/conn.php");
require_once "quiz/queries.php";

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

$action = $_POST['action'];

switch ($action) {

    case "add":
        handleUpload();
        break;

    case "clear":
        handleClearImage();
        break;
}

//http_response_code(400);
exit;



function handleClearImage()
{
    global $form_questions;
    global $quizID;
    global $questionID;

    for ($i = 0; $i < count($form_questions); $i++) {

        if ($form_questions[$i]['id'] == $questionID) {
            $form_questions[$i]['attachment'] = "";
            $form_questions[$i]['hasAttachment'] = false;
            break;
        }
    }

    $questionsJSON = json_encode($form_questions);

    updateQuestions($quizID, $questionsJSON);

}
function handleUpload()
{
    global $form_questions;
    global $quizID;
    global $questionID;

    $uploadedFile = saveImage();
    if (is_null($uploadedFile)) {
        http_response_code(400);
        exit;
    }


    for ($i = 0; $i < count($form_questions); $i++) {

        if ($form_questions[$i]['id'] == $questionID) {
            $form_questions[$i]['attachment'] = $uploadedFile;
            $form_questions[$i]['hasAttachment'] = true;
            break;
        }
    }

    $questionsJSON = json_encode($form_questions);

    updateQuestions($quizID, $questionsJSON);


}

function saveImage()
{

    global $quizID;
    global $questionID;

    $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $filename = uniqidReal() . "." . $extension;

    $target_file = $_SERVER['DOCUMENT_ROOT'] . "/uploads/" . $filename;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        return $filename;
    }

    return null;
}



function uniqidReal($lenght = 32)
{
    // uniqid gives 13 chars, but you could adjust it to your needs.
    if (function_exists("random_bytes")) {
        $bytes = random_bytes(ceil($lenght / 2));
    } elseif (function_exists("openssl_random_pseudo_bytes")) {
        $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
    }
    return substr(bin2hex($bytes), 0, $lenght);
}