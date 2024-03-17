<?php
require "auth.php";

$userID=requireUserID();

include("../db/conn.php");
require("../partials/header.php");




$myQuestions = getUserQuizzes($userID);

echo "<h4>Profile</h4>";


renderQuizList($myQuestions);

function renderQuizList($questions)
{
    ?>

    <h4>Quizzes</h4>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Kuvaus</th>
                <th>Tila</th>
                <th>Vastauksia</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($questions as $q): ?>

                <tr>
                    <td>
                        <a href="/quiz/edit.php?id=<?= $q['id'] ?>">
                            <?= $q['id'] ?>
                        </a>
                    </td>
                    <td>
                        <?= $q['description'] ?>
                    </td>
                    <td>
                        <?= $q['accepting_answers'] ? "avoinna" : "suljettu" ?>
                    </td>
                    <td>
                        <?= $q['response_count'] ?>
                    </td>
                </tr>

            <?php endforeach; ?>

        </tbody>
    </table>

    <?php
}

function getUserQuizzes($id)
{
    global $db;
    $sql = "SELECT q.*, (SELECT COUNT(1) FROM responses WHERE quiz_id=q.id) as response_count FROM quizzes q WHERE owner=?;";

    $stmt = $db->prepare($sql);

    $stmt->execute([$id]);

    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);

    return $data;
}