<?php
/**
* Page to overview all set filters in one view. 
* 
* Loads all filters and output per feed which filters are currently being invoked on that feed
* 
*/

class FafOverviewPage extends FeedWordPressAdminPage
{

protected $boxes_by_methods = array();



public function display()
{
    
    $this->boxes_by_methods = array('faf_filters_overview' => 'Advanced filters Overview');
    //parent::display();
   $this->faf_filters_overview();
}

/** 
* Outputs an overview of all filters. 
*
* @since 0.4
*/
public function faf_filters_overview()
{

echo "<h2>" . __("Feedwordpress Advanced Filters Overview","faf") . "</h2>"; 

 _e("This page will display the filters from all feeds in the order they will be executed on the posts", "faf"); 

$faf = new FeedwordpressAdvancedFilters(); 

$cat_id = FeedWordpress::link_category_id();
$links = get_bookmarks( array("category" => $cat_id) ); 

foreach($links as $l => $link) 
{ 
 	$Slink = new SyndicatedLink($link);
 	$filter_array = $faf->get_filter_tree($Slink); 
 	
 			 echo "<a name='#" . $link->link_name . "' ></a>"; 
			 echo "<hr /><h3>" . $link->link_name  . "</h3>"; 

			
	foreach($filter_array as $context => $filters) 
	{

 		if (count($filters) > 0)
 		{


	 		foreach($filters as $filter)
	 		{
	 		  $name = $filter["filter_name"]; 
	 		  $args = $filter["filter_args"];
	 		  

	 		 // echo "<div class='faf_filterSetting'><ul>";
	 		  $av_filters = $faf->get_available_filters(); 
	 		  $filter_function = $av_filters[$context][$name]["filter_function"];
	
				if (version_compare(PHP_VERSION,"5.3.0","<"))
					$desc = call_user_func($filter_function.'::get_description'); //php 5.2 compat.
				else
					$desc = $filter_function::get_description();  
				
	 			
	 		  echo "<div class='faf_filterBox'><h4>$desc</h4>"; 
	 		   //$filterObj = new $filter_function(array(),$args); 
	 		   //$filterObj->display(); 
	 		   
	 		//   $filter_function::getDisplay($args); 
	 		  if (version_compare(PHP_VERSION,"5.3.0","<"))
	 			   call_user_func_array($filter_function.'::getDisplay',array($args, $filter_function)); 
	 		  else
	 		  	  $filter_function::getDisplay($args);
	 		  	  
	 		  	/* foreach($args as $arg_name => $arg_value)
	 		  	{
	 		  		if ($arg_value == 1) $arg_value = "yes"; 
	 		  		echo "<li>$arg_name :: $arg_value</li>"; 
	 		   	} */
	 		   	
	 		  // echo "</ul></div></p>";  
	 		  echo "</div>"; 	
	 		}
 		}
 	}
}
echo "<div class='faf_small'><a href=\"http://www.weblogmechanic.com/plugins/feedwordpress-advanced-filters/\" target=\"_blank\">Feedwordpress Advanced Filters</a> 		version " . FAF_VERSION . " by Bas Schuiling</div>";

} // faf_filters_overview(); 

}
$faf = new FafOverviewPage; 
$faf->display(); 
?>
