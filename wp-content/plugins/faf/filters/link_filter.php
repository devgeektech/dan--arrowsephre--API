<?php 
/** Link filter

Format links from Feeds

@since 0.6 
@author Bas Schuiling
*/

class faf_link_filter extends fafFilter
{
    public static $name = "link_filters"; 
    public static $context = "posts";
	protected static $controls = "search_excerpt, search_content, link_blank, link_tracker"; 

	private $dom; // DOM Representation
	
	public static function display_help_text()
	{
		_e("Formats incoming links to your preferences. If you want to remove links you can use the 'remove HTML from post' filter", "faf"); 
		
	}
	
	public static function get_description()
	{
		return __("Link filter", "faf"); 
	
	}

    public static function setControls()
    {
      $controls = parent::setControls();

      
      $controls["link_blank"] = array(
		"name" => "filter_link_blank",
		"desc" => __("Open links in new window", "faf"), 			
		"type" => "checkbox",
		"default" => "1");   
	  $controls["link_tracker"] = array(
	  	"name" => "filter_link_tracker",
	  	"desc" => __("Run filter through click tracker (URL of tracker)", "faf"), 
	  	"type" => "text" 
	  	); 
	  return $controls;  
     
    }
     
	function execute()
	{
	 	$post = $this->post; 
	 	$args = $this->args;

	 	  	
	 	$search_excerpt = ( isset($args["filter_search_excerpt"]) ? $args["filter_search_excerpt"] : 0); 
	 	$search_content = ( isset($args["filter_search_content"]) ? $args["filter_search_content"] : 0); 
	 	$link_blank = ( isset($args["filter_link_blank"]) ? $args["filter_link_blank"] : 0); 
	 	$link_tracker = ( isset($args["filter_link_tracker"]) ? $args["filter_link_tracker"] : ""); 
		 	
		if ($search_excerpt == 1)
		{
			$post["post_except"] = $this->findBareLinks($post["post_excerpt"]); 
			$this->load_content($post["post_excerpt"]);
			
			$links = $this->get_links();
			if ($link_blank == 1) 
				$this->do_link_blank($links);
			if (isset($link_tracker)) 
				$this->do_link_tracker($links, $link_tracker);
					
			$post["post_excerpt"] =$this->save_content();
			
		}

		if ($search_content == 1)
		{
			$post["post_content"] = $this->findBareLinks($post["post_content"]);
			$this->load_content($post["post_content"]); 
			$links = $this->get_links(); 
			if ($link_blank == 1) 
				$this->do_link_blank($links);
			if (isset($link_tracker)) 
				$this->do_link_tracker($links, $link_tracker);			
			
			$post["post_content"] = $this->save_content();	
		}		
		
		return $post; 
	}
	
	/* Find links without proper <A tags> and convert them to real links 
	
	@param $content String The content which is to be search
	@return String The content searched for links and corrected to basic href. 
	@since 0.6.1
	*/
	protected function findBareLinks($content) 
	{
		$pattern = "/((([A-Za-z]{3,9}:(?:\/\/)?)(?:[-;:&=\+\$,\w]+@)?[A-Za-z0-9.-]+|(?:www.|[-;:&=\+\$,\w]+@)[A-Za-z0-9.-]+)((?:\/[\+~%\/.\w-_]*)?\??(?:[-\+=&;%@.\w_]*)#?(?:[.\!\/\\w]*))?)/i";
		$matches = array();
		preg_match_all($pattern, $content, $matches);
		if (! is_array($matches) || count($matches) == 0) 
			return $content;
		
		foreach($matches[0] as $link)
		{
			$content = str_replace($link, "<a href='$link'>$link</a>", $content); 

		}
		
		return $content;
	}
	
	private function get_links() {
	 
	    $links = array();

	  $links = $this->dom->getElementsByTagName('a'); 
	  return $links;

	}
	
	private function do_link_blank($links)
	{
		foreach($links as $link) 
		{
			$link->setAttribute("target","_blank"); 
		
		}
	}
	
	private function do_link_tracker($links, $link_tracker) 
	{
		if ($link_tracker == "") return;
		
		foreach($links as $link)
		{
			$href = $link->getAttribute('href'); 
			$link->setAttribute('href', $link_tracker . $href);
		}

	}
	
	// Workaround function for PHP DOM adding HTML tags etc
	
	private function load_content($content)
	{
		$content = "<bogus>" . $content . "</bogus>"; 
	 	$this->dom = new DomDocument(); 
	 	$this->dom->preserveWhiteSpace = false;
		@$this->dom->loadHTML($content);
	}
	
	private function save_content() 
	{

		$xpath = "/html/body/bogus/child::node()";

		$DOMXPath = new DOMXPath($this->dom);
		$snippetNode = $DOMXPath->query($xpath);
		$targetDom = new DomDocument(); 

		$childNodes = $snippetNode;
		 for($i =0; $i < $childNodes->length; $i++)
		{
			$importNode = $childNodes->item($i);
			$importedSnippetNode = $targetDom->importNode($importNode,true);
		// and append to our child
		$targetDom->appendChild($importedSnippetNode);
		}
		

		faf_debug($targetDom->saveHTML());
		
		return trim($targetDom->saveHTML());  // trim because saveHTML adds enters for some reason
	}
}

?>
