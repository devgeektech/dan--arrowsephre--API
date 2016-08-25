<?php
/**
* Filter: Unpublish (expire) posts after X amount of time. 
*
*/ 
class faf_post_expirator extends fafFilter 
{

    public static $name = "faf_expire_post"; 
    public static $context = "posts";
    protected static $controls = "expire_method, expire_time, expire_how, expirator_error"; 

	public static function display_help_text()
	{
		echo "<p>"; 
		_e("This filter allows you to expire posts after a certain time. You can either set to use the date of aggregation or the 
			  date the post has in the feed.", "faf");
		echo "</p>"; 
		echo "<p>";
		 _e("Warning: if you delete posts on expiration date and they are still exist in the feed they will be resyndicated", "faf");
		echo "</p>"; 
	
	}
	
	/* Function not an attribute due for gettext */ 
	public static function get_description()
	{
		return __("Expire posts", "faf"); 
	
	}

    public function execute()
    {
		$post = $this->post; 
		$args = $this->args; 

		if (! is_plugin_active("post-expirator/post-expirator.php"))
		{
			return $post; // no plugin no fun
		}
		
		$post_date_gmt = $post["post_date_gmt"]; 
		$timestamp = 0; 
		
		// set out base time
		switch($args["filter_expire_method"])
		{	case "setdate": 
				$timestamp = time();
			break;
			case "postdate": 
				$timestamp = strtotime($post_date_gmt);
			break;	
		}
		
		$expire_time = $args["filter_expire_time"]; 
		if (! isset($expire_time[0]) || ! is_numeric($expire_time[0]))
			$amount = 0;
		else
			$amount = $expire_time[0];
		switch($expire_time[1])
		{
			case "h":
				$timestamp = strtotime("+$amount hours",$timestamp);
			break;
			case "d":
				$timestamp = strtotime("+$amount days",$timestamp);
			break;
			case "w":
				$timestamp = strtotime("+$amount weeks",$timestamp);
			break;		
			case "m":
				$timestamp = strtotime("+$amount months",$timestamp);
			break;
			case "y":
				$timestamp = strtotime("+$amount years",$timestamp);
			break;		
		}

		$expire_how = $args["filter_expire_how"];
		$expirationType = array("expireType" => $expire_how); 
		
		$post["meta"]["_expiration-date"] = $timestamp;
		$post["meta"]["_expiration-date-options"] = $expirationType; 
		$post["meta"]["_expiration-data-status"] = "saved";
		$post["meta"]["faf_process_expire_date"] = $timestamp;
		
		return $post; 
		
		// Put Post expirator meta data into post_meta 
		// Blurp cron?
	}
	
	 // post process to put expiration items in cron 
    public static function process_complete()
    { 
		global $wpdb; 
		$sql = "SELECT * from $wpdb->postmeta where meta_key = 'faf_process_expire_date'"; 
    	$q = $wpdb->get_results($sql, ARRAY_A);
		foreach($q as $process )
    	{
    		$timestamp = $process["meta_value"]; 
    		$post_id = $process["post_id"]; 
    		$meta = get_post_meta($post_id);
    		$options = $meta["_expiration-date-options"];
    		_scheduleExpiratorEvent($post_id, $timestamp, $options);
    	}
     	$sql = "DELETE from $wpdb->postmeta where meta_key = 'faf_process_expire_date'";
    	$wpdb->query($sql);   	
    	
	}
	
	
	protected static function setControls()
	{
		if (! is_plugin_active("post-expirator/post-expirator.php"))
		{
			$controls["expirator_error"] = array("name" => "expire_need", 
					 "desc" => __("This filter needs <a href='http://wordpress.org/extend/plugins/post-expirator/' target='_blank'>Post expirator</a> plugin to function", "faf"),
												"type" => "expire_need_expirator");
			return $controls; 
		}
		$controls = parent::setControls(); 	
		$controls["expire_method"] = array(
		"name" => "filter_expire_method",
		"desc" => __("Set expire-time:", "faf"),
		"type" => "radio",
		"default" => "setdate",
		"options" => array("postdate" => __("From post date", "faf"),"setdate" => __("From feed update", "faf"))
		);	
		$controls["expire_time"] = array(
		"name" => "filter_expire_time",
		"desc" => __("Time", "faf"),
		"type" => "expire_time_box",
		"default" => array(1,'m') );					   
		$controls["expire_how"] = array(
		"name" => "filter_expire_how",
		"desc" => __("How to expire", "faf"),
		"type" => "expire_how_box",
		"default" => "draft");
		return $controls;				
	}
	
	public static function expire_how_box($page_type, $index = 0, $value = "", $filter_text)
	{
		echo "<label>$filter_text</label>"; 
  		echo "<select name='filter_expire_how[$index]'>"; 
  		$expires = array('draft','delete','private');
  		
  		foreach($expires as $exp) 
  		{
  			$sel = (($value == $exp) ? $sel = "SELECTED" : ""); 
  			echo "<option value='$exp' $sel>" . __($exp); 
  			
  		}  
  		echo "</select>"; 
	}
	
	protected static function display_expire_how_box($control, $value)
	{
		$desc = $control["desc"]; 
		echo "<label>$desc:</label> $value <br />"; 
	
	}

	
	public static function expire_time_box($page_type, $index = 0, $value = "", $filter_text)
	{

		$time_options = self::get_time_options(); 
		echo "<label>$filter_text</label>"; 
		echo "<input type='text' size='3' name='filter_expire_time[$index][0]' value='$value[0]' /> ";
		echo "<select name='filter_expire_time[$index][1]'>";
			foreach($time_options as $n => $v) 
			{
				$selected = (($value[1] == $n) ? "selected" : ""); 
				echo "<option value='$n' $selected> $v"; 
			}
			echo "</select>";
	}
	
	private function get_time_options()
	{
		$time_options = array('h' => __("hour(s)", "faf"),
							  'd' => __("day(s)", "faf"), 
							  'w' => __("week(s)", "faf"),
							  'm' => __("month(s)", "faf"),
							  'y' => __("year(s)", "faf")
							 );
		return $time_options;
	}	
	
	protected static function display_expire_time_box($control,$value)
	{
		$amount = (isset($value[0]) ? $value[0] : 0); 
		$time_options = self::get_time_options(); 
		$time = (isset($value[1]) ? $time_options[$value[1]] : "");
		$desc = $control["desc"]; 
		
		echo "<label>$desc:</label> $amount $time <br />"; 
	
	}
	
	public static function expire_need_expirator($page_type, $index, $current_value, $desc) 
	{
		echo "<div>" . $desc  . " </div>"; 
	}
	
	function display()
	{
		parent::display();
	
	}
}
?>
