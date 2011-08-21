<?php 

include_once('login_check.php');
require_once('classes/PHPEXcel/PHPExcel.php');
require_once('cdio3_common_functions.php');

$lo_output = show_lo_menu();

if ($action == 'listlo') {
    $query = "select * from ".db_table_name('cdio3_learningoutcomes')."where parent_id=-1 AND status=1";
    
    $result = db_execute_assoc($query) or safe_die($connect->ErrorMsg());
    
    if($result->RecordCount() > 0) {
        $listlo = "<br /><table class='listsurveys'><thead>
                  <tr>
                <th>".$clang->gT("Learning outcomes")."</th>
                <th>".$clang->gT("Description")."</th>
                </tr></thead>
                <tfoot><tr class='header'>
        <td colspan=\"6\">&nbsp;</td>".
        "</tr></tfoot>
        <tbody>";
        while($rows = $result->FetchRow()) {
            $listlo .= "<tr>";

            $listlo .= "<td><a href=$scriptname?action=editlo&id={$rows['id']}>".$rows['name']."</a></td>";
            $listlo .= "<td>".$rows['description']."</td>";
            $listlo .= "</tr><tbody>";
        }
        $listlo .= "</table>";
        $lo_output .= $listlo;
    }
    else {
        $lo_output .= "<p>There has been no learning outcomes yet</p>";
    }
}
else if ($action == 'editlo') {
    if(isset($_POST['txtname']) || isset($_POST['txtdesc'])) {
        $loUpdate['name'] = $_POST['txtname'];
        $loUpdate['description'] = $_POST['txtdesc'];
        $loUpdate['id'] = $_POST['txtid'];
        if (!UpdateLO($loUpdate)) {
            $lo_output = 'error occurred';
        }
    }
    if (isset($_GET['id'])) {
        $item_id = $_GET['id'];
    
        $query = "select * from ".db_table_name("cdio3_learningoutcomes")." where id='".db_quote($item_id)."' and status=1";
        $result = db_execute_assoc($query) or safe_die($connect->ErrorMsg());
        
        if ($result->RecordCount() > 0) {
            $row = $result->FetchRow();
            
            $lo_output .= "<div class='header'>Edit Learning Outcomes</div>
            <div class='tab-page'>
            <center>
            <form action='$scriptname?action=editlo&id={$item_id}' method='post'>
            <table>
                <tr>
                    <td valign='top'>
                     <li><label>Name: </label> 
                     <input type='hidden' value='{$item_id}' name='txtid' />
                     <input type='text' value=\"$row[name]\" size='50' name='txtname' /></li>
                    <li><label>Description:</label><br />
                    <textarea cols='60' name='txtdesc'>$row[description]</textarea></li>
                    </td>
                <tr>
                <td valign='top'>         
                    ";
            //get learning outcomes tree of the current learning outcomes and display it
            $tree = get_lo_subtree($item_id);
            $lo_output.="<ul class='mktree' id='lotree'>".display_lo_tree($tree)."</ul>";
            
            $popupURL = "popup.php?action=new_lo_item&root_id=$item_id";
            
            $lo_output.="
                    <center>
                    <input type='button' value='Add item' onclick=\"window.open('$popupURL', 
                                              'Add new learning outcomes item', 
                                              'width=400, height=200'); return false;\"/>
                    <input type='submit' value='Cancel' />
                    <input type='submit' value='Save'/>
            </form>
                    <input type='button' value='Generate survey' onclick=\"location.href='admin.php?action=gen_raw_survey&loid=$item_id'\" />";
            
            $hashID = hashLOID($item_id);
            $lo_output.= " <input type='button' value='Delete'
            						onclick=\" if (confirm('Sure?')) { window.open('popup.php?action=delete_lo_item&id=$hashID'); 
            							location.href='admin.php?action=listlo';} else {return false;}\" />
                    </center>
                </td>
                </tr>
            </tr></table>
            </center>
            </div>";
        }else {
            $lo_output .= "Learning outcomes not found!";
        }
    }
}
else if ($action == 'addlo') {
    if(isset($_GET['saved']) && ($_GET['saved'] == 'true')) {
        if (isset($_POST['txtname'])) {
            $name = $_POST['txtname'];
            if (isset($_POST['desc']))
                $desc = $_POST['desc'];
                InsertNewLO($name, $desc);
                $lo_output .= "into!";
            }
        else {
            $lo_output .= "out!";
        }
        $lo_output .= "saved!";
    }
    else {
        $lo_output .= genAddLOForm();
    }
    
}
else if ($action == 'importlo') {
    if ($_FILES["excel_file"]["error"] > 0) {
        $lo_output .= "Return code: ".$_FILES["excel_file"]["error"];
    }
    
    $fname = $_FILES["excel_file"]["name"];
    if (file_exists("../upload/".$fname)){
        $lo_output .= "File exists!";
    }
    else {
        move_uploaded_file($_FILES["excel_file"]["tmp_name"], "../upload/".$fname);
        processExcelFile("../upload/".$fname, $fname);
        
        unlink("../upload/".$fname);
        
        $lo_output .= "<script type=\"text/javascript\">\n<!--\n alert(\"".$clang->gT("Import finished!","js")."\")
                        location.href='admin.php?action=listlo';\n //-->\n</script>\n";
    }
}
elseif ($action == 'gensurvey') {
    if (isset($_GET['saved']) && ($_GET['saved'] == true)) {
        $lo_output .= 'gensurvey not implement yet';
    }
    else {
        $lo_output .= 'gensurvey not implement yet';
    }
}

function display_lo_tree($tree, $parent_index = " ") {
    $counter = count($tree);
    $index = 0;
    $html = "";
    while ($index < $counter) {
        if (isset($tree[$index])) {
            $item = $tree[$index];
            
            $item_index = "---".$parent_index.$item['ord'].". ";
            $url = "popup.php?action=edit_lo_item&itemid=".$item['id'];
            
            $html.= $item_index."<a href=\"javascript: void(0)\" 
                                   onclick=\"window.open('$url', 
                                          'Edit learning outcomes item', 
                                          'width=400, height=200'); 
                                           return false;\" title='".$item['description']."'>".$item['name']."</a><br />";        
            
            if (isset($item['sub'])) {
                $html .= display_lo_tree($item['sub'], $item_index);
            }
        }
        $index += 1;
    }
    
    return $html;
}

function processExcelFile($filename, $lo_name) {
    $objPHPExcel = PHPExcel_IOFactory::load($filename);
    
    $row = 2;
    $cur_level;
    
    InsertNewLO($lo_name, "", -1, 1);
    
    $root = GetLOItemByContent($lo_name);
    $parent_item['level1'] = array ('level' => '-1', 'content' => $root["name"]);
    
    $max_row = $objPHPExcel->getActiveSheet()->getHighestRow();
    
    while($row <= $max_row) {
        $cur_level = 0;
        $order = 1;
        $index[0] = $objPHPExcel->getActiveSheet()->getCell("A".$row)->getValue();

        if ($index[0] != "0") {
            $cur_level = 1;
            $order = $index[0];
        }
        
        $index[1] = $objPHPExcel->getActiveSheet()->getCell("B".$row)->getValue();
        if ($index[1] != "0") {
            $cur_level = 2;
            $order = $index[1];
        }
        
        $index[2] = $objPHPExcel->getActiveSheet()->getCell("C".$row)->getValue();
        if ($index[2] != "0") {
            $cur_level = 3;
            $order = $index[2];
        }
        
        $index[3] = $objPHPExcel->getActiveSheet()->getCell("D".$row)->getValue();
        if ($index[3] != "0") {
            $cur_level = 4;
            $order = $index[3];
        }
        
        $content = $objPHPExcel->getActiveSheet()->getCell("E".$row)->getValue();
        $content = trim($content);
        
        //print_r($cur_level);
        
        //print_r($content);        
        if ($cur_level == 1) {
            //if this is the first level, parent is root;    
            InsertNewLO($content, " ", $root['id'], $order);
        }
        else {
            //else, check the parent_item
            $pitem = GetLOItemByContent($parent_item['level'.$cur_level]['content']);
            //print_r($pitem['name']);
            InsertNewLO($content, " ", $pitem['id'], $order);
        }
        
        //if this is at same level of previous item
        $parent_item['level'.($cur_level+1)] = array(
                    'level' => $cur_level+1,
                    'content' => $content);

        $row ++;
        //break;
    }
}

function getlearningoutcomeslist() {
    $query = "select * from ".db_table_name('cdio3_learningoutcomes')."where parent_id=-1";
    
    $result = db_execute_assoc($query) or safe_die($connect->ErrorMsg());

    $html = "<option value=0>Select an learning outcomes</option>";
    if ($result->RecordCount() > 0) {
        while ($row = $result->FetchRow()) {
            $html .= "<option value=".$row['id'].">--- ".$row['name']."</option>";
        }
    }
    
    return $html;
}

function show_lo_menu()
{
    global $imageurl, $scriptname;
    $lomenu  ="<div class='menubar surveybar'>\n"
    . "<div class='menubar-title ui-widget-header'>\n"
    . "<strong>Lerning Outcomes Management</strong>"
    . "</div>"
    . "<div class='menubar-main'>";
    
    // list lo
    $lomenu .= "<a href=\"#\" onclick=\"window.open('$scriptname?action=listlo', '_top')\" title=\"List Surveys\" >\n"
    ."<img src='{$imageurl}/surveylist.png' name='ListSurveys' alt='List Surveys' />"
    ."</a>" ;
    $lomenu .= "<img src='{$imageurl}/blank.gif' alt='' width='11' />\n"
    . "<img src='{$imageurl}/seperator.gif' alt='' />\n";
    
    //new lo
    $lomenu .= "<a href=\"#\" onclick=\"window.open('$scriptname?action=addlo', '_top')\" title=\"Add LO\" >\n"
    ."<img src='{$imageurl}/add.png' name='ListSurveys' alt='Add LO' />"
    ."</a>" ;
    
    //select lo

    $lomenu .= "<span class=\"boxcaption\">Learning outcomes:</span>"
    . "<select onchange=\"window.open('$scriptname?action=editlo&id='+this.options[this.selectedIndex].value,'_top')\">\n"
    . getlearningoutcomeslist()
    . "</select></div></div>\n";

    return $lomenu;
}

function genAddLOForm() {
    global $scriptname;
    $html = "
            <div class=\"tab-pane\" id=\"tab-pane-newgroup\">
            <div class=\"tab-page\"> <h2 class=\"tab\">Input new LO
            </h2>
            <form action='$scriptname?action=addlo&saved=true' method=\"POST\">
            <li>
                <label>Name</label><br />
                <input type='text' size='80' maxlength='100' name='txtname'/>
                <font color='red' face='verdana' size='1'>Required</font>
            </li>
            
            <br />
            <li><label>Description</label><br />
                <textarea cols='80' rows='8' name='desc'></textarea>
            </li><br />
            
            <input type=\"submit\" value=\"Add\" />
            </form>
            </div>
            
            <div class=\"tab-page\"> 
                <h2 class=\"tab\">Import LO</h2>
                <form action='$scriptname?action=importlo' method=\"POST\" enctype=\"multipart/form-data\">
                    <label> Select an excel file:</label><br /><br />
                       <input type=\"file\" name=\"excel_file\" id=\"excel_file\"/><br /><br />
                    <input type=\"Submit\" value=\"Upload\"/>
                </form>
            </div>
        </div>
    ";
    
    return $html;
}

$lo_output .= '<br/>';

?>