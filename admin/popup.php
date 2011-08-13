<?php
require_once('../classes/core/startup.php');

if (version_compare(PHP_VERSION,'5','>=')&& !(function_exists('domxml_new_doc')))
{
    require_once('classes/core/domxml-php4-to-php5.php');
}
require_once('../config-defaults.php');
require_once('../common.php');

require_once('htmleditor-functions.php');

//@ini_set('session.gc_maxlifetime', $sessionlifetime);     Might cause problems in client??

if($casEnabled==true)
{
    include_once("login_check_cas.php");
}
else
{
    include_once('login_check.php');
}

if ($action == "edit_lo_item"|| $action="new_lo_item") {
    include ('cdio3/cdio3_common_functions.php');
    include ('database.php');
}

$popupoutput = '';

if(isset($_SESSION['loginID']))
{
    if($action == "edit_lo_item") {
        $itemid = returnglobal('itemid');
        $saved = returnglobal('saved');
        
        if (!isset($itemid)) {
            $popupoutput .= "Item not found";
        }
        else {
            //if submit button is hitted
            if (isset($saved) && $saved=="true") {
                $item['name'] = $_POST['txtItemName'];
                $item['item_id'] = $itemid;
                $item['description'] = $_POST['txtDesc'];
                if (UpdateLO($item))
                    $popupoutput .= "<script type=\"text/javascript\">\n<!--\n alert(\"".$clang->gT("Item has been updated!","js")."\")
                        window.opener.location.reload(); window.close();\n //-->\n</script>\n";
                else 
                    $popupoutput .= "<script type=\"text/javascript\">\n<!--\n alert(\"".$clang->gT("Item updating fail!","js")."\")\n //-->\n</script>\n";
            }
            
            //display the form
            $popupoutput .= genEditLoItemForm($itemid);
        }        
    }
    else if ($action == 'new_lo_item') {
        $root_id = returnglobal('root_id');
        $saved = returnglobal('saved');
        
        //if item exist
        if (isset($root_id)) {
            //submit button hitted
            if(isset($saved) && $saved==true) {
                $name = returnglobal('txtName');
                $desc = returnglobal('txtDesc');
                $parent = returnglobal('slParent');
                $order = returnglobal('txtOrder');
                
                //echo $parent;
                
                InsertNewLO($name, $desc, $parent, $order);
                
                //exit after add;
                $popupoutput .= "<script type=\"text/javascript\">
                    \n<!--\n alert(\"".$clang->gT("Item added!","js")."\")\n; window.opener.location.reload(); window.close(); //-->\n</script>\n";
            }
            else {
                $popupoutput .= genNewLOItemForm($root_id);
            }    
        }
        //if no item
        else {
            
        }
    }
    else {
        echo $action;
    }
}
else {
    include('access_denied.php');
}

//display the output
echo $popupoutput;

function genEditLoItemForm($itemid) {
    $item_not_found = "Item not found!";
    $html = '';
    
    //look for item in database
    $item = GetLO($itemid);
    
    //if there is an item
    if ($item != NULL) {
        $name = $item['name'];
        $desc = $item['description'];
        $html .= "<form name='edit_lo_item' method='POST', action=\"popup.php?action=edit_lo_item&saved=true\">";
        $html .= "<input type=\"hidden\" name=\"itemid\" value=\"$itemid\" />";
        $html .= "<label>Item name: </label>";
        $html .= "<input type=text name='txtItemName' value=\"$name\" /></br />";
        $html .= "<label>Description:</label><br />";
        $html .= "<textarea name='txtDesc' cols=25>$desc</textarea><br />";
        $html .= "<input type=submit value='Save' />";
        $html .= "<input type=button value='Close' onclick=\"window.opener.location.reload(); window.close(); return false;\" />";
        $html .= "</form>";
    }
    else {
        $html .= $item_not_found;
    }
    
    return $html;
}

function genNewLOItemForm($root_id) {
    $tree = get_lo_subtree($root_id);
    
    $html = '';
    $html .= "<form method=POST action=\"popup.php?action=new_lo_item&saved=true\" />";
    $html .= "<input type='hidden' name='root_id' value='$root_id' />";
    $html .= "Parent item: <select name='slParent'><option value='$root_id'>Root</option>".genLOItemSelectOption($tree)."</select><br />";
    $html .= "Order: ".getLOItemIndex($root_id).".<input type='text' name='txtOrder' /><br />";
    $html .= "Name: <input type='text' name='txtName' /><br />";
    $html .= "Description: <br /> <textarea name='txtDesc' ></textarea><br />";
    $html .= "<input type='submit' value='Add' />";
    $html .= "<input type='button' value='Close' onclick=\"window.close(); return false;\" />";
    
    return $html;
}

function genLOItemSelectOption($tree, $parent_index=" ") {
    $html = '';
    $count = count($tree);
    
    for($i = 0; $i < $count; $i++) {
        if (isset($tree[$i])) {
            $item = $tree[$i];
            $item_index = "---".$parent_index.$item['ord'].". ";
            $html .= "<option value='".$item['item_id']."'>".$item_index.$item['name']."</option>";
            if(isset ($item['sub'])) {
                $html .= genLOItemSelectOption($item['sub'], $item_index);
            }
        }
    }
    
    return $html;
}

?>
