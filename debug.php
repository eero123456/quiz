<?php

require "partials/header.php";
require "./db/conn.php";

if (isset($_GET['reset'])) {
    setcookie (session_id(), "", time() - 3600);
    session_destroy();
    session_write_close();    
}

?>

<h2>Debug and dev info</h2>

<h3>Session</h3>

<?php var_dump($_SESSION); ?>

<h3>Stack</h3>

<ul>

    <li>Apache/2.4.52 (Ubuntu)</li>
    <li>
        PHP <?= phpversion() ?>
    </li>

    <li>MySQL 8.0.36-0 ubuntu0.22.04.1</li>


</ul>




<h3>Database schema</h3>


<?php
describeTable("users");
describeTable("responses");
describeTable("quizzes");

function describeTable($tablename)
{
    global $db;

    $sql = "describe $tablename";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        return null;
    }

    $data = $result->fetch_all(MYSQLI_ASSOC);

    printAll("Users table", $data);
}


function printAll($tablename, $data)
{
    ?>

    <h4>
        <?= $tablename ?>
    </h4>

    <table>

        <tr>
            <th>Field</th>
            <th>Type</th>
            <th>Null</th>
            <th>Key</th>
            <th>Default</th>
        </tr>


        <?php foreach ($data as $v): ?>

            <tr>
                <td>
                    <?= $v['Field'] ?>
                </td>
                <td>
                    <?= $v['Type'] ?>
                </td>
                <td>
                    <?= $v['Null'] ?>
                </td>
                <td>
                    <?= $v['Key'] ?>
                </td>
                <td>
                    <?= $v['Default'] ?>
                </td>
            </tr>


        <?php endforeach ?>

    </table>
    <?php
}