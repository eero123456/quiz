<?php


function addQuiz($quiz)
{
    global $db;

    $sql = "INSERT INTO quizzes (title,description,json,owner,accepting_answers, auth_only) VALUES (?,?,?,?,?,?)";

    $stmt = $db->prepare($sql);

    if (!$stmt->execute([$quiz['title'], $quiz['description'], $quiz['json'], $quiz['owner'], $quiz['accepting_answers'], $quiz['auth_only']])) {
        return 0;
    }

    return $db->insert_id;
}

function getQuizByURL($url)
{
    global $userID;
    global $db;
    $sql = "SELECT id,json,accepting_answers,auth_only FROM quizzes WHERE url=?;";

    $stmt = $db->prepare($sql);

    $stmt->execute([$url]);

    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        return null;
    }

    $data = $result->fetch_assoc();

    return $data;
}


function getQuizForEditor($quizID)
{
    global $userID;
    global $db;
    $sql = "SELECT url,title,description,json,accepting_answers,auth_only FROM quizzes WHERE id=? AND owner=?;";

    $stmt = $db->prepare($sql);

    $stmt->execute([$quizID, $userID]);

    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        return null;
    }

    $data = $result->fetch_assoc();

    return $data;
}


function getQuizForResponder($url)
{
    global $userID;
    global $db;
    $sql = "SELECT url,owner,title,description,json,accepting_answers,auth_only,version FROM quizzes WHERE url=?";

    $stmt = $db->prepare($sql);

    $stmt->execute([$url]);

    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        return null;
    }

    $data = $result->fetch_assoc();

    return $data;
}

function updateQuiz($quiz)
{
    global $userID;
    global $db;

    $sql = "UPDATE quizzes SET title=?, description=?, accepting_answers=?, auth_only=? WHERE id=? AND owner=?";

    $stmt = $db->prepare($sql);

    if (!$stmt->execute([$quiz['title'], $quiz['description'], $quiz['accepting_answers'], $quiz['auth_only'], $quiz['id'], $userID])) {
        return 0;
    }

    return 1;
}

function getQuizForEdit($id)
{
    global $db;
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

    $sql = "UPDATE quizzes SET json=?,version=version+1 WHERE id=?";

    $stmt = $db->prepare($sql);

    if (!$stmt->execute([$questionsJSON, $quizID])) {
        return 0;
    }

    return 1;
}

function getQuizResponses($quizID)
{
    global $db;

    $sql = "SELECT r.id,r.responder_id, COALESCE(u.username,r.name) as name ,r.time,r.score,r.max_score, r.response " .
        "FROM responses r LEFT JOIN users u ON u.id=r.responder_id WHERE r.quiz_id=?;";

    $stmt = $db->prepare($sql);

    $stmt->execute([$quizID]);

    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        return null;
    }

    $data = $result->fetch_all(MYSQLI_ASSOC);

    return $data;
}


function getResponse($responseID,$userID)
{
    global $db;

    $sql = "SELECT r.id,r.quiz_id,r.responder_id, COALESCE(u.username,r.name) as name ,r.time,r.score,r.max_score, r.response 
    FROM responses r 
    LEFT JOIN users u ON u.id=r.responder_id
    WHERE r.id=? AND EXISTS (SELECT owner FROM quizzes WHERE quizzes.id = r.quiz_id AND owner=?);";

    $stmt = $db->prepare($sql);

    $stmt->execute([$responseID,$userID]);

    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        return null;
    }

    $data = $result->fetch_assoc();
    $result->free();
    return $data;
}

