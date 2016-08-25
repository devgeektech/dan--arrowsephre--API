<?php
	  $page_type = $this->get_page_type($page);
	  if ($page->for_feed_settings()) 
	      $global_array = $this->get_filter_options($page, true, true, true);
	  
     	  $filter_array = $this->get_filter_options($page,true,true);

	  $index = 0; // multiple form fields
	  if (isset($filter_array["process_setting"])) 
      {
    	  $process_setting = $filter_array["process_setting"];
    	  unset($filter_array["process_setting"]); // really? yes really.
	  }
	  else
	     $process_setting = "site-default"; 	  
	?>
		
<div class="faf_globalFilterBox"> 
	<p><?php  _e("Filter processing settings","faf") ?> :</p>		
	
<?php // global settings. Should local filter override after / before / not? 
	

		$postSelector = array(
		'only-global' => __("Only excute filters for all feeds (ignore per feed settings)","faf"),
		'no-global' => __("Only execute filters per feed (ignore filters for all feeds) ","faf"),
		'global-first' => __("Execute filters for all feeds first","faf"),
		'global-last' => __("Execute per feed filters first","faf"),
		);
        ?>
        
        <?php if ($page->for_feed_settings()): 
            if (isset($global_array["process_setting"])) 
	    {        $global_setting = $global_array["process_setting"];   

	    }         
	    else
	    	    $global_setting = 'global-first';
            $href = ""; 
        ?>    

             
             <ul class="options global-options"> 
             <?php if ($process_setting == 'site-default') $sel = "CHECKED"; else $sel = ""; ?>
            <li><label><input type="radio" name="faf_process_setting" value="site-default" <?php echo $sel?> />
               <?php _e("Use the site-wide setting", "faf"); ?></label> 
            <span class="current-setting"><?php _e("Currently:", "faf"); ?><strong> <?php echo $postSelector[$global_setting] ?></strong> (<a href="<?php print $href; ?>"><?php _e("change") ?></a>) </span>
            </li>
            </ul>
            
        <?php endif; // for feed settings ?>

	    <ul class="options local-options">
	    <?php
		foreach($postSelector as $value => $text):
		    if($process_setting == $value) 
		        $sel = "CHECKED"; 
		     else
		        $sel = ""; 
		?>  
		<li>
 <input type="radio" name="faf_process_setting" value="<?php echo $value ?>" <?php echo $sel ?> /> <label><?php echo $text ?> 
    </label> 
		</li>
		<?php
		endforeach;
        ?>
     <?php if ($page->for_feed_settings()):  ?>
        </ul>

      <?php endif; ?>
</div>


<p class="setting-description"><?php _e("You can pick any number of Filters. The meaning of the values of every filter can differ. Some have extra options as well", "faf"); ?></p>

<div class="faf_notBox"><?php _e("Please remember to click the Save button after making changes!", "faf") ?></div>
<div class="faf_errorBox"><?php _e("Some fields are required. Please fill them out first!", "faf") ?></div>

	<input type="hidden" name="current_page" value="<?php echo $page_type ?>" /> 
	
	<div id="filters"> 
	  <?php 
	  foreach ($filter_array as $index => $filter):
	        $filter_name = $filter["filter_name"]; 
	       	$filter_args = $filter["filter_args"]; 
	       	$filter_class = $this->available_filters[$page_type][$filter_name]["filter_function"];
	       	
	        if ($filter_name != "")  
	        $filter_text = $this->available_filters[$page_type][$filter_name]["filter_text"];
	        $selected = $filter_name; 
	  ?>

	<div id="faf_filter<?php echo $index ?>" class="faf_filterBox"> 
		<?php  if ($filter_name != "") 
		    {   
		        echo "<input type='hidden' name='faf_filter[$index]' value='$filter_name' />";
		        echo " <h4>" . __($filter_text) . "<span class='delete_filter'>X</span></h4>
		        <span class='faf_bigFilterNr'>" . ($index+1) . "</span>	
		        <div id='faf_filterSetting$index' class='faf_filterSetting'>"; 
		        	$this->showFilterParams($page_type, $filter_name, $index, $filter_args); 
		        echo "</div>"; 
		        
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
		    } 
		?>
		
		
	</div> <!--- /filterdiv --> 

	    <?php endforeach; ?>
	</div> <!--- /filters --> 
	<div >
			<div class="faf_newerrorBox"><?php _e("Error", "faf") ?></div>
		
	<?php 
			if ($index > 0 ) $index++;
		    	 echo "<h2>" . __("New Filter", "faf") . "</h2>";
		      echo $this->showFilterOptions($page,$index); 
			echo "<input type='button' class='button' name='add_filter' id='add_filter' value='" . __("Add Filter", 'faf') . "'>"; 
			echo "<input type='hidden' name='new_filter_index' id='new_filter_index' value='" . $index . "' />";
	

	?>
	</div>
	<br />
	<div class='faf_small'><a href="http://www.weblogmechanic.com/plugins/feedwordpress-advanced-filters/" target="_blank"><?php _e("Feedwordpress Advanced Filters") ?></a> <?php _e("version", 'faf') ?> <?php echo FAF_VERSION ?> <?php _e("by",'faf') ?> Bas Schuiling</div>
	
