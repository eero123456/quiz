<?php

require_once "../user/auth.php";

$userID=requireUserID();

require_once("../db/conn.php");
require_once "./queries.php";

require("../partials/header.php");

$quizID = (int) $_GET['id'];

$quiz= getQuizForEditor($quizID);

if (is_null($quiz)) {
    header("Location:/quiz/edit.php");
    die();
}


$responses = getQuizResponses($quizID);

if (is_null($responses)) {
    echo "<p>Ei vastauksia</p>";
    exit;
}

//id,responder_id,time,score,max_score, response FROM responses
?>

<h3>Kyselyn vastaukset</h3>




<table>


<tr>
    <th>ID</th>
    <th>Kirjautunut</th>
    <th>Nimi</th>
    <th>Tallennusaika</th>
    <th>pisteet</th>
    <th>vastaukset</th>
</tr>

<?php foreach ($responses as $res) :?>

    <tr>
        <td><?=$res['id']?></td>
        <td><?=$res['responder_id'] ? "Kyllä" : "ei"  ?></td>
        <td><?=$res['name']?></td>
        <td><?=$res['time']?></td>
        <td><?=$res['score']+0?>/<?=$res['max_score']+0?></td>
        <td><a href="/quiz/response.php?quiz=<?=$quizID ?>&id=<?= $res['id'] ?>" target="_blank" >Vastaukset</a></td>
    </tr>



<?php endforeach ?>



</table>
