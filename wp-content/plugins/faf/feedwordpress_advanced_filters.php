<?php
/*
Plugin Name: Feedwordpress Advanced Filters
Plugin URI: http://www.weblogmechanic.com/plugins/feedwordpress-advanced-filters/
Description: Feedwordpress Advanced Filters allows you to filter and manipulate Feedwordpress posts from feeds. 
Version: 0.6.2
Author: Bas Schuiling
Author URI: http://www.weblogmechanic.com/
License: GPL
*/

/**
* Feedwordpress Advanced Filters
* 
* Feedwordpress Advanced Filters is an addition to the Feedwordpress plugin offering multiple filters for content processing
*
*
* @since 0.1
* @author Bas Schuiling
*/ 

define('FAF_DEBUG',false);
define('FAF_FILTERS',"fafFilter");
define('FAF_VERSION','0.6.2');

class FeedWordPressAdvancedFilters
{
	private $available_filters = array(); // Set by function so class may be extended
    private $current_filter_options; 
	private $process_complete = array(); // run and cleanup after all is done
	/**
	* Class constructor
	*
	* Constructor adds Wordpress actions. 
	*
	* @since 0.1
	* 
	*/
	public function FeedWordPressAdvancedFilters () {

		add_action(
			/*hook=*/ 'feedwordpress_admin_page_posts_meta_boxes',
			/*function=*/ array($this, 'post_meta_boxes'),
			/*priority=*/ 1,
			/*arguments=*/ 1
		);
		add_action(
			/*hook=*/ 'feedwordpress_admin_page_categories_meta_boxes',
			/*function=*/ array($this, 'post_meta_boxes'),
			/*priority=*/ 1,
			/*arguments=*/ 1
		);
		add_action(
			/*hook=*/ 'feedwordpress_admin_page_posts_save',
			/*function=*/ array($this, 'posts_save'),
			/*priority=*/ 100,
			/*arguments=*/ 2
		);
		add_action(
			/*hook=*/ 'feedwordpress_admin_page_categories_save',
			/*function=*/ array($this, 'posts_save'),
			/*priority=*/ 100,
			/*arguments=*/ 2
		);		
 
		add_filter(
			/*hook=*/ 'syndicated_post',
			/*function=*/ array($this, 'faf_decide_filter'),
			/*priority=*/ 1,
			/*arguments=*/ 2
		); 		
		add_action(
			/*hook=*/ 'feedwordpress_update_complete',
			/*function=*/ array($this, 'faf_process_complete'),
			/*priority=*/ 100,
			/*arguments=*/ 2
		);
		  
		add_action('wp_ajax_faf_new_filter',array($this,'faf_new_filter') );
		add_action('wp_ajax_faf_help_text',array($this,'faf_help_text') );		
		
		include_once (plugin_dir_path(__FILE__) . "filters.php"); // should be nicer way 
		 
		$fdir = new DirectoryIterator(plugin_dir_path(__FILE__) . "/filters/"); // Get the defined filters
		foreach($fdir as $fileInfo)
		{
			if (version_compare(PHP_VERSION,"5.3.6","<"))
				$extension = pathinfo($fileInfo->getFilename(), PATHINFO_EXTENSION); // compat for 5.3 - 5.3.5
			else
				$extension = $fileInfo->getExtension();
				
			if ($extension == "php")
			{ 
				include_once($fileInfo->getRealPath());
			}
	
			}     		
		
 		  add_action('init', array($this,'set_available_filters')); // load filters   
	}

	/* Plugin initialisation function
	* 
	* Init is called by Wordpress on plugin startup. Registers styles, javascripting etc to the Wordpress handler 
	* 
	*/
	public function init($hook)
    	{
		// prevents FAF from loading everywhere in wp-admin
    	if (strpos($hook, 'feedwordpress') === false && strpos($hook, 'faf') === false)
			return; 
			    	
		wp_register_style( 'faf_style', plugins_url('faf_style.css', __FILE__) );
		wp_enqueue_style('faf_style');
		wp_register_script('faf-js', plugins_url('faf.js',__FILE__)); 
		wp_enqueue_script('faf-js'); 
		
		$nonce = wp_create_nonce(); 
		wp_localize_script( 'faf-js', 'ajax_object',
           		 array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'we_value' => $nonce,
           		 		'loader_url' => plugins_url('images/ajax-loader.gif',__FILE__ ) ) );    
           		 
		

	}

	/* Load plugin textdomain 
	*
	* @since 0.7
	*/ 
	public function load_plugin_textdomain()
	{
		$domain = 'faf';
		load_plugin_textdomain($domain, FALSE, dirname(plugin_basename(__FILE__)).'/languages/');
	}


	
	/* Init of scripts for FAF. 
	* 
	* This function is seperate because it uses admin_enqueue_scripts to prevent loading from other parts of 
	* WP-admin. 
	*/ 
	/*public function init_script()
	{
	
	
	} */


	/** 
	* Set all available filters 
	*
	* Sets an array of filters to class $av_filters. $av_filters define which filters are available within FAF.
	* Also defines the interface controls and settings for the interface by using "filter_controls" 
	* Format : $av_filters[$context][$filter_name] = array($filter_text => 'Interface name','filter_function => 'callback_functions) 
	* Filter Controls Format: $av_filters[$context][$filter_name]["filter_controls"] = 
	* array (variable name - display name - control style - default [optional] - required [optional] -
	*  js validation function ( on submit )
	*
	* Custom Filter Controls : if control style is not an HTML form element, class will assume custom type 
	* and control style is name of callback function.  
	* Params for function: $page_type, $index, $current_value, $filter_text  
	*
	* This function can be overriden to add additional [user-defined] filters
	*
	* 
	* @since 0.1
	*/
	public function set_available_filters()
	{
	   $classes = get_declared_classes(); 
	   
	   $av_filters = array(); 
	   	
	   foreach($classes as $c)
	   { 
	   	if (is_subclass_of($c, FAF_FILTERS))
		{ 
	 			$name = $c::$name; 
	 			$context = $c::$context; 
 	  			$desc = $c::get_description();
	  	 		$av_filters[$context][$name] = array("filter_text" => $desc, "filter_function" => $c); 
				$av_filters[$context][$name]["filter_controls"] = $c::getControls();
			// done
			
	  		if(method_exists($c,"process_complete"))
	  			$this->process_complete[] = $c; 
		}	
	   }
	  $this->available_filters = $av_filters;			
	}
	
	/** Get available filters 
	*
	* @return Array All filters currently loaded
	*
	* @since 0.5.1
	*/
	public function get_available_filters()
	{
		return $this->available_filters; 
	
	}

	/**
	* Adds a meta box to the interface for filters
	*
	* Meta box function works according to Wordpress admin framework 
	*
	* @since 0.1
	* @see add_meta_box()
	* @see do_meta_boxes()
	*/
	public function post_meta_boxes($page)
	{ 
	    $this->debug = false;
		add_meta_box(
			/*id=*/ 'faf',
			/*title=*/ __('Feedwordpress Advanced Filters', 'faf'),
			/*callback=*/ array($this, 'html_metabox'),
			/*page=*/ $page->meta_box_context(),
			/*context=*/ $page->meta_box_context()
		);
	}
	
	/**
	* Prepare and display filter interface
	* 
	* This functions output the principal HTML interface 
	* 
	* @since 0.1
	* @param Object $FeedWordPress Page object 
	*/
	public function html_metabox($page)
	{		
	 	require_once("faf_admin.php");

	} // html_metabox

    /** 
    * Build and display list of available filters
    * 
    * Uses set available filters to determine which are available in current context and version. This function is used to build new filters
    * selection box.
    * 
    * @param Object $page Page Object
    * @param int $index Multiple form fields parameter 
    * 
    * @return String Returns HTML markup with filters (radio)
    */
	private function showFilterOptions($page,$index =1)
	{
		$string = "<ul class='options'>"; 
		$av_filters = $this->available_filters;
		$page_type = $this->get_page_type($page);
		$av_filters = $av_filters[$page_type]; // get context filters only
		foreach($av_filters as $filter_name => $filter_options)
		{ 
		    if (isset($filter_options["filter_function"]) && isset($filter_options["filter_text"])) // minimum requirements
		    {
			$filter_text = $filter_options["filter_text"];
			$string .= "<li><input type='radio' name='new_filter[$index]' value='$filter_name' />
			    <label>" . 
					$filter_text . "</label></li>\n";
		    }
		} 
		   $string .= "</ul>"; 
		return $string; 
	}

	/** 
	* Load and show settings for specific filter (interface)
	* 
	* Loads the filter controls and settings for controls, including user settings. 
	*
	* @param String $page_type Name of current page in context ( i.e. 'posts' or 'categories' )
	* @param String $filter_name Name of the Filter to get content for
	* @param int $index Index of filter (internal numbering - multiple filter interface )
	* @param Array $filter_args Array or Arguments for filter ( user settings from database )
	*
	**/
	private function showFilterParams($page_type, $filter_name, $index = 0, $filter_args = array() )
	{
		if (! isset($this->available_filters[$page_type][$filter_name]["filter_controls"])) return; // nothing
		$filter_options = $this->available_filters[$page_type][$filter_name]["filter_controls"]; 
		$filter_class = $this->available_filters[$page_type][$filter_name]["filter_function"];
		$filter_validate = array(); // collect validation info
		

		foreach($filter_options as $filter_option) // loop all style elements 
		{
		
			unset( $name, $type, $desc, $default, $required,$options, $jscallback );
			extract($filter_option);				  
		  
		  if (isset($filter_args[$name])) 
			  $current_value = $filter_args[$name]; 
		  else 
		  {
		  	  if (isset($default))
		  	  	$current_value = $default; 
		  	  else
		  	  	$current_value = ""; 
		  }
		  
		  //validation options
		  if (isset($required)) 
		  {
		  	$filter_validate["required"][] = $name;
		  }
		  if (isset($jscallback))
		  {
		  	$filter_validate["jscallback"][] = $jscallback;
		  }
		    
		  echo "<div class='faf_valueBox'>"; 	  
		 switch($type)
		 {
		 	case "checkbox": 
		 		$checked = ($current_value == 1 ? "CHECKED" : "");
		 		echo "<input type='checkbox' name='" .$name ."[$index]' value='1' $checked /> $desc ";  
		 	break;
		 	case "text": 
		 	echo "$desc  <input type='text' name='" .$name ."[$index]' value='$current_value' />  ";
		 	break;
		 	case "radio":
		 	echo "<label>$desc</label> <br/>";
		 		if (isset($options))
		 		{	foreach ($options as $k => $o)
		 			{
		 				$checked = ( ($current_value == $k) ?  "CHECKED" : "");
		 				echo "<input type='radio' name='" . $name . "[$index]' value='$k' $checked /> $o <br />"; 
		 			}
		 		} 
		 	break;
		 	default: // assume special function 
		 		if (method_exists($filter_class,$type)) 
		 		{
		 			// php 5.2 workarounds 
					if (version_compare(PHP_VERSION,"5.3.0","<"))
		 	  		  	call_user_func_array($filter_class.'::'.$type, array($page_type, $index, $current_value, $desc));
		 	  		else		
		 	  			$filter_class::$type($page_type, $index, $current_value, $desc);  
		 	  	}		
		 	break;
		 }
		 echo "</div>"; 
		}
	
		if (isset($filter_validate["required"]) && count($filter_validate["required"]) > 0)
			echo "<input type='hidden' name='required$index' value='" . implode(",",$filter_validate["required"]) . "' />"; 
		if (isset($filter_validate["jscallback"]) && count($filter_validate["jscallback"]) > 0)	
			echo "<input type='hidden' name='jscallback$index' value='" . implode(",",$filter_validate["jscallback"]) . "' />"; 
	}

	/** 
	* Save form settings to Database
	*
	* Gets parameters from form and save settings to datase
	* Note: Parameters a checked for on basis of available filters and filter_controls 
	* Note: This function should do type-based input filtering
	* 
	* @param mixed $params Array of all field values in interface form	
	* @param Object $page Page Object
	*/
	public function posts_save($params, $page)
	{ 

    $index = 0; // multiple filters
    $rIndex = 0; // reIndex in case of deleted filters;     
    $filter_array = array();
     
    if (isset($params["faf_filter"]))
        $param_filter_array = $params["faf_filter"];    
    else
        $param_filter_array = array(); 
      

    foreach ($param_filter_array as $index => $filter_name)
    {
             $filter_array[$rIndex]["filter_name"] = $filter_name;
             // new logic :: Check for expected values in filter_name 
             
    		$page_type = $this->get_page_type($page); 
		
		if ( isset($this->available_filters[$page_type][$filter_name]["filter_controls"])) 
			$filter_controls = $this->available_filters[$page_type][$filter_name]["filter_controls"];    
		else
			$filter_controls = array(); // nothing 
		
			$filter_args = array(); 
		foreach ($filter_controls as $filter_control) 
		{
			unset( $name, $type, $desc, $default, $required, $options, $jscallback );
			 extract($filter_control);
			  // Format all fields - and load into filter_value var 
			
			if (isset($params[$name][$index])) 
			{
				
			   $var = $params[$name][$index]; 
		
			// Field validation goes here. Check for , and trim values for filter_value fields
			
			
			//validation on checkbox value ( and name?) 
			//validation on radio button values ( should be in options, otherwise revert to 'default' ) 
				switch($type)
				{
					case 'checkbox': 
					   if (! $var == 1 )
					   	$var = 0; 			
					break; 
					case 'text': 
						
						$keywords = explode(",",$var);
						$kwar = array();
						foreach($keywords as $kword)
						{
						 $kwar[] = trim($kword);	
						}
						$var = implode(",",$kwar); 
					break; 
					case 'radio': 
						if (! array_key_exists($var,$options))
							$var = $default;	
					break;
			 	}
			 	
				$filter_args[$name] = $var;
			} elseif ($type == "checkbox")
			{
				$filter_args[$name] = 0; // keep checkbox in filter_args if not set otherwise it will interfere with the defaults	
			}
		}        
   			$filter_array[$rIndex]["filter_args"] = $filter_args; 

        	$rIndex++; 
    }

    if (isset($params["faf_process_setting"])) 
    {
        $filter_array["process_setting"] = $params["faf_process_setting"];
    }
    
    $this->save_filter_options($page, $filter_array); 
}

    /** 
    * Determines the current page type and context  
    *
    * In Feedwordpress there is a difference between the multiple pages ( context ) and hence settings.
    * This function is the general way to determine where we are. 
    * 
    * @param Object $page Page Feedwordpress Page Object
    * @return String page_type The name of the current 'context'
    * 
    * @since 0.1
    */
	protected function get_page_type($page)
	{

  	        switch($page->context)
  		{
			case "feedwordpresscategories": 
			case "feedwordpresscategoriesforfeed": 
				$page_type = "categories";
			 break;
			 case "feedwordpresspostspage": 
			 case "feedwordpresspostspageforfeed":
				$page_type = "posts"; 
			 break;
		}
		return $page_type; 
	}

	/**
	*   Retrieve the options for current context. 
	*
	*   Options are saved under global var $faf_filters[page_type][filter_options] 
	*	or page->link->setting[$faf_filters][page_type][filter_options]; 
	*
	* @param Object $page Page Object
	* @param Boolean $context If false ignores any page context settings (page, categories, author etc)
	* @param Boolean $reload If true, settings will be reloaded. Otherwise settings will be loaded once   
	* @return Array Assoc Array of Filter options
	*/
	private function get_filter_options($page, $context = true, $reload = false, $getGlobal = false)
	{
	    /* situaties
	        global in context = ie global post
	        non-global in context = ie post
	        non-global out of context (all local - all global) = save settings in single var
	        alle settings = ie decide filter

	    */
	  if (isset($this->current_filter_options) && is_array($this->current_filter_options && ! $reload)) 
	    return $this->current_filter_options; // no extra work if not needed
	    
	  $page_type = $this->get_page_type($page); 
	  $filter_options = "";
	  $filter_array = array();
	  
	  if ($page->for_feed_settings() && !$getGlobal) 
	  {	
		if (! empty($page->link->settings["faf_advanced_filters_options"]))
		  $filter_array = maybe_unserialize($page->link->settings["faf_advanced_filters_options"]);  
	  }
	  else	
		$filter_array = get_option('faf_advanced_filters_options');  // see http://nacin.com/2010/04/18/wordpress-serializing-data/

		if (! empty($filter_array[$page_type]) && $context ) 	
		{	$filter_array = unserialize($filter_array[$page_type]);                                        	

		}
		elseif ($context) 
			 $filter_array = array(); // Failed or options out-of-context
  	 

  	 
  	    $this->current_filter_options = $filter_array;
  	 	return $filter_array;
	}

	/** Build array of filters to be used for processing
	* 
	* This functions builds the final array of filters to be used. This means it will also take into account if the default feed
	* settings (global filter) should be loaded or skipped. Also loads filters from all page contexts
	*
	* @param $link Feedwordpress SyndiLink object
	* @param $process_setting If set to false function will -not- take global / local settings into account
	* @return Array Assoc Array with all filters for all contexts
	* @since 0.1
	*/
    public function get_filter_tree($link = NULL, $process_setting = true)
   {
      $filter_array = array();
      $global_array = array(); 
      $tree_array = array(); 
      $av_filters = $this->get_available_filters();
 
 	// filter array - context (- index - filter ( filter_name, filter-value) process_setting )
      if (is_object($link) && isset($link->settings["faf_advanced_filters_options"]))
        $filter_array = maybe_unserialize($link->settings["faf_advanced_filters_options"]);
      
      $global_array = get_option("faf_advanced_filters_options"); 
         
   
      foreach($av_filters as $context => $av_filter) 
      {
        $filter_setting = "site-default"; 
        $global_process = "global-first"; 
        $global_context = array();
        $filter_context = array();

        if (isset($filter_array[$context]))
        {
            $filter_context = maybe_unserialize($filter_array[$context]);
            if (isset($filter_context["process_setting"]))
            {    $filter_setting = $filter_context["process_setting"];
                unset($filter_context["process_setting"]); 
            }   
          
             foreach($filter_context as $i => $f)  // make sure it's unserialized, maybe not needed
              $filter_context[$i] = maybe_unserialize($f);
        }
            
        if (isset($global_array[$context])) 
        {   
          $global_context = maybe_unserialize($global_array[$context]); 

         if (isset($global_context["process_setting"])) 
         {
            $global_process = $global_context["process_setting"];
            unset($global_context["process_setting"]);
         }
        }    
         
         
       /*	'only-global' => "Only do Global Filters",
		'no-global' => "Local filters override global filters",
		'global-first' => "Do global filters first",
		'global-last' => "Do global filters last",
		*/
		

	if ($filter_setting == 'site-default')
            $filter_setting = $global_process; 
		 
        if (! $process_setting)
        {
          $tree_array[$context] = array_merge($global_context, $filter_context);    
        }
        elseif ($filter_setting == 'only-global')
          $tree_array[$context] = $global_context;
        elseif($filter_setting  == 'no-global' )
           $tree_array[$context] = $filter_context;
    //    elseif($filter_setting == 'no-global') // local override global, but no local settings -> do global
    //        $tree_array[$context] = $global_context;
        elseif($filter_setting =='global-first')
            $tree_array[$context] = array_merge($global_context,$filter_context);
        elseif($filter_setting =='global-last')
            $tree_array[$context] = array_merge($filter_context,$global_context);
      }
        return $tree_array;
    }

	/**
	* Save filter settings to database
	*    
	* Perform all logic for saving to the correct context, see get filter options. Reloads the options after.
	*
	* @param Object $page Page Object 
	* @param Array $page_options Array of page_options 
	* 
	*/
	private function save_filter_options ($page, $page_options )
	{

		$s_filterArray = serialize($page_options); 
		$page_type = $this->get_page_type($page);  // context based options

	// Get both local and global settings. Decide which ones to run based on global options
		$fOptions = $this->get_filter_options($page,false);
		
		$fOptions[$page_type] = $s_filterArray;

		if ($page->for_feed_settings()) 
			{
				$page->link->settings["faf_advanced_filters_options"] = serialize($fOptions);
				$page->link->save_settings(/*reload=*/ true);	
			}
			else
			{
					update_option('faf_advanced_filters_options',$fOptions);
					
			}
	        $this->get_filter_options($page,true,true);
	}
	
	/** 
	* Filter processing of posts 
	* 
	* Decides on basis of available filters and set options which filters should be invoked 
	* Relies on get_filter_tree to get correct filter sets 
	*
	* @param Array $args Assoc Array with Wordpress post 
	* @param Object $args2 SyndiPost (Feedwordpress) Object
	*
	* @return Array $post Assoc Array with (updated) Wordpress post
	*/
	public function faf_decide_filter($args, $args2)
	{
		// Syndicate_item hook. $args = assoc array of post to be inserted
		faf_debug("FAF deciding on filters on post to be syndicated:");
		faf_debug($args["post_title"]);
		faf_debug($args);
		 		// return $args; 
		 		 
		$post = $args;
	
		$av_filters = $this->available_filters;
		if (empty($av_filters)) // something really wrong
			return; // nothing to do
		
		// will loop trough all filters selected with this feed.
		$syndiPost = $args2; // args 2 is of Syndipost class
    	
 
    	
     	$filter_array = $this->get_filter_tree($syndiPost->link);

		if (empty($filter_array) || ! is_array($filter_array)) 
		{
			return $post;
		}		
		
		// context is page like posts or categories. Defines available filters.
		foreach($filter_array as $context => $filters)
		{
		    foreach($filters as $index => $filter)
		    {
        		if (isset($filter["filter_name"]))
	        		$filter_name = $filter["filter_name"];
                else 
                    $filter_name = ""; //  no filter no action
            
			    if (isset($av_filters[$context][$filter_name]))

			    {
				    $filter_function = $av_filters[$context][$filter_name]["filter_function"]; 
				    $filter_args = $filter["filter_args"]; 
					faf_debug("Doing filter:" . $filter_function);
					
				    $filterObj = new $filter_function($post,$filter_args); 
				    $post = $filterObj->execute(); // do it!
			    }

			 }     
		}
		
		// quality control in case filter crashes, still syndicate posts
		if (! is_array($post) || count($post) < 1)
		{
			faf_debug("Serious problem: Filters returned no post"); 
			$post = $args; 
		}	
		 faf_debug("Decide filter: Returning post, everything seems orderly :" . $post["post_title"]);
		 faf_debug($post);
		 
		 return $post; 
	}
	
	function faf_process_complete($numbers)
	{
		foreach($this->process_complete as $c)
		{
		
			// php 5.2 workarounds 
			if (version_compare(PHP_VERSION,"5.3.0","<"))
				call_user_func($c.'::process_complete');
			else
				$c::process_complete(); 
		}
	}

	/**
	* AJAX Function for loading new filter settings
	* 
	* Load settings for a newly created filter usign AJAX-call 
	* 
	* @since 0.4
	*/
	public function faf_new_filter() 
	{
		$page_type = $_POST["page_type"]; 
		$new_filter = $_POST["new_filter"]; 
		$index = $_POST["filter_index"]; 
		$this->showFilterParams($page_type,$new_filter,$index); 
		die();
	} 
	
	public function faf_help_text()
	{
		$filter_name = $_POST["new_filter"]; 
		$page_type = $_POST["page_type"]; 
		
		$filter_class = $this->available_filters[$page_type][$filter_name]["filter_function"];
	   if (method_exists($filter_class,'display_help_text')) 
	   {
	  	    echo "<div class='faf_filterHelpBox'>";
	        echo "?"; 
	        echo "<span class='faf_filterHelpText'>";
	        
	        // php 5.2 workarounds 
			if (version_compare(PHP_VERSION,"5.3.0","<"))
		        call_user_func($filter_class.'::display_help_text');
			else
		        $filter_class::display_help_text();
		        
	        echo "</span>"; 
	      	echo "</div>";
	   } 
		die();
	}
	
} // Class 

	check_feedwordpress_exist();
	$faf = new FeedWordPressAdvancedFilters;
	
if (is_admin())
{
	add_action("admin_menu","overviewSubMenu",100); 
	add_action('admin_enqueue_scripts', array($faf, 'init'));
	add_action('init', array($faf, 'load_plugin_textdomain'));
//	add_action('admin_enqueue_scripts', array($faf,'init_script') );
	register_activation_hook (__FILE__, 'faf_install');
	register_uninstall_hook (__FILE__, 'faf_uninstall');
}

/** 
* Add subpage for overview page
* 
* Sets an overview page to the Feedwordpress submenu
*
*/
function overviewSubMenu()
{   	
	$menu_cap = FeedWordPress::menu_cap();
	$settings_cap = FeedWordPress::menu_cap(/*sub=*/ true);
	$syndicationMenu = FeedWordPress::path('syndication.php');
	$dirpath = plugin_basename(__DIR__) . "/";
	
	add_submenu_page(
		$syndicationMenu, 'Advanced Filters Overview', 'Advanced Filters Overview',
		$settings_cap,  $dirpath . 'advanced_filters_overview.php'   	);
    		
		if (FAF_DEBUG == true)
		{
    		add_submenu_page(
			$syndicationMenu, 'FAF Dbug', 'Advanced Filters Dbug',
			$settings_cap, plugin_dir_path(__FILE__) . 'dbug.php' 	);			
			    		add_submenu_page(
			$syndicationMenu, 'FAF Unit Text', 'Advanced Filters Unit Test',
			$settings_cap, plugin_dir_path(__FILE__) . 'unit_test.php' 	);	
		}
}
/** Install Feedwordpress plugin
*
* Called with Wordpress plugin activation hook to set up the plugin
*
* @since 0.4
*/
function faf_install()
{
 check_feedwordpress_exist(); 
 global $wpdb; 
 
 
 /* For old versions - this will clean-up for a few versions*/
     global $wpdb; 
    $table = $wpdb->prefix . "faftemp"; 
    $sql = "DROP TABLE IF EXISTS $table";
    
    $wpdb->query( $sql );
 
  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $sql );
}
 
/** Uninstall plugin
* 
* Called with Wordpress uninstall plugin hook. Cleans up database and removes plugin settings
* @since 0.4 
*/
function faf_uninstall()
{
    $cat_id = FeedWordpress::link_category_id();
    $links = get_bookmarks( array("category" => $cat_id) ); 
	foreach($links as $l => $link) 
	{	
		$Slink = new SyndicatedLink($link);    
  		unset($Slink->settings["faf_advanced_filters_options"]);
  		$Slink->save_settings();
  	}

    delete_option("faf_advanced_filters_options");
    global $wpdb; 
    $table = $wpdb->prefix . "faftemp"; 
    $sql = "DROP TABLE IF EXISTS $table";
    
    $wpdb->query( $sql );
}

/** Check if Feedwordpress plugin is loaded
*
* Feedwordpress plugin is required for this plugin, hence we need a check to see if it is there. 
* Display angry message if not. Also check for minimum required PHP version
*/
function check_feedwordpress_exist()
{
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
 if (! is_plugin_active('feedwordpress/feedwordpress.php')) 
 {
 	deactivate_plugins( __FILE__  );
 	die( __("Feedwordpress plugin is required for Feedwordpress Advanced Filters to work!") );
 }
 
}

	
	/* Internal Debug Function
	*
	* If debugging is available and needed outputs debug messages
	*
	* @param String $msg Debug Message
	*/
	 function faf_debug($msg)
	{
		echo "<p>" ;
		print_r($msg);
		echo  "</p>"; 
		if (function_exists("console"))
		{	
			console($msg); 	
		} 
	//	FeedWordpress::diagnostic('updated_feeds:errors',$msg);
	//	error_log($msg); 
	}
	
	
?>
