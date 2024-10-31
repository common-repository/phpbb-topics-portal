<?php
// ini_set('display_errors', 1);
// error_reporting(E_ALL);
/*
Plugin Name: phpbb_topics_portal
Plugin URI: none
Description: a simple plugin/widget to display recent phpbb forum topics in a Wordpress Widget
Author: macmiller
Version: 1.1
Author URI: none
*/

 include_once('phpbb_topics_portal_Guts.php');

/**
 * phpbb_topics_portal Class
 */

class phpbb_topics_portal extends WP_Widget {


    /** constructor */
    function phpbb_topics_portal() {
        $widget_ops = array('classname' => 'phpbb_topics_portal', 'description' => 'This is a widget which allows recent phpbb forum topics to be displayed within a WP widget');
//      $control_ops = array('width' => 300, 'id_base' => 'phpbb_topics_portal');
        $control_ops = array('width' => 300);
        parent::WP_Widget('phpbb_topics_portal', 'phpbb topics portal', $widget_ops, $control_ops);
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
//      $logfile = fopen("../zzphpbbtopicslog.txt", "a+");
//      fwrite($logfile, "----topics portal widget ---" . "\n");
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        $phpbb_config_location = $instance['phpbb_config_location'];
        $phpbb_url_location = $instance['phpbb_url_location'];
        $exclude_forums = $instance['exclude_forums'];
        $return_list_length = $instance['return_list_length'];
        $topic_text_length = $instance['topic_text_length'];
        $date_format = $instance['date_format'];
        ?>
              <?php echo $before_widget; ?>
                  <?php if ( $title )
                        echo $before_title . $title . $after_title; ?>
              <?php
              $msgR = array();
              $msgR = get_echo_phpbb_info($phpbb_config_location,$phpbb_url_location,$exclude_forums,$return_list_length,$topic_text_length,$date_format);
              if ($msgR['ind'] === FALSE) {
                 echo $msgR['msg'];
              }
              ?>
              <?php echo $after_widget; ?>
        <?php
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
	$instance = $old_instance;
	$instance['title'] = strip_tags($new_instance['title']);
	$instance['phpbb_config_location'] = strip_tags($new_instance['phpbb_config_location']);
	$instance['phpbb_url_location'] = strip_tags($new_instance['phpbb_url_location']);
	$instance['exclude_forums'] = strip_tags($new_instance['exclude_forums']);
	$instance['return_list_length'] = strip_tags($new_instance['return_list_length']);
	$instance['topic_text_length'] = strip_tags($new_instance['topic_text_length']);
	$instance['date_format'] = strip_tags($new_instance['date_format']);
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
//      $logfile = fopen("../zzphpbbtopicslog.txt", "a+");
//      fwrite($logfile, "----topics portal FORM ---" . "\n");


	$defaults = array( 'title' => 'Recent Forum Topics', 
                           'phpbb_config_location' => "{$_SERVER['DOCUMENT_ROOT']}" . '/myforum/config.php',
                           'phpbb_url_location' => '',
                           'exclude_forums' => '17/31/37',
                           'return_list_length' => '15',
                           'topic_text_length' => '30',
                           'date_format' => 'j-M-y g:iA');
	$instance = wp_parse_args( (array) $instance, $defaults );
        $title = esc_attr($instance['title']);
        $phpbb_config_location = esc_attr($instance['phpbb_config_location']);
        $phpbb_url_location = esc_attr($instance['phpbb_url_location']);
        $exclude_forums = esc_attr($instance['exclude_forums']);
        $return_list_length = esc_attr($instance['return_list_length']);
        $topic_text_length = esc_attr($instance['topic_text_length']);
        $date_format = esc_attr($instance['date_format']);
        // variables:
        //   title = the title for the widget as displayed on WP
        //   phpbb_config_location = location for the phpbb config file
        //   phpbb_url_location = url for the forum, optional, may be left blank to have program default values
        //   exclude_forums = a list of forums to exclude delimited by /
              
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
         <p>
          <label for="<?php echo $this->get_field_id('phpbb_config_location'); ?>"><?php _e('phpBB Config Location:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('phpbb_config_location'); ?>" name="<?php echo $this->get_field_name('phpbb_config_location'); ?>" type="text" value="<?php echo $phpbb_config_location; ?>" />
        </p>
         <p>
          <label for="<?php echo $this->get_field_id('phpbb_url_location'); ?>"><?php _e('phpBB Forum URL:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('phpbb_url_location'); ?>" name="<?php echo $this->get_field_name('phpbb_url_location'); ?>" type="text" value="<?php echo $phpbb_url_location; ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('exclude_forums'); ?>"><?php _e('Exclude Forum List:' );?></label>
          <input class="widefat" id="<?php echo $this->get_field_id('exclude_forums'); ?>" name="<?php echo $this->get_field_name('exclude_forums'); ?>" type="text" value="<?php echo $exclude_forums; ?>" />
        </p>
        <p style="width:48%;float:left;">
          <label for="<?php echo $this->get_field_id('return_list_length'); ?>"><?php _e('Return List Length:' );?></label>
          <input class="widefat" id="<?php echo $this->get_field_id('return_list_length'); ?>" name="<?php echo $this->get_field_name('return_list_length'); ?>" type="text" value="<?php echo $return_list_length; ?>" />
        </p>
        <p style="width:48%;float:right;">
          <label for="<?php echo $this->get_field_id('topic text length'); ?>"><?php _e('Topic Text Length:' );?></label>
          <input class="widefat" id="<?php echo $this->get_field_id('topic_text_length'); ?>" name="<?php echo $this->get_field_name('topic_text_length'); ?>" type="text" value="<?php echo $topic_text_length; ?>" />
        </p>
        <p style="width:48%;float:left;">
          <label for="<?php echo $this->get_field_id('date_format'); ?>"><?php _e('Date Format:' );?></label>
          <input class="widefat" id="<?php echo $this->get_field_id('date_format'); ?>" name="<?php echo $this->get_field_name('date_format'); ?>" type="text" value="<?php echo $date_format; ?>" />
        </p>
        <?php
        echo "<br>";
        echo "<br>";

        echo "<label for=" . '"' . "Path_Info" . '"' . "style=" . '"' . "width:98%;float:left;" . '">' . "Document Root Path info to aid in setting config path(info only)</label>";
        echo "<p class=" . '"' . "widefat" . '" "' . "style=" . "width:98%;float:left;" . '">';
        echo $_SERVER['DOCUMENT_ROOT'];
        echo "</p>";
        echo "<br>";
        echo "<br>";

        echo "<label for=\"Param_Validation\">Parameter Validation Return Area (Don't Enter)</label>";
        $indText = "Entered Values Are OK";
        if( !trim($exclude_forums) == "" ) {
            $nbr_ck = true;
           $ex_list = explode('/',$exclude_forums);
           foreach($ex_list as $ex_list_inx => $ex_list_item) {
              if (is_numeric($ex_list_item)) {
                 if (intval($ex_list_item) != floatval($ex_list_item)) {
                    $nbr_ck = false;
                    break;
                 }
              } else {
                 $nbr_ck = false;
              }
           }
           if (!$nbr_ck) {
              $indText = "Exclude forum list must either be blank or indicate a list of forums separated by '/', eg. 10/14/15/24 ";
           }
        }   
        if (is_numeric($return_list_length)) {
           if (intval($return_list_length) != floatval($return_list_length)) {
              $indText = "Return List Length should be an integer";
           }
        }
        if( (!is_numeric($return_list_length)) || ($return_list_length == 0) ) {
           $indText = "Return List Length should be numeric greater than 0";
        }   
        if (is_numeric($topic_text_length)) {
           if (intval($topic_text_length) != floatval($topic_text_length)) {
              $indText = "Topic Text Length should be an integer";
           }
        }
        if( (!is_numeric($topic_text_length)) || ($topic_text_length == 0) ) {
           $indText = "Topic Text Length should be numeric greater than 0";
        }   
        if (trim($date_format) == "") {
           $date_format = 'j-M-y g:iA';
        }
//      $CKonlyphpbb_config_location = '../' . $phpbb_config_location;
        if (trim($phpbb_config_location) == "") {
           $indText = "phpbb config location is required";
        } elseif (!file_exists($phpbb_config_location)) {
           $indText = "phpbb config file can't be opened.  This is the absolute file system reference of the file.  If the forum is at www.example.com/forum it might look like /srv/www/virtual/example.com/htdocs/forum/config.php depending on the file system structure of the host system.  Refer to the Document Root Path for the appropriate prefix to use.";
        }   
        echo "<p class=\"widefat\">";
        echo $indText;
        echo "</p>";

        ?>
        <?php 
    }

} // class phpbb_topics_portal

// register phpbb_topics_portal widget
add_action('widgets_init', create_function('', 'return register_widget("phpbb_topics_portal");'));