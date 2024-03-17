<?php

class TextQuestion extends Question
{

    public $answer;
    public $variables = [];
    function __construct($id)
    {
        parent::__construct($id, TEXT_QUESTION);

        $this->answer = "Vastaus...";
        $this->variables = [];

    }
    
    public function load($data) {
        parent::load($data);
        $this->answer=$data['answer'];
        $this->variables=$data['variables'];
    }
    public function update($data) {
        parent::update($data);  
        $this->answer=$data['answer']; 
       }
    public function toData()
    {
        $base=parent::toData();
        $base['answer']=$this->answer;
        $base['variables']=$this->variables;

        return $base;
    }

    public function render()
    {
        parent::render();

        echo "<input name='q-$this->id-responder-answer' placeholder='Vastaus'>";
        echo "</div>";
    }

    public function renderEdit()
    {
        parent::renderEdit();

        echo "
            <label>Vastaus:
                <input name='<q-$this->id-answer' placeholder='Vastaus' value='$this->answer'>
            </label>
        ";

    }

    public function renderResponse() {
        parent::renderResponse();
        
        echo "<p>$this->answer</p>";
        echo "<p>Pisteet $this->score/ $this->maxScore";

    }



    public function parseFormData($data)
    {
        $this->questionText = $data['questionText'];
        $this->answer = $data['answer'];
    }
}



