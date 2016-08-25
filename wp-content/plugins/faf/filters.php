<?php
/* Filter Class
*
* Abstract class which defines a Feedwordpress Advanced Filters filter. 
*
* @Class fafFilter
* @Author Bas Schuiling 
*/ 
 abstract class fafFilter
 {
    protected $post, $args; 
    public static $name; 

    public static $context; // Posts or Categories 
    protected static $av_controls;
    protected static $controls;
    
    /*  New filter constructor
    *
    * @param Array $post Array with all post data from feed
    * @param Array $args Array which contain all filter settings
    */
    public function __construct($post, $args)
    {
     $this->post = $post;
     $this->args = $args;
     //$this->setControls();
    }
    
     /** Controls defines default HTML controls for settings
     *
     * Child classes can make use of default controls by putting the name as a string in array and 
     * passing it to parent function
     *
     * @since 0.5.5
     */

  protected static function setControls()
   {
     	$controls = array(); 
     	
	$controls["filter_value"] = array(
			"name" => "filter_value",
			"desc" => __("Value", "faf"), 
			"type" => "text",
			"required" => true); 
	$controls["search_title"] = array(
			"name" => "filter_search_title",
			"desc" => __("Search in Title", "faf"), 
			"type" => "checkbox");
	$controls["search_excerpt"] = array(
			"name" => "filter_search_excerpt",
			"desc" => __("Search in Excerpt", "faf"), 
			"type" => "checkbox");
	$controls["search_content"] = array(
			"name" => "filter_search_content",
			"desc" => __("Search in Content", "faf"), 
			"type" => "checkbox",
			"default" => "1");	
	$controls["match_entire_word"] = array( 
			"name" => "filter_match_word",
			"desc" => __("Only match entire word", "faf"),
			"type" => "checkbox");
	$controls["match_case"] = array(
			"name" => "filter_match_case",
			"desc" => __("Only match case", "faf"),
			"type" => "checkbox");
	
     	return $controls;
     
    }
    
    /** Standard display of filter values in Filter overview
    * 
    *  Replaced by getDisplay functions
    */
   /* public function display()
    {
    	 $args = $this->args; 
    	  echo "<div class='faf_filterSetting'><ul>";
	  foreach($args as $arg_name => $arg_value)
	 {
	  		if ($arg_value == 1) $arg_value = "yes"; 
	   		echo "<li>$arg_name :: $arg_value</li>"; 
	   	}
	 		   	
	    echo "</ul></div>";
    } */
    
    /** Display settings in Overview page 
    *
    * This function tries to get find a display function per type of control 
    *
    */
    public static function getDisplay($args = array(), $class = "") 
	{
		//$controls = call_user_func_array($c.'::getControls',$c);
	
	faf_debug(version_compare(PHP_VERSION,"5.3.0","<"));
	
		// php 5.2 workarounds 
		if (version_compare(PHP_VERSION,"5.3.0","<"))	
		{
			$controls = call_user_func_array($class.'::getControls',$class);
		}
		else
		{
			$c = get_called_class(); 
			$controls = $c::getControls(); 
		}
		
		echo "<div class='faf_filterSetting'>";
		foreach($controls as $control)
		{
			$cname = $control["name"]; 
			if (isset($args[$cname]))
				$value = $args[$cname];
			else
				$value = ""; 
				
			$type = $control["type"]; 
			
			if (method_exists($c, "display_" . $type))
			{		
				$method = "display_" . $type;

				// php 5.2 workarounds 
				if (version_compare(PHP_VERSION,"5.3.0","<"))	
					call_user_func_array($c.'::'.$method, array($control, $value)); 
				else
					$c::$method($control, $value);
		
			}
		
		}
		echo "</div>"; 
	}
	
	/** Display a checkbox on overview page */
	protected static function display_checkbox($control, $value)
 	{
 		$desc = $control["desc"]; 
 		if ($value == 1) 
 			echo "&#10003; $desc <br />";
	}
	
	/** Display a Text control on overview page */
	protected static function display_text($control, $value) 
	{
		$desc = $control["desc"]; 
		if (! $value == "")
			echo "<label>$desc:</label> $value <br />";  
	}
	
	/** Display a radio button control on overview page */
	protected static function display_radio($control, $value)
	{

		if(array_key_exists($value, $control["options"]))
		{
			$desc = $control["desc"]; 
			$val  = $control["options"][$value];
			echo "<label>$desc:</label> $val <br />"; 
	
		}
	}

	 
    /** Execute is the a filters main function which performs the actual filtering
    * Params come with the constructor
    * 
    * @return Array $post . Array with all the posts. Failure to do so will result in destruction of all to be syndicated posts
    */
    public abstract function execute(); // should return $post array! 
    
    /** Returns all defined controls of the filter Class
    * 
    * This function will get all controls defined by the respective filter class and check if this control is defined
    * If not control will be ignored 
    * 
    * @return Array Control_array Array of controls ( see documentation for format )
    */
    public static function getControls($class = "" )
    {
    	$control_array = array();
		// php 5.2 workarounds 
		if (version_compare(PHP_VERSION,"5.3.0","<"))
		{
			$vars = get_class_vars($class); 
			$defined_controls = call_user_func($class.'::setControls');
	 	   $use_control = explode(",",$vars['controls']);
		}
		else
		{
			$c = get_called_class();
			$defined_controls = $c::setControls();
			$use_control = explode(",",$c::$controls);
		}



   	foreach($use_control as $control)
	    	{
	    			if (isset($defined_controls[trim($control)]))
		    			 $control_array[] = $defined_controls[trim($control)];		
	    	}

	    
    	return $control_array;
    }	
 }
?>
