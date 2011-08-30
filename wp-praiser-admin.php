<?php

function wp_praiser_admin_menu() 
{
	global $wp_praiser_admin_userlevel;
	add_object_page('WordPress Praiser Widget', 'Our Praises', $wp_praiser_admin_userlevel, 'wp-praiser', 'wp_praiser_praises_management');
}
add_action('admin_menu', 'wp_praiser_admin_menu');

function wp_praiser_count($condition = "")
{
	global $wpdb;
	$sql = "SELECT COUNT(*) FROM " . $wpdb->prefix . "wp_praiser ".$condition;
	$count = $wpdb->get_var($sql);
	return $count;
}
//********************** 	
// Makes the Upload Image button work

function praiser_admin_scripts() { 
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
	wp_enqueue_script('jquery');
}  
function praiser_admin_styles() { 
	wp_enqueue_style('thickbox'); 
}  
if (isset($_GET['page']) && $_GET['page'] == 'wp-praiser') { 
	add_action('admin_print_scripts', 'praiser_admin_scripts'); 
	add_action('admin_print_styles', 'praiser_admin_styles'); 
}
?>
<script language="JavaScript">
jQuery(document).ready(function() {
jQuery('#upload_image_button').click(function() {
formfield = jQuery('#wp_praiser_img_name').attr('name');
tb_show('', 'media-upload.php?type=image&TB_iframe=true');
return false;
});

window.send_to_editor = function(html) {
imgurl = jQuery('img',html).attr('src');
jQuery('#wp_praiser_img_name').val(imgurl);
tb_remove();
}

});
</script>
<?PHP
//**********************
function wp_praiser_pagenav($total, $current = 1, $format = 0, $paged = 'paged', $url = "")
{
	if($total == 1 && $current == 1) return "";
	
	if(!$url) {
		$url = 'http';
		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$url .= "s";}
		$url .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$url .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["PHP_SELF"];
		} else {
			$url .= $_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"];
		}
		if($query_string = $_SERVER['QUERY_STRING']) {
			$parms = explode('&', $query_string);
			$y = '?';
			foreach($parms as $parm) {
				$x = explode('=', $parm);
				if($x[0] == $paged) {
					$query_string = str_replace($y.$parm, '', $query_string);
				}
				else $y = '&';
			}
			if($query_string) {
				$url .= '?'.$query_string;
				$a = '&';
			}
			else $a = '?';	
		}
		else $a = '?';
	}
	else {
		$a = '?';
		if(strpos($url, '?')) $a = '&';	
	}
	
	if(!$format || $format > 2 || $format < 0 || !is_numeric($format)) {	
		if($total <= 8) $format = 1;
		else $format = 2;
	}
	
	
	if($current > $total) $current = $total;
		$pagenav = "";

	if($format == 2) {
		$first_disabled = $prev_disabled = $next_disabled = $last_disabled = '';
		if($current == 1)
			$first_disabled = $prev_disabled = ' disabled';
		if($current == $total)
			$next_disabled = $last_disabled = ' disabled';

		$pagenav .= "<a class=\"first-page{$first_disabled}\" title=\"".__('Go to the first page', 'wp-praiser')."\" href=\"{$url}\">&laquo;</a>&nbsp;&nbsp;";
		$pagenav .= "<a class=\"prev-page{$prev_disabled}\" title=\"".__('Go to the previous page', 'wp-praiser')."\" href=\"{$url}{$a}{$paged}=".($current - 1)."\">&#139;</a>&nbsp;&nbsp;";
		$pagenav .= '<span class="paging-input">'.$current.' of <span class="total-pages">'.$total.'</span></span>';
		$pagenav .= "&nbsp;&nbsp;<a class=\"next-page{$next_disabled}\" title=\"".__('Go to the next page', 'wp-praiser')."\" href=\"{$url}{$a}{$paged}=".($current + 1)."\">&#155;</a>";
		$pagenav .= "&nbsp;&nbsp;<a class=\"last-page{$last_disabled}\" title=\"".__('Go to the last page', 'wp-praiser')."\" href=\"{$url}{$a}{$paged}={$total}\">&raquo;</a>";
	
	}
	else {
		$pagenav = __("Goto page:", 'wp-praiser');
		for( $i = 1; $i <= $total; $i++ ) {
			if($i == $current)
				$pagenav .= "&nbsp<strong>{$i}</strong>";
			else if($i == 1)
				$pagenav .= "&nbsp;<a href=\"{$url}\">{$i}</a>";
			else 
				$pagenav .= "&nbsp;<a href=\"{$url}{$a}{$paged}={$i}\">{$i}</a>";
		}
	}
	return $pagenav;
}


function wp_praiser_addpraise($praise, $author = "", $source = "", $tags = "", $public = 'yes')
{
	if(!$praise) return __('Nothing added to the database.', 'wp-praiser');
	global $wpdb;
	$table_name = $wpdb->prefix . "wp_praiser";
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) 
		return __('Database table not found', 'wp-praiser');
	else //Add the praise data to the database
	{
		
		$praise = stripslashes($praise);
		$author = stripslashes($author);	
		$source = stripslashes($source);	
		$tags = stripslashes($tags);
		$img_name = str_replace("/", "&#47;", $img_name);
		$praise = "'".$wpdb->escape($praise)."'";
		$author = $author?"'".$wpdb->escape($author)."'":"NULL";
		$source = $source?"'".$wpdb->escape($source)."'":"NULL";
		$tags = explode(',', $tags);
		foreach ($tags as $key => $tag)
			$tags[$key] = trim($tag);
		$tags = implode(',', $tags);
		$tags = $tags?"'".$wpdb->escape($tags)."'":"NULL";
		if(!$public) $public = "'no'";
		else $public = "'yes'";
		$insert = "INSERT INTO " . $table_name .
			"(praise, author, source, tags, public, time_added, img_name)" .
			"VALUES ({$praise}, {$author}, {$source}, {$tags}, {$public}, NOW(), {$img_name})";
		$results = $wpdb->query( $insert );
		if(FALSE === $results)
			return __('There was an error in the MySQL query', 'wp-praiser');
		else
			return __('praise added', 'wp-praiser');
   }
}

function wp_praiser_editpraise($praise_id, $praise, $author = "", $source = "", $tags = "", $public = 'yes', $img_name="")
{
	if(!$praise) return __('praise not updated.', 'wp-praiser');
	if(!$praise_id) return srgq_addpraise($praise, $author, $source, $public, $img_name);
	global $wpdb;
	$table_name = $wpdb->prefix . "wp_praiser";
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) 
		return __('Database table not found', 'wp-praiser');
	else //Update database
	{
		
		$praise = stripslashes($praise);
		$author = stripslashes($author);	
		$source = stripslashes($source);	
		$tags = stripslashes($tags);
		$img_name = str_replace("/", "&#47;", $img_name);
	  	$praise = "'".$wpdb->escape($praise)."'";
		$author = $author?"'".$wpdb->escape($author)."'":"NULL";
		$source = $source?"'".$wpdb->escape($source)."'":"NULL";
		$tags = explode(',', $tags);
		$img_name = $img_name?"'".$wpdb->escape($img_name)."'":"NULL";
		echo "$img_name";
		foreach ($tags as $key => $tag)
			$tags[$key] = trim($tag);
		$tags = implode(',', $tags);
		$tags = $tags?"'".$wpdb->escape($tags)."'":"NULL";
		if(!$public) $public = "'no'";
		else $public = "'yes'";
		$update = "UPDATE " . $table_name . "
			SET praise = {$praise},
				author = {$author},
				source = {$source}, 
				tags = {$tags},
				public = {$public},
				img_name = {$img_name}, 
				time_updated = NOW()
			WHERE praise_id = $praise_id";
		$results = $wpdb->query( $update );
		if(FALSE === $results)
			return __('There was an error in the MySQL query', 'wp-praiser');		
		else
			return __('Changes saved', 'wp-praiser');
   }
}


function wp_praiser_deletepraise($praise_id)
{
	if($praise_id) {
		global $wpdb;
		$sql = "DELETE from " . $wpdb->prefix ."wp_praiser" .
			" WHERE praise_id = " . $praise_id;
		if(FALSE === $wpdb->query($sql))
			return __('There was an error in the MySQL query', 'wp-praiser');		
		else
			return __('praise deleted', 'wp-praiser');
	}
	else return __('The praise cannot be deleted', 'wp-praiser');
}

function wp_praiser_getpraisedata($praise_id)
{
	global $wpdb;
	$sql = "SELECT praise_id, praise, author, source, tags, public, img_name
		FROM " . $wpdb->prefix . "wp_praiser 
		WHERE praise_id = {$praise_id}";
	$praise_data = $wpdb->get_row($sql, ARRAY_A);	
	return $praise_data;
}

function wp_praiser_editform($praise_id = 0)
{
	$public_selected = " checked=\"checked\"";
	$submit_value = __('Add praise', 'wp-praiser');
	$form_name = "addpraise";
	$action_url = get_bloginfo('wpurl')."/wp-admin/admin.php?page=wp-praiser#addnew";
	$praise = $author = $source = $tags = $hidden_input = $back = "";

	if($praise_id) {
		$form_name = "editpraise";
		$praise_data = wp_praiser_getpraisedata($praise_id);
		foreach($praise_data as $key => $value)
			$praise_data[$key] = $praise_data[$key];
		extract($praise_data);
		$praise = htmlspecialchars($praise);
		$author = htmlspecialchars($author);
		$source = htmlspecialchars($source);
//		$img_name = str_replace("&#47;", "/", $img_name);
		$tags = implode(', ', explode(',', $tags));
		$hidden_input = "<input type=\"hidden\" name=\"praise_id\" value=\"{$praise_id}\" />";
		if($public == 'no') $public_selected = "";
		$submit_value = __('Save changes', 'wp-praiser');
		$back = "<input type=\"submit\" name=\"submit\" value=\"".__('Back', 'wp-praiser')."\" />&nbsp;";
		$action_url = get_bloginfo('wpurl')."/wp-admin/admin.php?page=wp-praiser";
	}

	$praise_label = __('The praise', 'wp-praiser');
	$author_label = __('Author', 'wp-praiser');
	$source_label = __('Source', 'wp-praiser');
	$tags_label = __('Tags', 'wp-praiser');
	$public_label = __('Public?', 'wp-praiser');
	$optional_text = __('optional', 'wp-praiser');
	$comma_separated_text = __('comma separated', 'wp-praiser');
	$img_name_label = __('Picture<BR><small><small>(60x60 pixel image)</small></small>', 'wp-praiser');
	$img_generic = __(plugins_url(). '/wp-praiser/60x60.png', 'wp-praiser');
	$upload_image_url = __(bloginfo('name'). '/wp-admin/media-upload.php?type=image&amp;TB_iframe=true&amp;width=640&amp;height=105', 'wp-praiser');

	$display =<<< EDITFORM
<form name="{$form_name}" method="post" action="{$action_url}">
	{$hidden_input}
	<table class="form-table" cellpadding="5" cellspacing="2" width="100%">
		<tbody><tr class="form-field form-required">
			<th style="text-align:left;" scope="row" valign="top"><label for="wp_praiser_praise">{$praise_label}</label></th>
			<td><textarea id="wp_praiser_praise" name="praise" rows="5" cols="50" style="width: 97%;">{$praise}</textarea></td>
		</tr>
		<tr class="form-field">
			<th style="text-align:left;" scope="row" valign="top"><label for="wp_praiser_author">{$author_label}</label></th>
			<td><input type="text" id="wp_praiser_author" name="author" size="40" value="{$author}" /><br />{$optional_text}</td>
		</tr>
		<tr class="form-field">
			<th style="text-align:left;" scope="row" valign="top"><label for="wp_praiser_source">{$source_label}</label></th>
			<td><input type="text" id="wp_praiser_source" name="source" size="40" value="{$source}" /><br />{$optional_text}</td>
		</tr>
		<tr class="form-field">
			<th style="text-align:left;" scope="row" valign="top"><label for="wp_praiser_tags">{$tags_label}</label></th>
			<td><input type="text" id="wp_praiser_tags" name="tags" size="40" value="{$tags}" /><br />{$optional_text}, {$comma_separated_text}</small></td>
		</tr>
		<tr>
			<th style="text-align:left;" scope="row" valign="top"><label for="wp_praiser_img_name">{$img_name_label}</label></th>
			<td valign="top"><label for="upload_image"><input type="text" id="wp_praiser_img_name" name="img_name" size="50" value="{$img_name}" /> <input id="upload_image_button" type="button" value="Upload Image" />
		<br />&nbsp;&nbsp;If Left blank <img height="30" width="30" src="{$img_generic}"> will be used.<BR>
		</label>

		</tr>
        <tr>
			<th style="text-align:left;" scope="row" valign="top"><label for="wp_praiser_public">{$public_label}</label></th>
			<td><input type="checkbox" id="wp_praiser_public" name="public"{$public_selected} />
		</tr></tbody>
	</table>
	<p class="submit">{$back}<input name="submit" value="{$submit_value}" type="submit" class="button button-primary" /></p>
</form>
EDITFORM;
	return $display;
}

function wp_praiser_changevisibility($praise_ids, $public = 'yes')
{
	if(!$praise_ids)
		return __('Nothing done!', 'wp-praiser');
	global $wpdb;
	$sql = "UPDATE ".$wpdb->prefix."wp_praiser 
		SET public = '".$public."',
			time_updated = NOW()
		WHERE praise_id IN (".implode(', ', $praise_ids).")";
	$wpdb->query($sql);
	if($public == 'yes')
		return __("Selected praises made public", 'wp-praiser');
	else
		return __("Selected praises made private", 'wp-praiser');
}

function wp_praiser_bulkdelete($praise_ids)
{
	if(!$praise_ids)
		return __('Nothing done!', 'wp-praiser');
	global $wpdb;
	$sql = "DELETE FROM ".$wpdb->prefix."wp_praiser 
		WHERE praise_id IN (".implode(', ', $praise_ids).")";
	$wpdb->query($sql);
	return __('praise(s) deleted', 'wp-praiser');
}



function wp_praiser_praises_management()
{	

	global $wp_praiser_db_version;
	$options = get_option('wp_praiser');
	$display = $msg = $praises_list = $alternate = "";
	
	if($options['db_version'] != $wp_praiser_db_version )
		wp_praiser_install();
		
	if(isset($_REQUEST['submit'])) {
		if($_REQUEST['submit'] == __('Add praise', 'wp-praiser')) {
			extract($_REQUEST);
			$msg = wp_praiser_addpraise($praise, $author, $source, $tags, $public, $img_name);
		}
		else if($_REQUEST['submit'] == __('Save changes', 'wp-praiser')) {
			extract($_REQUEST);
			$msg = wp_praiser_editpraise($praise_id, $praise, $author, $source, $tags, $public, $img_name);
		}
	}
	else if(isset($_REQUEST['action'])) {
		if($_REQUEST['action'] == 'editpraise') {
			$display .= "<div class=\"wrap\">\n<h2>WordPress Praiser Widget &raquo; ".__('Edit praise', 'wp-praiser')."</h2>";
			$display .=  wp_praiser_editform($_REQUEST['id']);
			$display .= "</div>";
			echo $display;
			return;
		}
		else if($_REQUEST['action'] == 'delpraise') {
			$msg = wp_praiser_deletepraise($_REQUEST['id']);
		}
	}
	else if(isset($_REQUEST['bulkactionsubmit']))  {
		if($_REQUEST['bulkaction'] == 'delete') 
			$msg = wp_praiser_bulkdelete($_REQUEST['bulkcheck']);
		if($_REQUEST['bulkaction'] == 'make_public') {
			$msg = wp_praiser_changevisibility($_REQUEST['bulkcheck'], 'yes');
		}
		if($_REQUEST['bulkaction'] == 'keep_private') {
			$msg = wp_praiser_changevisibility($_REQUEST['bulkcheck'], 'no');
		}
	}
	
	
	$display .= "<div class=\"wrap\">";
	
	if($msg)
		$display .= "<div id=\"message\" class=\"updated fade\"><p>{$msg}</p></div>";

	$display .= "<h2>WordPress Praiser Widget <a href=\"#addnew\" class=\"add-new-h2\">".__('Add new praise', 'wp-praiser')."</a></h2>";

	$num_praises = wp_praiser_count();
	
	if(!$num_praises) {
		$display .= "<p>".__('No praises in the database', 'wp-praiser')."</p>";

		$display .= "</div>";
	
		$display .= "<div id=\"addnew\" class=\"wrap\">\n<h2>".__('Add new praise', 'wp-praiser')."</h2>";
		$display .= wp_praiser_editform();
		$display .= "</div>";

		echo $display;
		return;
	}

	global $wpdb;

	$sql = "SELECT praise_id, praise, author, source, tags, public, img_name
		FROM " . $wpdb->prefix . "wp_praiser";
		
	$option_selected = array (
		'praise_id' => '',
		'praise' => '',
		'author' => '',
		'source' => '',
		'time_added' => '',
		'time_updated' => '',
		'public' => '',
		'img_name' => '',
		'ASC' => '',
		'DESC' => '',
	);
	if(isset($_REQUEST['orderby'])) {
		$sql .= " ORDER BY " . $_REQUEST['orderby'] . " " . $_REQUEST['order'];
		$option_selected[$_REQUEST['orderby']] = " selected=\"selected\"";
		$option_selected[$_REQUEST['order']] = " selected=\"selected\"";
	}
	else {
		$sql .= " ORDER BY praise_id ASC";
		$option_selected['praise_id'] = " selected=\"selected\"";
		$option_selected['ASC'] = " selected=\"selected\"";
	}
	
	if(isset($_REQUEST['paged']) && $_REQUEST['paged'] && is_numeric($_REQUEST['paged']))
		$paged = $_REQUEST['paged'];
	else
		$paged = 1;

	$limit_per_page = 20;
	$total_pages = ceil($num_praises / $limit_per_page);
	if($paged > $total_pages) $paged = $total_pages;

	$admin_url = get_bloginfo('wpurl'). "/wp-admin/admin.php?page=wp-praiser";
	if(isset($_REQUEST['orderby']))
		$admin_url .= "&orderby=".$_REQUEST['orderby']."&order=".$_REQUEST['order'];
	$page_nav = wp_praiser_pagenav($total_pages, $paged, 2, 'paged', $admin_url);
	$start = ($paged - 1) * $limit_per_page;
	$sql .= " LIMIT {$start}, {$limit_per_page}"; 

	// Get all the praises from the database
	$praises = $wpdb->get_results($sql);
	
	foreach($praises as $praise_data) {
		if ($praise_data->img_name=="") {
    	$img_name = plugins_url(). "/wp-praiser/60x60.png";
    } else {
        $img_name = $praise_data->img_name;
	}

		if($alternate) $alternate = "";
		else $alternate = " class=\"alternate\"";
		$praises_list .= "<tr{$alternate}>";
		$praises_list .= "<th scope=\"row\" class=\"check-column\"><input type=\"checkbox\" name=\"bulkcheck[]\" value=\"".$praise_data->praise_id."\" /></th>";
		$praises_list .= "<td>" . $praise_data->praise_id . "</td>";
		$praises_list .= "<td>";
		$praises_list .= wptexturize(nl2br(make_clickable($praise_data->praise)));
    	$praises_list .= "<div class=\"row-actions\"><span class=\"edit\"><a href=\"{$admin_url}&action=editpraise&amp;id=".$praise_data->praise_id."\" class=\"edit\">".__('Edit', 'wp-praiser')."</a></span> | <span class=\"trash\"><a href=\"{$admin_url}&action=delpraise&amp;id=".$praise_data->praise_id."\" onclick=\"return confirm( '".__('Are you sure you want to delete this praise?', 'wp-praiser')."');\" class=\"delete\">".__('Delete', 'wp-praiser')."</a></span></div>";
		$praises_list .= "</td>";
		$praises_list .= "<td><img src=$img_name></td>";
		$praises_list .= "<td>" . make_clickable($praise_data->author);
		if($praise_data->author && $praise_data->source)
			$praises_list .= " / ";
		$praises_list .= make_clickable($praise_data->source) ."</td>";
		$praises_list .= "<td>" . implode(', ', explode(',', $praise_data->tags)) . "</td>";
		if($praise_data->public == 'no') $public = __('No', 'wp-praiser');
		else $public = __('Yes', 'wp-praiser');
		$praises_list .= "<td>" . $public  ."</td>";
		$praises_list .= "</tr>";
	}
	
	if($praises_list) {
		$praises_count = wp_praiser_count();
		$display .= "<form id=\"wp_praiser\" method=\"post\" action=\"".get_bloginfo('wpurl')."/wp-admin/admin.php?page=wp-praiser\">";
		$display .= "<div class=\"tablenav\">";
		$display .= "<div class=\"alignleft actions\">";
		$display .= "<select name=\"bulkaction\">";
		$display .= 	"<option value=\"0\">".__('Bulk Actions')."</option>";
		$display .= 	"<option value=\"delete\">".__('Delete', 'wp-praiser')."</option>";
		$display .= 	"<option value=\"make_public\">".__('Make public', 'wp-praiser')."</option>";
		$display .= 	"<option value=\"keep_private\">".__('Keep private', 'wp-praiser')."</option>";
		$display .= "</select>";	
		$display .= "<input type=\"submit\" name=\"bulkactionsubmit\" value=\"".__('Apply', 'wp-praiser')."\" class=\"button-secondary\" />";
		$display .= "&nbsp;&nbsp;&nbsp;";
		$display .= __('Sort by: ', 'wp-praiser');
		$display .= "<select name=\"orderby\">";
		$display .= "<option value=\"praise_id\"{$option_selected['praise_id']}>".__('praise', 'wp-praiser')." ID</option>";
		$display .= "<option value=\"praise\"{$option_selected['praise']}>".__('praise', 'wp-praiser')."</option>";
		$display .= "<option value=\"img_name\"{$option_selected['img_name']}>".__('Picture', 'wp-praiser')."</option>";
		$display .= "<option value=\"author\"{$option_selected['author']}>".__('Author', 'wp-praiser')."</option>";
		$display .= "<option value=\"source\"{$option_selected['source']}>".__('Source', 'wp-praiser')."</option>";
		$display .= "<option value=\"time_added\"{$option_selected['time_added']}>".__('Date added', 'wp-praiser')."</option>";
		$display .= "<option value=\"time_updated\"{$option_selected['time_updated']}>".__('Date updated', 'wp-praiser')."</option>";
		$display .= "<option value=\"public\"{$option_selected['public']}>".__('Visibility', 'wp-praiser')."</option>";
		$display .= "</select>";
		$display .= "<select name=\"order\"><option{$option_selected['ASC']}>ASC</option><option{$option_selected['DESC']}>DESC</option></select>";
		$display .= "<input type=\"submit\" name=\"orderbysubmit\" value=\"".__('Go', 'wp-praiser')."\" class=\"button-secondary\" />";
		$display .= "</div>";
		$display .= '<div class="tablenav-pages"><span class="displaying-num">'.sprintf(_n('%d praises', '%d praises', $praises_count, 'wp-praiser'), $praises_count).'</span><span class="pagination-links">'. $page_nav. "</span></div>";
		$display .= "<div class=\"clear\"></div>";	
		$display .= "</div>";
		$display .= "<table class=\"widefat\">";
		$display .= "<thead><tr>
			<th class=\"check-column\"><input type=\"checkbox\" onclick=\"wp_praiser_checkAll(document.getElementById('wp_praiser'));\" /></th>
			<th>ID</th><th>".__('The praise', 'wp-praiser')."</th>
			<th>".__('Picture', 'wp-praiser')."</th>
			<th>".__('Author', 'wp-praiser')." / ".__('Source', 'wp-praiser')."</th>
			<th>".__('Tags', 'wp-praiser')."</th>
			<th>".__('Public?', 'wp-praiser')."</th>
		</tr></thead>";
		$display .= "<tbody id=\"the-list\">{$praises_list}</tbody>";
		$display .= "</table>";
		$display .= "<div class=\"tablenav\">";
		$display .= '<div class="tablenav-pages"><span class="displaying-num">'.sprintf(_n('%d praises', '%d praises', $praises_count, 'wp-praiser'), $praises_count).'</span><span class="pagination-links">'. $page_nav. "</span></div>";
		$display .= "<div class=\"clear\"></div>";	
		$display .= "</div>";

		$display .= "</form>";
		$display .= "<br style=\"clear:both;\" />";
	}
	else
		$display .= "<p>".__('No praises in the database', 'wp-praiser')."</p>";
	$display .= "</div>";
	$display .= "<div id=\"addnew\" class=\"wrap\">\n<h2>".__('Add new praise', 'wp-praiser')."</h2>";
	$display .= wp_praiser_editform();
	$display .= "</div>";
	echo $display;
}

function wp_praiser_admin_footer()
{
	?>
<script type="text/javascript">
function wp_praiser_checkAll(form) {
	for (i = 0, n = form.elements.length; i < n; i++) {
		if(form.elements[i].type == "checkbox" && !(form.elements[i].hasAttribute('onclick'))) {
				if(form.elements[i].checked == true)
					form.elements[i].checked = false;
				else
					form.elements[i].checked = true;
		}
	}
}
</script>

	<?php
}

add_action('admin_footer', 'wp_praiser_admin_footer');
?>
