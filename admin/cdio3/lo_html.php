<?php

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

function showlomenu()
{
	global $imageurl, $scriptname;
    $lomenu  = "<div class='menubar'>\n";
    $lomenu  .="<div class='menubar-title'>\n"
    . "<div class='menubar-title-left'>\n"
    . "<strong>Lerning Outcomes Management</strong>";

	
    $lomenu .= "<img src='{$imageurl}/blank.gif' alt='' width='11' />\n"
    . "<img src='{$imageurl}/seperator.gif' alt='' />\n";

    // list lo
    $lomenu .= "<a href=\"#\" onclick=\"window.open('$scriptname?action=listlo', '_top')\" title=\"List Surveys\" >\n"
    ."<img src='{$imageurl}/surveylist.png' name='ListSurveys' alt='List Surveys' />"
    ."</a>" ;
	
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
?>
