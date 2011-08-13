<?
function testExporting() {
        $exportHtml .= "<select id='question_type' style='margin-bottom:5px' name='type' "
        . ">\n"
        . getqtypelist($eqrow['type'],'group')
        . "</select>\n";
        echo $exportHtml;
}

function parseQuestionContent () {

}

// encode LOID into xml format
function encodeLO ($loid) {

}

?>
