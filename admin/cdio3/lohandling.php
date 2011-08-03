<?php 

include_once('login_check.php');
require_once('lo_html.php');
require_once("/../classes/PHPEXcel/PHPExcel.php");

//showlomenu is invoked from lo_html.php
$lo_output = showlomenu();

if ($action == 'listlo') {	
	$query = "select * from ".db_table_name('cdio3_learningoutcomes')."where parent_id=-1 AND status=1";
	
	$result = db_execute_assoc($query) or safe_die($connect->ErrorMsg());
	
	if($result->RecordCount() > 0) {		
		$listlo = "<br /><table class='listsurveys'><thead>
				  <tr>
                    <th>".$clang->gT("LOID")."</th>
				<th>".$clang->gT("Learning outcomes")."</th>
				<th>".$clang->gT("Description")."</th>
				</tr></thead>
				<tfoot><tr class='header'>
		<td colspan=\"6\">&nbsp;</td>".
		"</tr></tfoot>
		<tbody>";
        while($rows = $result->FetchRow()) {
			$listlo .= "<tr>";
			$listlo .= "<td>".$rows['id']."</td>";
			$id = $rows['id'];
			
			$listlo .= "<td><a href=$scriptname?action=editlo&id=$id>".$rows['name']."</a></td>";
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
	if (isset($_GET['id'])) {
		$item_id = $_GET['id'];
	
		$query = "select * from ".db_table_name("cdio3_learningoutcomes")." where id='".db_quote($item_id)."' and status=1";
		
		$result = db_execute_assoc($query) or safe_die($connect->ErrorMsg());
		
		$row;
		if ($result->RecordCount() > 0) {
			$row = $result->FetchRow();
			
			$lo_output .= "<div class='header'>Edit Learning Outcomes</div>
		    <div class='tab-page'>
		    <center>
		    <form action='$scriptname?action=editlo&status=saved' method='post'>
		    <table>
		        <tr><td valign='top'><li><label>Name: </label> 
					<input type='text' value=\"$row[name]\" size='50'/></li>
		                <li><label>Description:</label><br />
		                <textarea cols='60'>$row[description]</textarea></li>
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
					<input type='button' value='Generate survey' onclick=\"location.href='admin.php?action=gen_raw_survey&loid=$item_id'\" />
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
			
			//this function is invoke from database.php
			InsertNewLO($name, $desc);
			
			$lo_output .= "into!";
		}
		else {
				$lo_output .= "out!";
		}
		$lo_output .= "saved!";
		
	}
	else {
		//$lo_output .= file_get_contents('cdio3/add_lo_html.html');
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
	//	$lo_output .= "";
	}
}
elseif ($action == 'gensurvey') {
	if (isset($_GET['saved']) && ($_GET['saved'] == true)) {
		$lo_output .= file_get_contents('cdio3/new_survey.html');
	}
	else {
		$lo_output .= file_get_contents('cdio3/raw_survey.html');
	}
}

function display_lo_tree($tree, $parent_index = " ") {
	//print_r ($tree);
	$counter = count($tree);
	$index = 0;
	
	$html = "";
	/*
	<ul class='mktree' id='lotree'>
					<li id='level1' class='liOpen'>Level 1_1
						<ul>
							<li id='level2_1' class='liOpen'>Level2_1
								<ul id='level2' class='liOpen'>
									<li>Level_3</li>
								</ul>
							</li>
							
							<li id='level2_2'>Level2_2</li>
						</ul>
					</li>
					<li id='level1_2'>Level 1_2
					</li>
				</ul>*/
	while ($index < $counter) {
		if (isset($tree[$index])) {
			$item = $tree[$index];
			
			$item_index = "---".$parent_index.$item['order'].". ";
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
		
		//print_r($index[0]);
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
?>
