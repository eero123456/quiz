<?php


class MultipleChoice extends Question
{

    public $options=[];
    public $selected;
    public $allowMultipleSelections = false;
    public $variables=[];
    function __construct($id)
    {
        parent::__construct($id, MULTIPLE_CHOICE);

        $this->options = [createOption(1), createOption(2)];
        $this->selected = 0;
        $this->allowMultipleSelections = false;
        $this->variables = [];

    }

    public function load($data) {

        parent::load($data);

        $this->allowMultipleSelections=$data['allowMultipleSelections'];
        $this->selected=$data['selected'];
        $this->options=$data['options'];
    }   

    public function update($data) {
     parent::update($data);   

    $options=parseFormOptions($data);

    $this->options=$options;
    $this->allowMultipleSelections=isset($data['allowMultipleSelections']);

    }
    public function toData()
    {
        $base=parent::toData();
        $base['allowMultipleSelections']=$this->allowMultipleSelections;
        $base['selected']=$this->selected;
        $base['options']=$this->options;

        return $base;
    }

    public function render()
    {
        parent::render();
        renderMultipleChoice($this->id,$this->options,$this->allowMultipleSelections);
        echo "</div>";
    }
    
    public function renderEdit() {
        parent::renderEdit();

        renderMultipleChoiceEdit($this->id,$this->allowMultipleSelections,$this->options);
        

    }

    public function renderResponse() {
        parent::renderResponse();

        renderResponseMultipleChoice($this->options,$this->allowMultipleSelections);
        
        echo "<p>Pisteet $this->score/ $this->maxScore";

    }



    public function parseFormData($data)
    {
        $this->questionText = $data['questionText'];
        unset($data['questionText']);
        
        if (isset($data['allowMultiple'])) {
            $this->allowMultipleSelections= true;
            unset($data['allowMultiple']);
        }
        
        $this->options=parseFormOptions($data);
        

    }

}




function parseFormOptions($data)
{
    
    $options = [];

    foreach ($data as $k => $v) {

        if (!str_starts_with($k, "option")) {
            continue;
        }

        $parts = explode("-", $k, 3);
        if (count($parts) < 3) {
            continue;
        }

        $id = $parts[1];
        $property = $parts[2];
        $options[$id][$property] = $v;

    }

    $formattedOptions = [];

    foreach ($options as $id => $option) {

        $formattedOptions[] = [
            'id' => $id,
            'text' => $option['text'],
            'points' => (float) $option['points']
        ];

    }

    return $formattedOptions;


}

function renderResponseMultipleChoice($options,$allowMultipleSelections)
{
    ?>
    <table>
        <?php foreach ($options as $v): ?>

            <tr>
                <td>
                    <?= $v['text'] ?>
                </td>
                <td>
                    <?php if ($allowMultipleSelections): ?>
                        <input type="checkbox" <?php if ($v['checked']) echo "checked"?> disabled >
                    <?php else: ?>

                        <input type="radio" <?php if ($v['selected']) echo "checked"?> disabled>

                    <?php endif ?>

                </td>
            </tr>

        <?php endforeach; ?>
    </table>

    <?php

}








function createOption($id)
{
    return [
        'id' => $id,
        'text' => "Vaihtoehto",
        'points' => $id % 2,
    ];
}



function renderMultipleChoice($id,$options,$allowMultipleSelections )
{
    ?>
    

    <table>
        <?php foreach ($options as $k => $v): ?>

            <tr>
                <td>
                    <?= $v['text'] ?>
                </td>
                <td>
                    <?php if ($allowMultipleSelections): ?>
                        <input type="checkbox" name="<?= 'q-' . $id . '-option-' . $v['id'] ?>">
                    <?php else: ?>

                        <input type="radio" name="<?= 'q-' . $id . '-radio' ?>" value="<?= $v['id'] ?>">

                    <?php endif ?>

                </td>
            </tr>

        <?php endforeach; ?>
    </table>

    <?php

}


function renderMultipleChoiceEdit($id,$allowMultipleSelections,$options)
{
    ?>
    <div>
        <label for="allowMultiple">Salli monta valintaa:</label>
        <input type="checkbox" name="<?= "q-$id-allowMultiple" ?>" id="allowMultiple"
            <?= $allowMultipleSelections ? 'checked' : '' ?>>
    </div>

    <table>
        <tr>
            <th>Teksti</th>
            <th>Pisteet</th>
            <th>Poista</th>
        </tr>
        <?php foreach ($options as $v): ?>

            <?php $optionID = "q-" . $id . "-option-" . $v['id']; ?>
            <tr>
                <td>
                    <input name="<?= $optionID . "-text" ?>" value="<?= $v['text'] ?>" />
                </td>
                <td>
                    <input type="number" name="<?= $optionID . '-points' ?>" value='<?= $v["points"] ?>' step="0.1">
                </td>
                <td><button type="button" onclick="deleteOption(event)">poista</button></td>
            </tr>

        <?php endforeach; ?>
        <tr>
            <td><button type="button" onclick="addOption(event)">Lisää vaihtoehto</button></td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <?php

}




function parseResponseDataMultipleChoice($data, $questionID)
{    
    $options = [];
    foreach ($data as $k => $v) {

        if ($k== "radio") {            
            $options[] = (int) $v;
            break;
        }

        if (!str_starts_with($k, "option")) {
            continue;
        }

        $parts = explode("-", $k, 2);
        if (count($parts) < 2) {
            continue;
        }

        $options[] = (int) $parts[1];

    }

    return [
        "id" => $questionID,
        "questionType"=>MULTIPLE_CHOICE,
        'options' => $options
    ];

}
/*
function renderQuestionEdit($question)
{
    $id=$question['id'];
    $type=$question['questionType'];
    
    echo "<input name='q-$id-type' value='$type' hidden>";

    switch ($question['questionType']) {


        case TEXT_QUESTION:
            renderTextQuestionEdit($question);
            break;

        case MULTIPLE_CHOICE:
            renderMultipleChoiceEdit($question);
            break;

        default:

    }

}

*/