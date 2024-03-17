<?php

require_once "../user/auth.php";

$userID=requireUserID();

require_once("../db/conn.php");
require_once "./queries.php";

require("../partials/header.php");
require_once "../question/questionEditor.php";


$quizID = (int) $_GET['quiz'];
$responseID = (int) $_GET['id'];

$response= getResponse($responseID,$userID);

$form_questions = json_decode($response['response'], true);

if (is_null($response)) {
    
    exit;
}

//id,responder_id,time,score,max_score, response FROM responses
?>

<h3>Vastaus <?= $response['id'] ?></h3>

<table>

<tr>
    <th>ID</th>
    <th>Kirjautunut</th>
    <th>Nimi</th>
    <th>Tallennusaika</th>
    <th>pisteet</th>    
</tr>
    <tr>
        <td><?=$response['id']?></td>
        <td><?=$response['responder_id'] ? "Kyllä" : "ei"  ?></td>
        <td><?=$response['name']?></td>
        <td><?=$response['time']?></td>
        <td><?=$response['score']+0?>/<?=$response['max_score']+0?></td>
        
    </tr>




</table>



<div id="question-list">
            <?php foreach ($form_questions as $v): ?>


                <?php renderQuestionForResponder($v); ?>


            <?php endforeach; ?>


        </div>