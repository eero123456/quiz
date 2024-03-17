<?php

const TEXT_QUESTION = "TextQuestion";
const MULTIPLE_CHOICE = "MultipleChoice";

const QUESTION_TYPES = [MULTIPLE_CHOICE, TEXT_QUESTION];

require "MultipleChoice.php";
require "TextQuestion.php";

class Question
{
    public readonly int $id;
    protected $type;

    public $questionText = "Question text";
    public $error = false;
    public $errorMessage = "";
    public $isAnswered = false;
    public $required = false;
    public $score = 0;
    public $maxScore = 1;
    public $section = 1;
    public $inRandomPool = false;
    public $hasAttachment = false;
    public $attachment = "";
    function __construct($id, $type)
    {
        $this->id = $id;
        $this->type = $type;
    }

    public function load($data)
    {

        $this->questionText = $data["questionText"];
        $this->error = $data["error"];
        $this->errorMessage = $data["errorMessage"];
        $this->isAnswered = $data["isAnswered"];
        $this->required = $data["required"];
        $this->score = $data["score"];
        $this->maxScore = $data["maxScore"];
        $this->section = $data["section"];
        $this->inRandomPool = $data["inRandomPool"];
        $this->hasAttachment = $data["hasAttachment"];
        $this->attachment = $data["attachment"];

    }

    public function update($data) {

        foreach($data as $k=>$v) {
            $this->$k=$v;
        }

    }

    public function toData()
    {
        $base = [
            "id" => $this->id,
            "type" => $this->type,
            "questionText" => $this->questionText,
            "error" => $this->error,
            "errorMessage" => $this->errorMessage,
            "isAnswered" => $this->isAnswered,
            "required" => $this->required,
            "score" => $this->score,
            "maxScore" => $this->maxScore,
            "section" => $this->section,
            "inRandomPool" => $this->inRandomPool,
            "hasAttachment" => $this->hasAttachment,
            "attachment" => $this->attachment
        ];

        return $base;


    }
    public function render()
    {
        echo "<div><input value='$this->type' name='q-$this->id-type' hidden />";

        if ($this->hasAttachment) {
            echo "<img src='/uploads/$this->attachment'alt='' >";
        }

        echo "<div><p> $this->questionText </p></div>";
    }

    public function renderEdit()
    {
        ?>
        <h4>Kysymys
            <?= $this->id ?>
        </h4>
        <div><input value='<?= $this->type ?>' name='q-<?= $this->id ?>-type' hidden />
            <button type='button' onclick='deleteQuestion(event,<?= $this->id ?>)'>Poista kysymys</button>

            <?php renderImageBlockEditor($this->id, $this->hasAttachment, $this->attachment); ?>

            <div>
                <p>Kysymysteksti</p>
                <textarea name='q-<?= $this->id ?>-questionText' rows='5' cols='40'><?= $this->questionText ?> </textarea>
            </div>

            <?php
    }

    public function renderResponse(){


        if ($this->hasAttachment) {
            echo "<img src='/uploads/$this->attachment' alt=''>";
        }
        
        echo "<div><p> $this->questionText </p></div>";
    }

    public function parseFormData($data)
    {


    }

}

function renderImageBlockEditor($id, $hasAttachment, $attachment)
{

    if ($hasAttachment) {
        ?>
            <div>
                <img src="/uploads/<?= $attachment ?>" alt="">
                <input type="file" accept="image/*">

                <button type="button" onclick="uploadImage(event,<?= $id ?>)">Vaihda kuva</button>
                <button type="button" onclick="clearImage(event,<?= $id ?>)">Tyhjennä</button>
            </div>

            <?php
            return;
    }

    ?>
        <div>

            <label for="picture">Lisää kuva:</label>
            <img src="" alt="">
            <input type="file" accept="image/*">

            <button type="button" onclick="uploadImage(event,<?= $id ?>)">Tallenna</button>
            <button type="button" onclick="clearImage(event,<?= $id ?>)">Tyhjennä</button>
        </div>

        <?php
}


function createQuestions($data)
{
    //var_dump($data);
    $questions = [];
    foreach ($data as $questionData) {


        switch ($questionData['type']) {
            case MULTIPLE_CHOICE:
                $question = new MultipleChoice($questionData['id']);

                $question->load($questionData);
                break;
            case TEXT_QUESTION:
                $question = new TextQuestion($questionData['id']);
                $question->load($questionData);
                break;
        }
        $questions[] = $question;


    }

    return $questions;

}
