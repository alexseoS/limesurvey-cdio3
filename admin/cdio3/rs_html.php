<?php 
function show_rs_menu() {
	global $imageurl, $scriptname;
    $rsmenu  = "<div class='menubar'>\n";
    $rsmenu  .="<div class='menubar-title'>\n"
    . "<div class='menubar-title-left'>\n"
    . "<strong>Raw Survey Management</strong>";

	
    $rsmenu .= "<img src='{$imageurl}/blank.gif' alt='' width='11' />\n"
    . "<img src='{$imageurl}/seperator.gif' alt='' />\n";

    // list raw surveys
    $rsmenu .= "<a href=\"#\" onclick=\"window.open('$scriptname?action=listrs', '_top')\" title=\"List Surveys\" >\n"
    ."<img src='{$imageurl}/surveylist.png' name='ListRawSurveys' alt='List Raw Surveys' />"
    ."</a>" ;

	
	//select raw survey

    $rsmenu .= "<span class=\"boxcaption\">Raw Surveys:</span>"
    . "<select onchange=\"window.open('$scriptname?action=editlo&id='+this.options[this.selectedIndex].value,'_top')\">\n"
    . "</select></div></div>\n";
	

    return $rsmenu;
}

function genNewRawSurveyForm ($lo) {
	$html = "<table><form method=POST action='admin.php?action=gen_raw_survey&saved=true'>";
	$html .= "<input type=hidden name=loid value='".$lo['item_id']."'";
	$html .= "
		<tr>
			<td valign='top'>
				Survey name: 
			</td>
			<td valign='top'>
				<input type='text' name='txtName' size=60 value='".$lo['name']."'/>
			</td>
		</tr>
		<tr>
			<td valign='top'>
				Admin email: 
			</td>
			<td valign='top'>
				<input type='text' size=60 name='txtAdminEmail' />
			</td>
		</tr>
		<tr>
			<td colspan=2 align=center>
				<input type='submit' value='Generate'/>
			</td>
		</tr>
	";
	$html .= "</form></table>";
	
	return $html;
}

function genRawQuestionOnInitialize($lo_tree, $questiontype, &$lo_id_list, $index="") {
	$count = count($lo_tree);
	$html = "";
	
	for($i = 0; $i < $count; $i++) {
		if (isset($lo_tree[$i])) {
			$item = $lo_tree[$i];
			
			if ($index == "") {
				$item_index = $item['ord'];
			}
			else {
				$item_index = $index.".".$item['ord'];
			}
			$lo_item_id = $item['item_id'];
			array_push($lo_id_list, $lo_item_id);
			$lo_item = $item['name'];
			
			$count_q = count($questiontype);
			$select = "<select name='slQuestionType_$lo_item_id'>";
			for($j =0; $j < $count_q; $j++) {
				$select .= "<option value='".$questiontype[$j]['tid']."'>".$questiontype[$j]['type_name']."</option>";
			}
			$select .= "</select>";
			
			$html .= "<tr>
				<td>$item_index.$lo_item</td>
				<td><input name='txtQuestion_$lo_item_id' type='text' value='$lo_item'/></td>
				<td>$select</td>
			</tr>";
			
			if(isset($item['sub'])) {
				$html .= genRawQuestionOnInitialize($item['sub'], $questiontype, $lo_id_list, $item_index);
			}
		}
	}
	
	return $html;
}

function genQuestionTypeSelectBox() {
	$qt = GetAllQuestionType();
	$count = count($qt);
	$html = "<select name='slQuestionType'>";
	for($i =0; $i < $count; $i++) {
		$html .= "<option value='".$qt[$i]['tid']."'>".$qt[$i]['type_name']."</option>";
	}
	$html .= "</select>";
}
?>
