<?/* 
   +-------------------------------------------------------------------------+
   | Copyright (C) 2002 Ian Berry                                            |
   |                                                                         |
   | This program is free software; you can redistribute it and/or           |
   | modify it under the terms of the GNU General Public License             |
   | as published by the Free Software Foundation; either version 2          |
   | of the License, or (at your option) any later version.                  |
   |                                                                         |
   | This program is distributed in the hope that it will be useful,         |
   | but WITHOUT ANY WARRANTY; without even the implied warranty of          |
   | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           |
   | GNU General Public License for more details.                            |
   +-------------------------------------------------------------------------+
   | cacti: the rrdtool frontend [php-auth, php-tree, php-form]              |
   +-------------------------------------------------------------------------+
   | This code is currently maintained and debugged by Ian Berry, any        |
   | questions or comments regarding this code should be directed to:        |
   | - iberry@raxnet.net                                                     |
   +-------------------------------------------------------------------------+
   | - raXnet - http://www.raxnet.net/                                       |
   +-------------------------------------------------------------------------+
   */?>
<? 	
$section = "Add/Edit Graphs"; 
include ('include/auth.php');
include_once ('include/form.php');

if ($form[action]) { $action = $form[action]; } else { $action = $args[action]; }
if ($form[ID]) { $id = $form[ID]; } else { $id = $args[id]; }

switch ($action) {
	case 'save':
		$redirect_location = form_save();
		
		header ("Location: $redirect_location"); exit;
		break;
	case 'item_movedown':
		item_movedown();
		
		header ("Location: " . getenv("HTTP_REFERER"));
		break;
	case 'item_moveup':
		item_moveup();
		
		header ("Location: " . getenv("HTTP_REFERER"));
		break;
    	case 'remove':
		tree_remove();
		
		header ("Location: rra.php");
		break;
	case 'edit':
		include_once ("include/top_header.php");
		
		tree_edit();
		
		include_once ("include/bottom_footer.php");
		break;
	default:
		include_once ("include/top_header.php");
		
		tree();
		
		include_once ("include/bottom_footer.php");
		break;
}

/* --------------------------
    The Save Function
   -------------------------- */

function form_save() {
	global $form;
	
	if (isset($form[save_component_rra])) {
		rra_save();
		return "rra.php";
	}
}

/* -----------------------
    Tree Item Functions
   ----------------------- */

function item_moveup() {
	include_once('include/tree_functions.php');
	
	global $args;
	
	$order_key = db_fetch_cell("SELECT order_key FROM graph_tree_view_items WHERE id=$args[tree_item_id]");
	if ($order_key > 0) { branch_up($order_key, 'graph_tree_view_items', 'order_key', ''); }
}

function item_movedown() {
	include_once('include/tree_functions.php');
	
	global $args;
	
	$order_key = db_fetch_cell("SELECT order_key FROM graph_tree_view_items WHERE id=$args[tree_item_id]");
	if ($order_key > 0) { branch_down($order_key, 'graph_tree_view_items', 'order_key', ''); }
}

/* ---------------------
    Tree Functions
   --------------------- */

function tree_edit() {
	include_once("include/tree_view_functions.php");
	
	global $args, $colors, $cdef_item_types;
	
	start_box("<strong>Trees [edit]</strong>", "", "");
	
	if (isset($args[id])) {
		$tree = db_fetch_row("select * from graph_tree_view where id=$args[id]");
	}else{
		unset($tree);
	}
	
	?>
	<form method="post" action="tree.php">
	
	<?DrawMatrixRowAlternateColorBegin($colors[form_alternate1],$colors[form_alternate2],0); ?>
		<td width="50%">
			<font class="textEditTitle">Name</font><br>
			A useful name for this graph tree.
		</td>
		<?DrawFormItemTextBox("name",$tree[name],"","255", "40");?>
	</tr>
	
	<?
	DrawFormItemHiddenIDField("id",$args[id]);
	end_box();
	
	start_box("Tree Items", "", "tree.php?action=item_edit&tree_id=$tree[id]");
	grow_edit_graph_tree($args[id], "", "");
	end_box();
	
	DrawFormItemHiddenTextBox("save_component_tree","1","");
	
	start_box("", "", "");
	?>
	<tr bgcolor="#FFFFFF">
		 <td colspan="2" align="right">
			<?DrawFormSaveButton("save", "tree.php");?>
		</td>
	</tr>
	</form>
	<?
	end_box();	
}

function tree() {
	global $colors;
	
	start_box("<strong>Graph Trees</strong>", "", "cdef.php?action=edit");
	                         
	print "<tr bgcolor='#$colors[header_panel]'>";
		DrawMatrixHeaderItem("Name",$colors[header_text],1);
		DrawMatrixHeaderItem("&nbsp;",$colors[header_text],1);
	print "</tr>";
    
	$trees = db_fetch_assoc("select * from graph_tree_view order by name");
	
	if (sizeof($trees) > 0) {
	foreach ($trees as $tree) {
		DrawMatrixRowAlternateColorBegin($colors[alternate],$colors[light],$i); $i++;
			?>
			<td>
				<a class="linkEditMain" href="tree.php?action=edit&id=<?print $tree[id];?>"><?print $tree[name];?></a>
			</td>
			<td width="1%" align="right">
				<a href="tree.php?action=remove&id=<?print $tree[id];?>"><img src="images/delete_icon.gif" width="10" height="10" border="0" alt="Delete"></a>&nbsp;
			</td>
		</tr>
	<?
	}
	}
	end_box();	
}
 ?>
