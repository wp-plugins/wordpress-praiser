<?php

function wp_praiser_widget_init()
{
	if(function_exists('load_plugin_textdomain'))
		load_plugin_textdomain('wp-praiser', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	
	if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') )
		return;
	
	function wp_praiser_widget() {
		$options = get_option('wp_praiser');
		$title = isset($options['title'])?apply_filters('the_title', $options['title']):__('WordPress Praiser Widget', 'wp-praiser');
		$min_height = isset($options['min_height'])?$options['min_height']:150;
		$show_author = isset($options['show_author'])?$options['show_author']:1;
		$show_source = isset($options['show_source'])?$options['show_source']:1;
		$random_order = isset($options['random_order'])?$options['random_order']:1;
		$refresh_interval = isset($options['refresh_interval'])?$options['refresh_interval']:5;
		$praise_quote = isset($options['praise_quote'])?$options['praise_quote']:1;
		$char_limit = $options['char_limit'];
		$char_size = $options['char_size'];
		$page_exclude = $options['page_exclude'];
		$tags = $options['tags'];
		$pickle = get_the_title();
		if ($pickle != $page_exclude){
		if($praises = wp_praiser_display_praises($title, $random_order, $min_height, $refresh_interval, $show_source, $show_author, $tags, $char_limit, $char_size, $full_page, $praise_quote)) {
			echo $before_widget;
			if($title) echo $before_title . $title . $after_title . "\n";
			echo $praises;
			echo $after_widget;
		}
		}
	}

	function wp_praiser_full() {
		$options = get_option('wp_praiser');
		$title = '';
		$min_height = 1000;
		$show_author = 1;
		$show_source = 1;
		$random_order = 0;
		$praise_quote = 0;
		$refresh_interval = 999999;
		$char_limit = 1000;
		$char_size = 12;
		$tags = '';
		$full_page = 1;
		$page_exclude = "";
		if($praises = wp_praiser_display_full($title, $random_order, $min_height, $refresh_interval, $show_source, $show_author, $tags, $char_limit, $char_size, $full_page, $page_exclude, $praise_quote)) {
			echo $before_widget;
			if($title) echo $before_title . $title . $after_title . "\n";
			echo $praises;
			echo $after_widget;
		}
	}

	function wp_praiser_widget_control()
	{
		// default values for options
		$options = array(
			'title' => __('WordPress Praiser Widget', 'wp-praiser'), 
			'min_height' => 150,
			'show_author' => 1,
			'show_source' => 0, 
			'random_order' => 1,
			'praise_quote' => 0,
			'refresh_interval' => 5,
			'tags' => '',
			'char_limit' => 500,
			'char_size' => 11,
			'page_exclude' => ''
		);

		if($options_saved = get_option('wp_praiser'))
			$options = array_merge($options, $options_saved);
			
		// Update options in db when user updates options in the widget page
		if(isset($_REQUEST['wp_praiser-submit']) && $_REQUEST['wp_praiser-submit']) {
			$options['title'] = strip_tags(stripslashes($_REQUEST['wp_praiser-title']));
			$options['min_height'] = strip_tags(stripslashes($_REQUEST['wp_praiser-min_height']));
			$options['show_author'] = (isset($_REQUEST['wp_praiser-show_author']) && $_REQUEST['wp_praiser-show_author'])?1:0;
			$options['show_source'] = (isset($_REQUEST['wp_praiser-show_source']) && $_REQUEST['wp_praiser-show_source'])?1:0;
			$options['refresh_interval'] = strip_tags(stripslashes($_REQUEST['wp_praiser-refresh_interval']));
			$options['random_order'] = (isset($_REQUEST['wp_praiser-random_order']) && $_REQUEST['wp_praiser-random_order'])?1:0;
			$options['tags'] = strip_tags(stripslashes($_REQUEST['wp_praiser-tags']));
			$options['page_exclude'] = strip_tags(stripslashes($_REQUEST['wp_praiser-page_exclude']));
			$options['char_limit'] = strip_tags(stripslashes($_REQUEST['wp_praiser-char_limit']));
			$options['praise_quote'] = (isset($_REQUEST['wp_praiser-praise_quote']) && $_REQUEST['wp_praiser-praise_quote'])?1:0;
			if(!$options['char_limit'])
				$options['char_limit'] = __('none', 'wp_praiser');
			$options['char_size'] = strip_tags(stripslashes($_REQUEST['wp_praiser-char_size']));
			if(!$options['char_size'])
				$options['char_size'] = __('none', 'wp_praiser');
			update_option('wp_praiser', $options);
		}

		// Now we define the display of widget options menu
		$show_author_checked = $show_source_checked	= $random_order_checked = $praise_quote_checked = '';
		$int_select = array ( '5' => '', '10' => '', '15' => '', '20' => '');
        if($options['show_author'])
        	$show_author_checked = ' checked="checked"';
        if($options['show_source'])
        	$show_source_checked = ' checked="checked"';
        if($options['random_order'])
        	$random_order_checked = ' checked="checked"';
        if($options['praise_quote'])
        	$praise_quote_checked = ' checked="checked"';
		echo "<p style=\"text-align:left;\"><label for=\"wp_praiser-title\">".__('Title', 'wp-praiser')." </label><input class=\"widefat\" type=\"text\" id=\"wp_praiser-title\" name=\"wp_praiser-title\" value=\"".htmlspecialchars($options['title'], ENT_QUOTES)."\" /></p>";
		echo "<p style=\"text-align:left;\"><label for=\"wp_praiser-min_height\">".__('Minimum Height', 'wp-praiser')." </label><input class=\"widefat\" type=\"text\" id=\"wp_praiser-min_height\" name=\"wp_praiser-min_height\" value=\"".htmlspecialchars($options['min_height'], ENT_QUOTES)."\" /><br/><span class=\"setting-description\"><small>".__('Minimum height in px, this must be set to a value that suits your logest praise (increase this value if you find that your praises are getting cut off).', 'wp-praiser')."</small></span></p>";
		echo "<p style=\"text-align:left;\"><input type=\"checkbox\" id=\"wp_praiser-show_author\" name=\"wp_praiser-show_author\" value=\"1\"{$show_author_checked} /> <label for=\"wp_praiser-show_author\">".__('Show author?', 'wp-praiser')."</label></p>";
		echo "<p style=\"text-align:left;\"><input type=\"checkbox\" id=\"wp_praiser-show_source\" name=\"wp_praiser-show_source\" value=\"1\"{$show_source_checked} /> <label for=\"wp_praiser-show_source\">".__('Show source?', 'wp-praiser')."</label></p>";
		echo "<p style=\"text-align:left;\"><small><a id=\"wp_praiser-adv_key\" style=\"cursor:pointer;\" onclick=\"jQuery('div#wp_praiser-adv_opts').slideToggle();\">".__('Advanced options', 'wp-praiser')." &raquo;</a></small></p>";
		echo "<div id=\"wp_praiser-adv_opts\" style=\"display:none\">";
		echo "<p style=\"text-align:left;\"><label for=\"wp_praiser-refresh_interval\">".__('Refresh Interval', 'wp-praiser')." </label><input class=\"widefat\" type=\"text\" id=\"wp_praiser-refresh_interval\" name=\"wp_praiser-refresh_interval\" value=\"".htmlspecialchars($options['refresh_interval'], ENT_QUOTES)."\" /><br/><span class=\"setting-description\"><small>".__('In seconds.', 'wp-praiser')."</small></span></p>";
		echo "<p style=\"text-align:left;\"><input type=\"checkbox\" id=\"wp_praiser-random_order\" name=\"wp_praiser-random_order\" value=\"1\"{$random_order_checked} /> <label for=\"wp_praiser-random_order\">".__('Random order', 'wp-praiser')."</label><br/><span class=\"setting-description\"><small>".__('Unchecking this will rotate praises in the order added, latest first.', 'wp-_praiser')."</small></span></p>";
		echo "<p style=\"text-align:left;\"><input type=\"checkbox\" id=\"wp_praiser-praise_quote\" name=\"wp_praiser-praise_quote\" value=\"1\"{$praise_quote_checked} /> <label for=\"wp_praiser-praise_quote\">".__('Praise Quotes', 'wp-praiser')."</label><br/><span class=\"setting-description\"><small>".__('Checking this will place the Quote makes around the Praises.', 'wp-_praiser')."</small></span></p>";
		echo "<p style=\"text-align:left;\"><label for=\"wp_praiser-tags\">".__('Tags filter', 'wp-praiser')." </label><input class=\"widefat\" type=\"text\" id=\"wp_praiser-tags\" name=\"wp_praiser-tags\" value=\"".htmlspecialchars($options['tags'], ENT_QUOTES)."\" /><br/><span class=\"setting-description\"><small>".__('Comma separated', 'wp-praiser')."</small></span></p>";
		echo "<p style=\"text-align:left;\"><label for=\"wp_praiser-char_limit\">".__('Character limit', 'wp-praiser')." </label><input class=\"widefat\" type=\"text\" id=\"wp_praiser-char_limit\" name=\"wp_praiser-char_limit\" value=\"".htmlspecialchars($options['char_limit'], ENT_QUOTES)."\" /></p>";
		echo "<p style=\"text-align:left;\"><label for=\"wp_praiser-char_size\">".__('Font Size', 'wp-praiser')." </label><input class=\"widefat\" type=\"text\" id=\"wp_praiser-char_size\" name=\"wp_praiser-char_size\" value=\"".htmlspecialchars($options['char_size'], ENT_QUOTES)."\" /><br/><span class=\"setting-description\"><small>".__('Default is 11. Best between 9 and 14', 'wp-praiser')."</small></span></p>";
		
		echo "<p style=\"text-align:left;\"><label for=\"wp_praiser-page_exclude\">".__('Name of Praiser Page', 'wp-praiser')." </label>";
		echo "<select class=\"widefat\" id=\"wp_praiser-page_exclude\" name=\"wp_praiser-page_exclude\"> ";
echo "<option value=\"".htmlspecialchars($options['page_exclude'], ENT_QUOTES)."\">".htmlspecialchars($options['page_exclude'], ENT_QUOTES)."</option> ";
  $pages = get_pages(); 
  foreach ( $pages as $pagg ) {
  	$option = '<option value="' . $pagg->post_title . '">';
	$option .= $pagg->post_title;
	$option .= '</option>';
	echo $option;
  }
  echo "<option value=\"Display on all pages\">Display on all pages</option> ";

echo "</select>";
		echo "<br/><span class=\"setting-description\"><small>".__('Choose the page to display all Praises', 'wp-praiser')."</small></span></p>";
		
		echo "</div>";
		echo "<input type=\"hidden\" id=\"wp_praiser-submit\" name=\"wp_praiser-submit\" value=\"1\" />";
	}

	if ( function_exists( 'wp_register_sidebar_widget' ) ) {
		wp_register_sidebar_widget( 'wp_praiser', 'WordPress Praiser Widget', 'wp_praiser_widget' );
		wp_register_widget_control( 'wp_praiser', 'WordPress Praiser Widget', 'wp_praiser_widget_control', 250, 350 );
	} else {
		register_sidebar_widget(array('WordPress Praiser Widget', 'widgets'), 'wp_praiser_widget');
		register_widget_control('WordPress Praiser Widget', 'wp_praiser_widget_control', 250, 350);
	}
}

add_action('plugins_loaded', 'wp_praiser_widget_init');
?>
