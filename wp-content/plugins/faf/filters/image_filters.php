<?php
/** 
  Filter :: Filters images, makes a local copy and resize.
*/ 
class faf_image_filter extends fafFilter 
{  

   public static $name = "image_filters"; 
   public static $context = "posts";
   private $image_array = array(); // keep track which images pass by

 protected static $controls = "image_local, image_remove_excerpt,image_size_box, image_process_select, image_remove_unselected, image_set_featured";
 
 	public static function display_help_text()
	{
		echo "<p>";
		 _e("Filter for manipulation of images in your feed. You can remove images from the excerpt or resize
		 			them using the standard WordPress sizes. ", "faf");
		 echo "</p>";
		 echo "<p>";
		 _e("Save images locally: will save the feed image to your local wordpress installation instead 
		 		   of leaving it on the remote host", "faf");
		 echo "</p>";
		 echo "<p>";
		 		   
		 _e("Images to process: in case you have multiple images you can select which ones to process, 
		 		   i.e '1,2,3,4'. This works together with 'remove unselected images'. Every image not processing 
		 		   will be dropped. Leave empty if all images should be processed or you have a feed with only one image", "faf");
		 echo "</p>";
		 echo "<p>";
		
		_e(" Set featured: Will set an image as 'featured image' in the Wordpress post and remove it from your content", "faf"); 

		 echo "</p>";
	
	}
	
	/* Function not an attribute due for gettext */ 
	public static function get_description()
	{
		return __("Image filters", "faf"); 
	
	}

   function execute()
   {
   	faf_debug("Execute image filter");
    $post = $this->post; 
    $args = $this->args; 
    
    $image_local = 0; 
    $resize_to = "";
       
    if (isset($args["filter_image_remove_excerpt"]) && $args["filter_image_remove_excerpt"] == 1) 
    { $post = $this->remove_image_excerpt($post); }
	else 
	  $post = $this->image_process($post,$args,"excerpt");
    
    // all other filters except remove excerpt done there 
    $post = $this->image_process($post,$args,"content");
	faf_debug($post["meta"]);
	// Do Enclosures
	if (isset($post["meta"]["enclosure"])) 
	{	
		faf_debug("Execute : Enclosure images");
	 	$filter_image_local = ( (isset($args["filter_image_local"]) && $args["filter_image_local"] == 1) ? 1 : 0);
	 	if ($filter_image_local == 1)
	 	{
			$post = $this->save_enclosure($post);
    	}
    }
    
    $post = $this->set_metadata($post,$args); // set metadata for processing 
   	return $post;
   }


     protected static function setControls()
     {
	$controls = parent::setControls();     
	$controls["image_local"] = array(
		"name" => "filter_image_local",
		"desc" => __("Save images locally", "faf"), 			
		"type" => "checkbox",
		"default" => "1");     	
	$controls["image_remove_excerpt"] = array(
		"name" => "filter_image_remove_excerpt",
		"desc" => __("Remove images from excerpt", "faf"), 			
		"type" => "checkbox",
		"default" => "1");
	$controls["image_size_box"] = array(
		"name" => "faf_image_resize",
		"desc" => __("Resize image to", "faf"), 			
		"type" => "image_size_box",
		"default" => "medium");	
	$controls["image_process_select"] = array(
		"name" => "faf_image_process_select",
		"desc" => __("Images to process", "faf"), 
		"type" => "text");
	$controls["image_remove_unselected"] = array(
		"name" => "faf_image_remove", 
		"desc" => __("Remove unselected images", "faf"),
		"type" => "checkbox"); 
	$controls["image_set_featured"] = array(
		"name" => "faf_image_featured",
		"desc" => __("Set featured image", "faf"), 
		"type" => "checkbox");			
	return $controls;
     }   
     
     
    /** Process function after full update is completed
    *
    * This function will bind post_id to meta_id since post_id is not yet available during the 
    * normal feed process. Also will check for featured images and set these to post for the same reason
    */
    public static function process_complete()
    { 
    	global $wpdb; 
    	$sql = "SELECT * from $wpdb->postmeta where meta_key = 'faf_process_image' or meta_key = 'faf_featured_image'"; 
    	$q = $wpdb->get_results($sql, ARRAY_A);

		
   		foreach($q as $process )
    	{
    		$post_id = $process["post_id"]; 
			$meta_key = $process["meta_key"]; 
			
			switch($meta_key)
			{
				case 'faf_process_image':
		
				$values = explode(",",$process["meta_value"]); 
				foreach($values as $attach_id) 
				{
					$postAr = array();
					$postAr["ID"] = $attach_id;
					$postAr["post_parent"] = $post_id; 		
					wp_update_post($postAr); 							
				}
				delete_post_meta($post_id,'faf_process_image');
				
				break;
    		case 'faf_featured_image': 	 		  			
 	  			$attach_id = $process["meta_value"]; 
    			if (! has_post_thumbnail($post_id) && is_numeric($attach_id) )
    			{
 	   				set_post_thumbnail($post_id, $attach_id);
 	   				delete_post_meta($post_id,'faf_featured_image');
 	   			}	
 	   			else {
 	   			 faf_debug( is_numeric($attach_id) );
 	   			 faf_debug("Attach Id ( $attach_id ) not integer for post_thumbnail $post_id");
 	   			 }
    		break;
    		default: faf_debug("Process_complete: Meta Keys detected not of usuable type"); 
 	   		break;
 	   		}
 	   	}	
	} 
     
     
  	/*  Display image sizes for resizing (custom filter control)
 	*
 	* Display an box of possible image sizes for image filter resize ( filter control)
 	*
 	* @param String $page_type Context of current page (page or categories)
 	* @param int $index Index number of current filter 
 	* @param String $value Value of current selection ( from database )
 	* @param String $filter_text Display name of filter 
 	*
 	* @since 0.3
	*/ 
  	public static function image_size_box($page_type, $index = 0, $value = "", $filter_text)
  	{
  		$image_sizes = get_intermediate_image_sizes();	
  		array_unshift($image_sizes,__("Do not Resize", "faf")); 	
  		echo "<label>$filter_text</label>"; 
  		echo "<select name='faf_image_resize[$index]'>"; 
  		foreach($image_sizes as $image_size) 
  		{
  			if ($value == $image_size) $sel = "SELECTED"; else $sel = ""; 
  			echo "<option value='$image_size' $sel> $image_size "; 
  		} 
  		echo "</select>"; 
  	
  	} 
  	
  	/** Display the image size box control on overview page */
  	
  	public static function display_image_size_box($control, $value)
  	{
  		echo "<label>" . $control["desc"] . ": </label> $value <br/>"; 
	}
  	    

  
  /** Process an image 
  *
  * This function will get the images to be processed from a process_type switch (content or excerpt )
  * and then delegate the image to be checked according to filter settings
  *
  * @param $post Array the Post Array
  * @param $args Array The filter arguments
  * @param $process_type String Defined which field to check ( content or excerpt ) 
  * @return Array The modified post array 
  */
 private function image_process($post, $args, $process_type = "content") 
 	{ 	
	faf_debug("Image process");
	$content = $post["post_$process_type"]; // excerpt or content
 	
 	$filter_image_local = ( (isset($args["filter_image_local"]) && $args["filter_image_local"] == 1) ? 1 : 0);
 	$image_process = ((isset( $args["faf_image_process_select"])) ?  explode(",",$args["faf_image_process_select"]) : array());
 	$remove_unselected = ((isset($args["faf_image_remove"])) ? $args["faf_image_remove"] : 0); 
 	 	 
 	preg_match_all( '/<img[^>]+src\s*=\s*["\']?([^"\' ]+)[^>]*>/i', $content, $matches, PREG_SET_ORDER );
  
 
 	// kill images that should not be processed
 
 	$result = $this->remove_unwanted_images($content, $matches, $image_process,$remove_unselected); 

	$post["post_$process_type"] = $result[0]; 
	$matches = $result[1]; 
 	$m_count = count($matches); 
 	
 	if($m_count > 0 ) {	
			foreach($matches as $match)
			 {
				$img_full_tag = $match[0]; // full img tag
				$imgsrc = $match[1]; // url
				if( preg_match( '/\.(gif|jpeg|jpg|png)(?:\?(.*))?/i', $imgsrc, $m ) ) {
					faf_debug("Local save:" .$imgsrc);
					$args["img_full_tag"] = $img_full_tag; 
					$args["imgsrc"] = $imgsrc;
					$args["imgext"] = $m[1];
					
					if ($filter_image_local) 
					{
						faf_debug("Image process :: Saving local image");
						$post = $this->save_image_local($post,$args, $process_type);
						
					}	
					else
					{
						$imgdata = array("img_full_tag" => $img_full_tag, "src" => $imgsrc);  
	    				$post["post_$process_type"]  = $this->resize_image_html($post["post_$process_type"],$args, $imgdata);
	    					
	    				}	 
				}
			}
		}	
	else { faf_debug("No image matches"); faf_debug($matches); }

	return $post;
 }
 
   /** Remove images from the excerpt */
   private function remove_image_excerpt($post)
   {
   	$excerpt = $post["post_excerpt"]; 
 	
 	$excerpt = preg_replace('/<img[^>]+src\s*=\s*["\']?([^"\' ]+)[^>]*>/i',"", $excerpt);
   	$post["post_excerpt"] = $excerpt; 
   	
   	return $post; 
   }
   
 
   
    /** Function to check and save enclosures if needed
    *
    * Enclosure is a seperate optional feed in RSS-feeds. This function will check for (multiple) enclosures 
    * and process them as required. 
    * @param $post Array The post Array
    * @return Array The modified Post Array 
    */	
 	private function save_enclosure($post) 
 	{
 		$enclosure = $post["meta"]["enclosure"];
		faf_debug("Enclosure save:");
		faf_debug($enclosure); 
		
 		if (! is_array($enclosure) || $enclosure == "") 
 		{
 			return $post; // nothing to be done. 
 		}
 			
 		foreach($enclosure as $index => $enclosed)
 		{
 			preg_match("/https?:\/\/.*\.(?:png|jpg|jpeg|gif)/i",$enclosed,$match);
 			if (is_array($match) && count($match) > 0 )
 			{
	 			$path  = parse_url($match[0], PHP_URL_PATH);
	 			$pathinfo = pathinfo($path);

	 			$filename = $pathinfo["filename"] . "." . $pathinfo["extension"];
			// start of save local
				try {
					$local_file = $this->grab_remote_image($match[0], $filename); 
					}
				catch (Exception $e)
				{
					faf_debug($e->getMessage());
					return $post;
				}
	 			$attach_id = $this->add_attachment($local_file, $filename);

				$new_file = wp_get_attachment_url($attach_id);
				$new_enc = str_replace($match[0],$new_file,$enclosed);
				faf_debug("New Enclosure:" . $new_enc);
				$post["meta"]["enclosure"][$index] = $new_enc; 
			} else faf_debug("No match on $enclosed"); 
 		}		

 		return $post;	
 	}
 	
 	/** Function to remove images from processing or remove them from post at all
 	*
 	* Implements to option to process selected images and to remove unselected images thus allowing for 
 	* images to be removed from the syndicated post all together 
 	*
 	* @param $content String The content on which the processing is done
 	* @param $matches Array The found images in an array 
 	* @param $process_selected Array The numbers of images to be processed or not ( e.g 1,2,3, ) 
 	* @param $remove_unselected Boolean If true then images not in $process_selected will be removed from post 
 	* @return Array Array contained both the modified content String as well as the modified $matches array
 	*/
 	private function remove_unwanted_images($content, $matches, $process_selected = array(), $remove_unselected = 0) 
 	{ 		
 		if (count($process_selected) == 0) { faf_debug("NO process");  return array($content, $matches); }
 		
 		// check if ALL array items are numeric to prevent wrong user input. Skip if something wrong.
 		foreach($process_selected as $ps) 
 		{
 			if (! is_numeric($ps)) 
 				return array($content, $matches);
 		}
 		
 		$rmatches = array(); 			
 		foreach($matches as $index => $match)
 		{
 			// +1 since user starts counting at 1, not 0
 		  if (! in_array( ($index+1),$process_selected) ) 
 		  {	
 		  	
 		  	$rmatches[] = $index; 
			if ($remove_unselected)
			{
				faf_debug("Remove Unselected: " . $match[0]);
				$content = str_replace($match[0],'',$content); 
			}
			
 		  }
 		}
 		
 		foreach($rmatches as $index)
 			unset($matches[$index]);

 		return array($content, $matches);
 	}

 	/** Function to grab images from remote locations
 	* 
 	* Implements the retrieval of external images using wp_remove_get
 	* 
 	* @param $url The full URL of the image to retrieve
 	* @param $filename The file name under which the new image will be saved
 	* @return String The string of the new URL under which the remote image is saved. 
 	* @Throws Exceptions if there is a problem with writing the file to the upload Dir or 404 if image is not found remotely. 
 	*/
 	private function grab_remote_image($url, $filename)
 	{
		faf_debug("grab remote location : $url"); 
		$result = wp_remote_get($url); 
		
		
		if( is_array($result) ) {
			
 			$response = $result["response"]; // check for 404 


 			if ($response["code"] == '404') 
 				throw new Exception("404 - File not found");
 				 
 			// remove spaces in filenames
 			$filename = str_replace('%20','',$filename);	 
 			
 			$filename = apply_filters('faf-saveimage-filename',$filename, $this->post, $this->args);
 			
			$upload_dir = wp_upload_dir();

			$outfile = $upload_dir['path'] . '/' . $filename;	

			
			/* if file exists, check if file is of the same size, then assume same image and do nothing
			   if of different size, assume two seperate files, append filesize as random string to new image */
			if (file_exists($outfile)) 
			{
				faf_debug("Image exists, checking for same file size");
			   $tmpfile = tempnam('.','imgtest'); 
			   $h = fopen($tmpfile, "w");
			   fwrite($h, $result["body"]);
			  $fs = filesize($tmpfile);

				if (filesize($outfile) != $fs)
				{
					$outfile = $upload_dir['path'] . '/' . $fs . '-' . $filename;	
				}
				fclose($h);
				unlink($tmpfile);	
			}

		    if (!file_exists( $outfile ))
			{
				if( $h = fopen( $outfile, 'w' ) ) {
					fwrite( $h, $result["body"] );
					fclose( $h );

 					 return $outfile;
				} else {
					throw new Exception("File Write Error - Check your uploads map"); 
				} 
			}
			
			
			return $outfile; // we already got it, good 

		}
		throw new Exception("General HTTP Failure"); // HTTP Failure
 	
 	}

	/** Saves an image to local path 
	* 
	* Get the properties of an image and tries to save the file to a local path and rewrite the image tags 
	* to fit the new path, dimensions and title 
	* 
	* @param $post Array The Post Array
	* @param $args Array The Filter Arguments 
	* @param $process_type String 'content' or 'excerpt' - defines on which content to apply 
	* @return Array The modified Post Array
	*/
   private function save_image_local($post, $args, $process_type ) // routine to get and save to image locally
   {
    	//needed : X image title - X image_resize args - X img_full_tag, X imgsrc
    		$title = $post["post_title"]; 
    		$img_full_tag = $args["img_full_tag"]; 
    		$imgsrc = $args["imgsrc"]; 
    		$imgext = $args["imgext"]; 
    		if (isset($args["faf_image_resize"]) ? 
    			$image_resize = $args["faf_image_resize"] : $image_resize = "");

    		
    		$content = $post["post_$process_type"];
    	
    	$path     = parse_url($imgsrc, PHP_URL_PATH);
		$pathinfo = pathinfo($path);
		$filename = $pathinfo["filename"] . "." . $imgext;
		
    	
		// start of save local
		try {
			$local_file = $this->grab_remote_image($imgsrc, $filename); 
			}
		catch (Exception $e)
		{
			faf_debug("Image exception happened: "); 
			faf_debug($e->getMessage());
			return $post;
		}

				$attach_id = $this->add_attachment($local_file, $filename);
								
			    $img_array = image_downsize( $attach_id, $image_resize);
			    $new_img_path = $img_array[0];
			    $new_width = $img_array[1]; 
			    $new_height = $img_array[2];

// workaround for featured images 
				$this->image_array[$local_file]["process_type"] = $process_type;
				$this->image_array[$local_file]["img_src"] = $new_img_path; 
										    
			    $new_img_tag = $img_full_tag; 
			    $new_img_tag = str_replace( $imgsrc, $new_img_path, $new_img_tag); 
			    faf_debug("Replacing images : $img_full_tag - $new_img_tag ON $content");
			  
			    $content = str_replace( $img_full_tag, $new_img_tag, $content ); 

 
				
			    $imgdata = array (	"img_full_tag" => $new_img_tag,
			    			"src" => $new_img_path, 
			    			"title" => $title,
			    		    	"new_width" => $new_width, 
			    		    	"new_height" => $new_height);

				
				
			    $content = $this->resize_image_html($content,$args,$imgdata); 
 				//faf_debug("POST RESULT: " . $post["post_content"]);
 			$post["post_$process_type"] = $content; 
 			
    	 return $post;
    }
    
    /** Function to write attachment data for attaching image
    *
    * This function will add an image to the metadata and post table using the Wordpress insert_attachment process
    * 
    * @param $local_file The local image to attach
    * @param $filename The filename - used for post_title 
    * @return int The attachment_id of the image
    */
    private function add_attachment($local_file, $filename)
    {
    		// taken from codex doc @ http://codex.wordpress.org/Function_Reference/wp_insert_attachment
				$wp_filetype = wp_check_filetype( basename( $local_file ), null );
				$attachment = array(
						'post_mime_type' => $wp_filetype['type'],
						'post_title' => $filename,
						'post_content' => '',
						'post_status' => 'inherit'
						);
						
			$attach_id = wp_insert_attachment( $attachment, $local_file );
			
			$this->image_array[$local_file] = array(
						"mime" => $wp_filetype['type'], 
						"title" => $filename, 
						"attach_id" => $attach_id, 
						); // save it to array 
			
				// you must first include the image.php file
				// for the function wp_generate_attachment_metadata() to work
				require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
				$attach_data = wp_generate_attachment_metadata( $attach_id, $local_file );
							
				wp_update_attachment_metadata( $attach_id,  $attach_data );
				return $attach_id;
    }
    
    /** Sets the metadata of the image for process_complete 
    *
    * Since post_id's are inserted after the feed update is completed, images are added without any attachment to posts
    * To fix this this function will set specified metadata to the database (temporary) to match attachment_id's with 
    * post_id's later.  This procedure is the same for featured images 
    *
    * @param $post Array The Post Array
    * @param $args Array The Filter Args Array
    * @return array The modified $post Array
    *
    */
    private function set_metadata($post, $args)
    {
    	$image_array = $this->image_array; 
    	$image_featured = isset($args["faf_image_featured"]) ? $args["faf_image_featured"] : 0; 	
    	$process = array(); 
    	//faf_debug($image_array);
    	foreach($image_array as $image) 
    	{
    		$attach_id = $image["attach_id"]; 
    		if (isset($image["attach_id"]) && is_numeric($image["attach_id"]))
    		{
				if ($image_featured == 1 && ! isset($post["meta"]["faf_featured_image"])) 
				{
					$post["meta"]["faf_featured_image"] = $attach_id;
					if (isset($image["process_type"]))
					{
						$process_type = $image["process_type"]; 	
						$img_src = $image["img_src"]; 
						// rather dirty :/
						preg_match_all( '/<img[^>]+src\s*=\s*["\']?([^"\' ]+)[^>]*>/i', $post["post_$process_type"], $m, PREG_SET_ORDER );
						foreach($m as $match)
						{
							
							if ($match[1] == $img_src)
								$post["post_$process_type"] = str_replace( $match[0],"", $post["post_$process_type"]); 
						
						}

					} 
				} 
				$process[] = $attach_id; 	
    		}
    		else faf_debug("error with metadata in $image");
    	}
    	$post["meta"]["faf_process_image"] = implode(",",$process);
    	return $post; 
    }
    
    /** Function to apply new sizes to image tags 
    *
    * Checks the new dimension of the image via the metadata given in the $args parameter and rewrites the <img> tag accordingly 
    * 
    * @param $content String Content to be processed
    * @param $args Array Arguments of the filter 
    * @param $imgdata Array The image metadata ( like the new dimensions ) 
    * @return $String The modified content with altered tags
    */
    private function resize_image_html($content, $args, $imgdata)
    {
    	if (! isset($imgdata["img_full_tag"]))
    	 return $post; // nothing
    	
    	$imgsrc = $imgdata["src"]; 
    	$img_full_tag = $imgdata["img_full_tag"]; 
    	
    	$title = (isset($imgdata["title"]) ? $imgdata["title"] : $post["post_title"]); 
    	$new_width = (isset($imgdata["new_width"]) ? $imgdata["new_width"] : 0); 
    	$new_height = (isset($imgdata["new_height"]) ? $imgdata["new_height"] : 0); 
    	
    	if ($new_width == 0 || $new_height == 0)
    	{
    		// todo: try to get images dimensions from tag and check if dimensions are smaller than needed 
    		$image_resize = $args["faf_image_resize"];

		$new_width = get_option($image_resize.'_size_w');
		$new_height = get_option($image_resize.'_size_h');
    	}
        	
    	$new_tag = "<img src=\"$imgsrc\" width=\"$new_width\" height=\"$new_height\" alt=\"$title\" />";
    	$content = str_replace($img_full_tag, $new_tag, $content); 

    	return $content;
    }

} // end class image_filters
?>
