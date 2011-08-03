<?php

include_once('login_check.php');
require_once('rs_html.php');

//let get the menu, invoked from rs_html.php
$rs_output = show_rs_menu();

if ($action == 'gen_raw_survey') {
	$loid = returnglobal('loid');
	$saved = returnglobal('saved');
	
	$rs_output .= "<div class='header'>Generate new raw survey</div>";
	$rs_output .= "<div class='tab-page'><center>";
	if(isset($loid)) {
		if (isset($saved) && $saved=="true") {
			$name = returnglobal('txtName');
			$adminemail = returnglobal('txtAdminEmail');
			
			//InsertRawSurvey($loid, $name, $adminemail, $_SESSION['loginID']);
			
			$_SESSION['initialize_rs'] = true;
			$rs_output .= "<script>
				alert('Survey has been created! Redirect to adding new question!');
				location.href='$scriptname?action=new_survey_question&loid=$loid'</script>";
		}
		else {
			$lo = GetLO($loid);
			if ($lo != null) {
				$rs_output .= genNewRawSurveyForm($lo);
			}
			else {
				$rs_output.= "Learning outcomes not found!";
			}
		}
	}
	else {
		$rs_output.= "Learning outcomes not found!";	
	}
	
	$rs_output .= "</center></div>";
}
elseif ($action == 'listrs') {
	$rs_output .= "<div class='header'>All raw survey</div>";
	$rs = GetRawSurveyList();
	if($rs != null) {
		$listrs = "<br /><table class='listsurveys'><thead>
				  <tr>
                    <th>".$clang->gT("LOID")."</th>
				    <th>".$clang->gT("Raw Survey Name")."</th>
					<th>".$clang->gT("Admin email")."</th>
				    <th>".$clang->gT("Owner ID")."</th>
				  </tr></thead>
				  <tfoot><tr class='header'>
		<td colspan=\"4\">&nbsp;</td>".
		"</tr></tfoot>
		<tbody>";
		foreach($rs as $item) {
			$listrs .= "
				<tr>
					<td>".
						$item['loid']
						."
					</td>
					<td><a href=$scriptname?action=edit_rs&rsid=".$item['rsid'].">".$item['name']."</a></td>
					<td>".$item['adminemail']."</td>
					<td>".$item['owner_id']."</td>	
				</tr>
			";
		}
		$listrs .= "</tbody></table>";
		
		$rs_output .= $listrs;
	}
}
elseif ($action == 'new_survey_question') {
	$saved = returnglobal('saved');
	
	$rs_output .= "<div class='header'>Generate question for this survey</div>";
	$rs_output .= "<div class='tab-page'>";
	$rs_output .= "<form action='$scriptname?action=new_survey_question&saved=true' method='POST'><table class='listsurveys'>
		<thead><tr>
			<th>LO item</th>
			<th>Question</th>
			<th>Question type </th>
		</tr></thead><tfoot><tr><td class=header align=center colspan=3><input type='submit' value='Generate'/></td></tr></tfoot><tbody>";
	//todo: check if it is posted from previous page
	if(true) {
		$loid = returnglobal('loid');
		//print_r($loid);
		if(isset($loid)) {
			if (isset($saved) && $saved == "true") {
				//insert new question!
				$loid_list = returnglobal('loid_list');
				$loid_list = explode("~", $loid_list);
				foreach($loid_list as $item) {
					$loid = $item;
					$question = returnglobal("txtQuestion_".$item);
					$questiontype = returnglobal("slQuestionType_".$item);
					$parent_id = "";
				}
			}
			else {
				$lo_tree = get_lo_subtree($loid);
				//print_r($lo_tree);
				$qt = GetAllQuestionType();
				$lo_id_list = array();
				$html = genRawQuestionOnInitialize($lo_tree, $qt, $lo_id_list);
				
				//print_r($qt);
				//print_r($lo_id_list);
				
				$rs_output .= $html;
				$rs_output .= "<input name='loid' type='hidden' value='$loid' />";
				$lo_id_list = implode("~", $lo_id_list);
				$rs_output .= "<input name='loid_list' type='hidden' value='$lo_id_list' />";
			}
			
		}
	}
	
	$rs_output .= "</tbody></table></form></div>";
}
elseif ($action == 'edit_rs') {
	
}
?>
