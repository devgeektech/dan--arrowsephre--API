<?php
/* Filter: Add this post to a category when certain keyword is found.
*
*  @since 0.4
*/
class faf_add_keyword_category extends fafFilter
{
    public static $name = "add_keyword_category"; 
    //public static $description = "Add post to category when keywords match"; 
    public static $context = "categories";
    protected static $controls = "filter_value,search_title,search_excerpt,search_content,categories,match_entire_word,match_case";

	public static function display_help_text()
	{
		 _e("Specify keyword(s) and categories. If the keyword matches this post will additionally added to your selected categories
		 You can use multiple keywords by entering them comma-seperated 
		", "faf"); 
	
	}
	
	/* Function not an attribute due for gettext */ 
	public static function get_description()
	{
		return __("Add post to category when keywords match", "faf"); 
	
	}
		
	public function execute()
	{	
	  $post = $this->post; 
	  $args = $this->args; 
	  
	  $post_title = $post["post_title"];
	  $post_content = $post["post_content"]; 
	  $post_excerpt = $post["post_excerpt"]; 
	
	  $modifiers = ""; 
	  $check_content = ""; 
	  	
	// add all relevant fields to the content check.
	  if (isset($args["filter_search_title"]) && $args["filter_search_title"] == 1)
	  	$check_content .= $post_title; 
	  if (isset($args["filter_search_content"]) && $args["filter_search_content"] == 1)
	  	$check_content .= $post_content; 
	  if (isset($args["filter_search_excerpt"]) && $args["filter_search_excerpt"] == 1)
  		$check_content .= $post_excerpt; 	  	
  
   	 $pattern = preg_quote(wp_specialchars_decode(trim($args["filter_value"]))); 
	 if (empty($pattern))
		return; // empty filter will always return true; not a good idea 

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

	  $cats = $args["faf_filter_categories"]; // categories -array- to possibly ad

	  $matches = array(); 

	if (is_array($pattern))  // if array loop over multiple possibilities. Every match leads to inclusion of categories!
	{
	  foreach($pattern as $p) 
		{
		  preg_match($p,$check_content,$matches); 
		  if (count($matches) > 0) 
		  	 break;
		}  	 
	}
	else
	    preg_match($pattern,$check_content,$matches);
		  
	  if(count($matches) > 0) // found, add to category X
	  { 
		// get the wanted categories + post
	    $categories = (isset($post["tax_input"]["category"]) ? $post["tax_input"]["category"] : array()); // is array of categories
		$cats = array_merge($categories, $cats);  
        $post["tax_input"]["category"] = $cats;
      }

      return $post;
	}
	


     
     public static function setControls()
     {
     	$controls = parent::setControls(); 
     	$controls["filter_value"]["desc"] = __("Keyword", "faf");
     	$controls["categories"] = array(
		"name" => "faf_filter_categories",
		"desc" => __("Categories", "faf"), 			
		"type" => "categories_box"
		);
     	return $controls;
     } 
         /**
    * Displays all categories in a box. (Custom Filter Control)
    * 
    * Displays an extra box of categories for category specific filters in category page context.
    * This is a custom filter control.
    * 
    * @param String $page_type Context of current page (page or categories)
    * @param int $index Multiple form field index
    * @param Array $value_array Array of selected categories (from database)
    *
    * @since 0.3
    */
	public static function categories_box ($page_type, $index = 0, $value_array= array() )
	{ 
		if (! is_array($value_array))  // when creating new filter, prevent emptyness
			$value_array = array(); 
			
		$categories = get_categories( array('hide_empty' => '0') ); 
		echo "<label>" . __("Categories") . "</label>";
		echo "<div class='fafCatBox  tabs-panel'><br />"; 
		$name = "faf_filter_categories[$index]";
		$sel = ""; 
        $cIndex = 0; // weird checkbox situaties with name
		
		foreach($categories as $category => $cat)
		{
		    
		 	if ( in_array($cat->cat_ID, $value_array) )
				$sel = "CHECKED"; 
			else
				$sel = "";  
		    ?>
		     <input type="checkbox" <?php echo $sel ?> name="<?php echo $name ?>[<?php echo $cIndex?>]" value="<?php echo $cat->cat_ID ?>" /> 
		     <?php echo $cat->cat_name ?> 
		     <br />
		     <?php
		    $cIndex++;
		}

         echo "</div>";
	}

}
?>
