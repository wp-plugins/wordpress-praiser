<?php
/*
Plugin Name: WordPress Praiser
Description: WordPress Praiser plugin lets you display Praises (or Testimonials) in a sidebar on your WordPress blog or fill a whole page using a short code. This plugin allows for an image of the prasier.
Version: 1.5
Author: cShellFranklin
License: GPL2
*/

/*  Copyright 2015 cShellFranklin

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
/*  This plugin uses some code from Quotes Collection plugin by Srini G found
    at http://srinig.com/wordpress/plugins/quotes-collection/ 
*/
/*  Refer http://codex.wordpress.org/Roles_and_Capabilities */
$wp_praiser_admin_userlevel = 'edit_posts'; 

$wp_praiser_db_version = '1.5'; 

require_once('wp-praiser-widget.php');
require_once('wp-praiser-admin.php');

function wp_praiser_display_praises($title = '', $random = 1, $min_height, $refresh_interval = 5, $show_source = 0, $show_author = 1, $tags = '', $char_limit = 500, $char_size = 11, $full_page = 0, $praise_quote = 1)
{
	$conditions = " WHERE public = 'yes'";
	
	if(char_limit && is_numeric($char_limit)) {
		$conditions .= " AND CHAR_LENGTH(praise) <= ".$char_limit;
	} else {
		$options['char_limit'] = 0;
	}
	
	if($char_size) {
		$char_size_a = $char_size+1;
	}

if($tags) {
		$taglist = explode(',', $tags);
		$tag_conditions = "";
		foreach($taglist as $tag) {
			$tag = mysql_real_escape_string(strip_tags(trim($tag)));
			if($tag_conditions) $tag_conditions .= " OR ";
			$tag_conditions .= "tags = '{$tag}'";
		}
		$conditions .= " AND ({$tag_conditions})";
	}
	
	if($random) {
		$conditions .= " ORDER BY RAND()";
	} else {
		$conditions .= " ORDER BY praise_id DESC";
	}
	
	$praises = wp_praiser_get_praises($conditions);
	
	$min_height .= 'px';
	$html = <<<EOF
	<style>
	.wp_praiser_praises {
		min-height: $min_height;
	}
	</style>
    <script type="text/javascript">
		function nextpraise() {
			if (!jQuery('.wp_praiser_praises').first().hasClass('hovered')) {
				var active = jQuery('.wp_praiser_praises .wp_praiser_active');
				var next = (jQuery('.wp_praiser_praises .wp_praiser_active').next().length > 0) ? jQuery('.wp_praiser_praises .wp_praiser_active').next() : jQuery('.wp_praiser_praises .wp_praiser_praise:first');
				active.fadeOut(1250, function(){
					active.removeClass('wp_praiser_active');
					next.fadeIn(500);
					next.addClass('wp_praiser_active');
				});
			}
		}
		
		jQuery(document).ready(function(){
		    jQuery('.wp_praiser_praises').hover(function() { jQuery(this).addClass('hovered') }, function() { jQuery(this).removeClass('hovered') });
		    setInterval('nextpraise()', $refresh_interval * 1000);
		});
    </script>
EOF;
	if ($title) {
		$html .= "<h4 class=\"wp_praiser\">$title</h4>";
	}
	$html .= '<div class="wp_praiser_praises">';
	$first = true;
	foreach ($praises as $praise) {
    if ($praise['img_name']=="") {
    	$img_name = plugins_url(). "/wordpress-praiser/60x60.png";
    } else {
        $img_name = $praise['img_name'];
	}
     if ($praise_quote==1) {
?>
		<style>
            q:before, q:after {
                content:"";
            }
        </style>
       
<?PHP

	}
		if (!$first) {
			$html .= '<div class="wp_praiser_praise">';
		} else {
			$html .= '<div class="wp_praiser_praise wp_praiser_active">';
			$first = false;
		}
		$html .= "<p style='font-size: " . $char_size . "px;'>";
		$html .= "<img src=". $img_name ." style='float: left; margin: auto 4px;'>";
		$html .= "<q>". $praise['praise'] ."";
		$html .= "</q><BR /><BR />";
		$cite = "";
		if($show_author && $praise['author'])
			$cite = '<span class="wp_praiser_author" style="font-size: '. $char_size_a .'px;">'. $praise['author'] .'</span>';
	
		if($show_source && $praise['source']) {
			if($cite) $cite .= "<BR />";
				$cite .= '<span class="wp_praiser_source" style="font-size: '. $char_size .'px;">'. $praise['source'] .'</span>';
		}
		if($cite) $cite = " <cite>{$cite}</cite>";
		$html .= $cite."</p></div>";	
		
	}
	$html .= '</div>';
	
	echo $html;
}

function wp_praiser_display_full($title = '', $random = 1, $min_height, $refresh_interval = 5, $show_source = 0, $show_author = 1, $tags = '', $char_limit = 500, $char_size = 11, $full_page = 0)
{
	$conditions = " WHERE public = 'yes'";
	
	if(char_limit && is_numeric($char_limit)) {
		$conditions .= " AND CHAR_LENGTH(praise) <= ".$char_limit;
	} else {
		$options['char_limit'] = 0;
	}
	
	if($char_size) {
		$char_size_a = $char_size+1;
	}

if($tags) {
		$taglist = explode(',', $tags);
		$tag_conditions = "";
		foreach($taglist as $tag) {
			$tag = mysql_real_escape_string(strip_tags(trim($tag)));
			if($tag_conditions) $tag_conditions .= " OR ";
			$tag_conditions .= "tags = '{$tag}'";
		}
		$conditions .= " AND ({$tag_conditions})";
	}
	
	if($random) {
		$conditions .= " ORDER BY RAND()";
	} else {
		$conditions .= " ORDER BY praise_id DESC";
	}
	
	$praises = wp_praiser_get_praises($conditions);
	
	$min_height .= 'px';
	$html = '';
    ?>
	<style>
	.wp_praiser_praises_full {
		min-height: $min_height;
	}
	.entry-title {
		width: 100%;
	}
	</style>
<?PHP
	if ($title) {
		$html .= "<h4 class=\"wp_praiser\">$title</h4>";
	}
	$html .= '<div class="wp_praiser_praises_full">';
	$first = true;
	foreach ($praises as $praise) {
    if ($praise['img_name']=="") {
    	$img_name = plugins_url(). "/wordpress-praiser/60x60.png";
    } else {
        $img_name = $praise['img_name'];
	}
	     if ($praise_quote==1) {
?>
		<style>
            q:before, q:after {
                content:"";
            }
        </style>
<?PHP

	}
		if (!$first) {
			$html .= '<div class="wp_praiser_praise_full">';
		} else {
			$html .= '<div class="wp_praiser_praise_full wp_praiser_active_full">';
			$first = false;
		}
		$html .= '<table style="border:none; vertical-align:middle; margin: 0; padding: 5px 5px 5px 5px;">';
		$html .= "<p style='font-size: " . $char_size . "px;'>";
		
		$html .= "<img src=". $img_name ." style='float: left; margin: auto 4px; height:80px; width:80px;'>";
		$html .= "</P></TD><TD style='width: 100%;'>";
		$html .= "<p style='font-size: " . $char_size . "px; width: 80%;'>";
		$html .= "<q>". $praise['praise'] ."";
		$html .= "</q><BR /><BR />";
		$cite = "";
		if($show_author && $praise['author'])
			$cite = '<span class="wp_praiser_author" style="font-size: '. $char_size_a .'px;">'. $praise['author'] .'</span>';
	
		if($show_source && $praise['source']) {
			if($cite) $cite .= "<BR />";
				$cite .= '<span class="wp_praiser_source" style="font-size: '. $char_size .'px;">'. $praise['source'] .'</span>';
		}
		if($cite) $cite = " <cite>{$cite}</cite>";
		$html .= $cite."</p></TD></TR></TABLE><BR></div>";	
		
	}
	$html .= '</div>';
	
	echo $html;
}


function wp_praiser_get_praises($conditions = "")
{
	global $wpdb;
	$sql = "SELECT praise_id, praise, author, source, tags, public, img_name
		FROM " . $wpdb->prefix . "wp_praiser"
		. $conditions;
		
	if($praises = $wpdb->get_results($sql, ARRAY_A))
		return $praises;	
	else
		return array();
}


function wp_praiser_install()
{
	global $wpdb;
	$table_name = $wpdb->prefix . "wp_praiser";

	if(!defined('DB_CHARSET') || !($db_charset = DB_CHARSET))
		$db_charset = 'utf8';
	$db_charset = "CHARACTER SET ".$db_charset;
	if(defined('DB_COLLATE') && $db_collate = DB_COLLATE) 
		$db_collate = "COLLATE ".$db_collate;


	// if table name already exists
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
   		$wpdb->query("ALTER TABLE `{$table_name}` {$db_charset} {$db_collate}");

   		$wpdb->query("ALTER TABLE `{$table_name}` MODIFY praise TEXT {$db_charset} {$db_collate}");

   		$wpdb->query("ALTER TABLE `{$table_name}` MODIFY author VARCHAR(255) {$db_charset} {$db_collate}");

   		$wpdb->query("ALTER TABLE `{$table_name}` MODIFY source VARCHAR(255) {$db_charset} {$db_collate}");

   		if(!($wpdb->get_results("SHOW COLUMNS FROM {$table_name} LIKE 'tags'"))) {
   			$wpdb->query("ALTER TABLE `{$table_name}` ADD `tags` VARCHAR(255) {$db_charset} {$db_collate} AFTER `source`");
		}
   		if(!($wpdb->get_results("SHOW COLUMNS FROM {$table_name} LIKE 'public'"))) {
   			$wpdb->query("ALTER TABLE `{$table_name}` CHANGE `visible` `public` enum('yes', 'no') DEFAULT 'yes' NOT NULL");
		}
        
   		$wpdb->query("ALTER TABLE `{$table_name}` MODIFY img_name LONGTEXT {$db_charset} {$db_collate}");

	}
	else {
		//Creating the table ... fresh!
		$sql = "CREATE TABLE " . $table_name . " (
			praise_id mediumint(9) NOT NULL AUTO_INCREMENT,
			praise TEXT NOT NULL,
			author VARCHAR(255),
			source VARCHAR(255),
			tags VARCHAR(255),
			public enum('yes', 'no') DEFAULT 'yes' NOT NULL,
			time_added datetime NOT NULL,
			time_updated datetime,
 			img_name LONGTEXT NOT NULL,
			PRIMARY KEY  (praise_id)
		) {$db_charset} {$db_collate};";
		$results = $wpdb->query( $sql );
	}
	
	global $wp_praiser_db_version;
	$options = get_option('wp_praiser');
	$options['db_version'] = $wp_praiser_db_version;
	update_option('wp_praiser', $options);

}


function wp_praiser_css_head() 
{
	?>
	<link rel="stylesheet" type="text/css" href="<?php echo plugins_url(); ?>/wordpress-praiser/css/wp-praiser.css" />
	<?php
}

function wp_praiser_enqueue_scripts() 
{
	wp_enqueue_script('jquery');
}

add_action('wp_head', 'wp_praiser_css_head' );
add_action('wp_enqueue_scripts', 'wp_praiser_enqueue_scripts');



register_activation_hook( __FILE__, 'wp_praiser_install' );

function praise_func( $atts ) {
	extract( shortcode_atts( array(
		'foo' => 'something',
	), $atts ) );
	$disp_praises = wp_praiser_full();
	return "$disp_praises";
}
add_shortcode( 'wppraise', 'praise_func' );

?>
