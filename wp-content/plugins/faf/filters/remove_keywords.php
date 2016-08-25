<?php
/**
* Filter: Remove certain content from post (keywords)
*
*/ 
class faf_remove_keywords extends fafFilter 
{
    public static $name = "remove_keywords_from_content"; 
    public static $context = "posts";
    protected static $controls = "filter_value, search_title, search_excerpt, search_content, match_entire_word, match_case"; 
	
	public static function display_help_text()
	{
		 _e("This filter will remove certain keywords in the text. For instance if you are aggrating a 
		newspaper which always starts with a location 'New York-' which is not relevant, you can choose to remove this. 
		You can add multiple keywords comma-seperated. 
		", "faf"); 
	
	}
	
	/* Function not an attribute due for gettext */ 
	public static function get_description()
	{
		return __("Remove keywords from post", "faf"); 
	
	}
	
    public function execute()
    { 	  
        $post = $this->post; 
        $args = $this->args; 
        
      $filter_value = $args["filter_value"];
		
        
        $pattern = preg_quote(wp_specialchars_decode(trim($filter_value))); 
        $modifiers = ""; // after pattern PRCE modifiers


    	 if (empty($pattern))
	    	return $post; // empty filter will always return true; not a good idea 

	if (! isset($args["filter_match_case"]) || $args["filter_match_case"] == 0)
	    $modifiers = "i"; // case insensitive and other preg_match modifiers
	   		
        $split_pattern = preg_split("/,/i",$pattern); // case insensitive match (rough)
        if (count($split_pattern) > 1) // yes multiple keywords
        {
            $pattern = array(); // make it array
            foreach($split_pattern as $item)
            {
            	if (isset($args["filter_match_word"]) && $args["filter_match_word"] == 1)
            	 $pattern[] = "/\b$item\b/$modifiers";
            	else
                 $pattern[] = "/$item/$modifiers";
            }  
        }            
        else  
        {
	    if (isset($args["filter_match_word"]) && $args["filter_match_word"] == 1)
	    	   $pattern = "/\b$pattern\b/$modifiers";	
	    else
       	 	   $pattern = "/$pattern/$modifiers"; // rough and bad - needs updating for more algo's
       		
	}
	  
	   
	    // add all relevant fields to the content check.	
        $content = $post["post_content"]; 
        $excerpt = $post["post_excerpt"];
        $title = $post["post_title"];

        // check for multiple keywords.
        if (isset($args["filter_search_title"]) && $args["filter_search_title"] == 1)
	        $title = preg_replace($pattern, "",$title);

	if (isset($args["filter_search_content"]) && $args["filter_search_content"] == 1)
	        $content = preg_replace($pattern,"",$content);

	if (isset($args["filter_search_excerpt"]) && $args["filter_search_excerpt"] == 1)     	
	      	$excerpt = preg_replace($pattern,"",$excerpt); 

        $post["post_content"] = $content;
        $post["post_excerpt"] = $excerpt;
        $post["post_title"] = $title; 
        return $post; 
    } 
    
    protected static function setControls()
     {
     	$controls = parent::setControls();
     	$controls["filter_value"]["desc"] = __("Keywords:", "faf"); // different description
     	return $controls;
     }  

}   
?>
