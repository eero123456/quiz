<?php

require_once "../user/auth.php";

$userID=getUserID();

require("../partials/header.php");
include("../db/conn.php");



// new blank form
if (!isset($_POST['url']) || empty($_POST['url'])) {
    header("Location:/");
    die();
}

require_once "../question/base.php";
require_once "queries.php";

$url = $_POST['url'];

$quiz=getQuizByURL($url);
$quizID=$quiz['id'];

if (!$_SESSION[$url]) {
    header("Location:/");
    die();
}

$stored = $_SESSION[$url];




$questions=createQuestions($stored['questions']);



$form_questions = [];

foreach ($_POST as $k => $v) {

    $parts = explode("-", $k, 3);
    if (count($parts) < 3) {
        continue;
    }
    $id = $parts[1];
    $trimmedKey = $parts[2];
    $form_questions[$id][$trimmedKey] = $v;
}



$parsedResponses=[
    "response"=>[],
    'score'=>0,
    'max_score'=>0
];



foreach ($form_questions as $k => $v) {
    
    switch($v['type']) {
        case MULTIPLE_CHOICE:
            $response = parseResponseDataMultipleChoice($v, $k);
            $result=validateResponse($response);
            
            break;
        case TEXT_QUESTION:
            //$response = parseResponseDataMultipleChoice($v, $k);
            break;
        default:
            continue 2;
    }

    
    $parsedResponses['response'][] = $result;
    $parsedResponses['score']+=$result['score'];
    $parsedResponses['max_score']+=$result['max_score'];

}


echo "<h4>Vastauksen tiedot</h4>";

//var_dump($parsedResponses);

function getQuestionByID($id) {
    global $questions;

    for ($i=0;$i<count($questions);$i++) {
        if ($questions[$i]->id==$id) {
            return $questions[$i];
        }
    }

    return null;
}

foreach($parsedResponses['response'] as $response) {

    $q=getQuestionById($response['id']);

    $q->load($response);
    $q->renderResponse();
    //renderResponse($v);

}

$score=$parsedResponses['score'];
$max_score=$parsedResponses['max_score'];
echo "<p>Pisteet yhteensä: $score / $max_score</p>";

saveResponse($parsedResponses);



echo "<p>Vastaus tallennettu!</p>";

function saveResponse($response)
{

    $userID = getUserID() ?? null;
    global $quizID;
    global $db;

    $responsejson=json_encode($response['response']);

    $sql = "INSERT INTO responses (quiz_id,responder_id,score,max_score,response) VALUES (?,?,?,?,?)";

    $stmt = $db->prepare($sql);

    if (!$stmt->execute([$quizID,$userID, $response['score'], $response['max_score'], $responsejson])) {
        return 0;
    }

    return $db->insert_id;


}




function validateResponse($response)
{

    $correct = getQuestionFromSession($response['id']);
    
    if (is_null($correct)) {
        //
        return false;
    }
    
        
    //$response['questionText']=$correct['questionText'];
    //$response['allowMultipleSelections']=$correct['allowMultipleSelections'];
    $correct['isAnswered']=true;

    if (!$correct['allowMultipleSelections']){
        $selected=$response['options'][0];
        //$response['options']=$correct['options'];
        $points = getOptionPoints($selected, $correct['options']);                                
        
        foreach( $correct['options'] as $k=>&$v) {           
            $v['selected']= $v['id']== $selected;
        }
        $correct['score']=$points>0 ? $points:0;
        $correct['max_score']=max(array_map(fn($option)=>$option['points'],$correct['options']));
        //$response['options']=
        
        return $correct;
    }

    foreach ($response['options'] as $k=>$selectedOption) {

        $points = getOptionPoints($selectedOption, $correct['options']);
        //$response['options']['score'] = $points;
        $response['score'] += $points;        
        //$response['options']['selected'] = true;
        $correct['options']['selected'] = true;
        if ($points == 0) {
            //$response['options']['correct'] = true;
            $correct['options']['correct'] = true;
        } else {
            //$response['options']['correct'] = false;
            $correct['options']['correct'] = false;
        }
    }

    if ($response['score'] < 0) {
        $response['score'] = 0;
    }
        
    $response['options']=$correct['options'];
    
    return $response;
}

function getOptionPoints($id, $options)
{
    foreach ($options as $val) {
        if ($val['id'] == $id) {
            return $val['points'];
        }
    }
    return 0;
}




function getQuestionFromSession($id)
{
    global $stored;

    foreach ($stored['questions'] as $question) {

        if ($question['id'] == $id) {
            return $question;
        }

    }

    return null;
}



/*


$data = getQuiz($url);


if (is_null($data)) {
    header("Location:/");
    die();
}

$questions = json_decode($data['json'], true);


function getQuiz($url)
{
    global $userID;
    global $db;
    $sql = "SELECT url,title,description,json,accepting_answers FROM quizzes WHERE url=?";

    $stmt = $db->prepare($sql);

    $stmt->execute([$url]);

    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        return null;
    }

    $data = $result->fetch_assoc();

    return $data;
}

?>

<div>

    <h3>Kysely</h3>

    <p>
        <?= $data['title'] ?>
    </p>

    <form action="respond.php" method="POST">
        <input name="url" hidden value="<?= $url ?>">
        <hr>

        <div id="question-list">
            <?php foreach ($questions as $k => $v): ?>


                <div>
                    <h4>Question
                        <?= $v['id'] ?>
                    </h4>
                    <p>
                        <?= $v['questionText'] ?>
                    </p>

                </div>

            <?php endforeach; ?>


        </div>


        <button type="submit">Lähetä vastaus</button>

    </form>

</div>

*/