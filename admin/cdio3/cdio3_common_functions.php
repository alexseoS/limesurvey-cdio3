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

// CDIO3: Learning Outcomes Database manipulation functions

//Insert new LO, incase insert root lo, parent id is set default to -1
function InsertNewLO($name, $description, $parent_id=-1, $order=1)
{
        global $connect;
        $insertarray=array( 'name'=>$name,
                            'description'=>$description, 
                            '`order`'=>$order,
                            'parent_id'=>$parent_id);
                            
        $dbtablename=db_table_name_nq('cdio3_learningoutcomes');
        
        $isquery = $connect->GetInsertSQL($dbtablename, $insertarray);
        
        //$isquery = "insert into $dbtablename('name', 'description', 'order', 'parent_id') values ($name, $description, $order, $parent_id)";
        
        $connect->execute($isquery);
}


function GetLO ($itemid) {
        global $connect;
        $query = "select * from ".db_table_name('cdio3_learningoutcomes'). "where id='".db_quote($itemid)."' and status=1";
        $result = db_execute_assoc($query) or safe_die($connect->ErrorMsg());

        if ($result->RecordCount() > 0) {
                $r = $result->FetchRow();
                return $r;
        }
        else {
                return NULL;
        }
}

function UpdateLO($item) {
        global $connect;
        $query = "update ".db_table_name_nq("cdio3_learningoutcomes")." set name='".db_quote($item['name'])."'";
        $query .=", description='".db_quote($item['description'])."' ";
        $query .="where id='".db_quote($item['id'])."'";

        return $connect->Execute($query);
}

//get learning outcome subtree
//$item_id: parent id, -1 = root
//$level: to set the value of level for each item
//return value: an array of children item of the $item_id, each child node has its subtree
function get_lo_subtree($item_id=-1, $level=1) {
        $query = "select * from ". db_table_name("cdio3_learningoutcomes"). "where parent_id='".db_quote($item_id)."' and status=1 order by `order` ASC";
        $result = db_execute_assoc($query) or safe_die($connect->ErrorMsg());
        $r = "";
        if ($result->RecordCount() > 0) {
                $counter = 0;
                
                while($row = $result->FetchRow()) {
                                $r[$counter]['id'] = $row['id'];
                                $r[$counter]['parent_id'] = $row['parent_id'];
                                $r[$counter]['name'] = $row['name'];
                                $r[$counter]['description'] = $row['description'];
                                $r[$counter]['order'] = $row['order'];
                                $r[$counter]['status'] = $row['status'];
                                $r[$counter]['level'] = $level;
                                
                                $r[$counter]['sub'] = get_lo_subtree($row['id'], $level+1);
                                
                                $counter+=1;
                }
        }
        
        return $r;
}

function getLOItemIndex($item_id) {
        global $connect;
        $rt = "";
        $query = "select parent_id from ".db_table_name("cdio3_learningoutcomes")." where id='".db_quote($item_id)."' and status=1";
        $result = db_execute_assoc($query) or safe_die($connect->ErrorMsg());
        
        if ($result->RecordCount() > 0) {
                $r = $result->FetchRow();
                if ($r['parent_id'] != "-1") {
                        $rt .= getLOItemIndex($r['parent_id']).$item_id;
                }
                else {
                        $rt .= $item_id;
                }
        }
        
        return $rt;
}

function GetRawSurveyList() {
        global $connect;
        $query = "select * from ".db_table_name("cdio3_rawsurveys")." where status =1";
        
        $result = db_execute_assoc($query) or safe_die($connect->ErrorMsg());
        
        $rt = null;
        if ($result->RecordCount() > 0) {
                $i = 0;
                while($row = $result->FetchRow()) {
                        $rt[$i] = $row;
                        $i ++;
                }
        }
        
        return $rt;
}

function InsertRawSurvey($loid, $name) {
        global $connect;
        $insertarray=array( 'loid'=>$loid,
                            'name'=>$name);
    $dbtablename=db_table_name_nq('cdio3_rawsurveys');
    $isquery = $connect->GetInsertSQL($dbtablename, $insertarray);
        
        $connect->execute($isquery);
}

function InsertRawSurveyQuestion($typeid,$rsid, $question, $title, $parent_id = "0") {
        global $connect;
        
        $insertarray = array (
                'type_id' => $typeid,
                'parent_id' => $parent_id,
                'rsid' => $rsid,
                'question' => $question,
                'title' => $title
                );
        $dbtablename = db_table_name('rs_question');
        $query = $connect->GetInsertSQL($dbtablename, $insertarray);
        
        $connect->execute($query);
}

function InsertRawSurveyAnswer($qid, $code, $answer, $assessment_value, $scale_id) {
        global $connect;
        
        $insertarray = array(
                'qid' => $qid,
                'code' => $code,
                'answer' => $answer,
                'assessment_value' => $assessmentvalue,
                'scale_id' => $scale_id 
                );
        $dbtablename = db_table_name('rs_question');
        $query = $connect->GetInsertSQL($dbtablename, $insertarray);
        
        $connect->execute($query);
}

function GetAllQuestionType() {
        return null;
}

function GetQuestionIDByLOID($loid) {
        global  $connect;
        
}

function GetLOItemByContent($content) {
        global $connect;
        $query = "SELECT * FROM ".db_table_name("cdio3_learningoutcomes")." WHERE status = 1 AND name='".db_quote($content)."' ORDER BY id DESC LIMIT 1";
        
        $result = db_execute_assoc($query) or safe_die($connect->ErrorMsg());
        $rt = null;
        if ($result->RecordCount() > 0) {
                $rt = $result->FetchRow();
        }
        
        return $rt;
}

?>
