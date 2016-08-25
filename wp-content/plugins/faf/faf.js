var jq = jQuery.noConflict();
jQuery(document).ready(function () {

	jq("#add_filter").click(function() { // Click on add Filter 
		
		var index = parseInt(jq('#new_filter_index').val());
		var new_filter = jq('input[name=\'new_filter[' + index + ']\']:checked').val();
	 	var new_filter_name = jq('input[name=\'new_filter[' + index + ']\']:checked').next('label').text();
	
	
		if (typeof new_filter == "undefined") 
		{	

			jq('.faf_newerrorBox').css("display","block");
			jq('.faf_newerrorBox').text ("No Filter selected!"); 
			return;
		} else jq('.faf_newerrorBox').css("display","none");
		
		page_type = jq('input[name=\'current_page\']').val();
		
		
		if(jq('.faf_filterSetting').last().length)
		{
			obj = jq('.faf_filterSetting').last();		
			index = obj.attr('id').replace('faf_filterSetting','');
			index++;
		
		}
		
		var data = {
			action: 'faf_new_filter',
			nonce: ajax_object.we_value,
			page_type: page_type,
			new_filter: new_filter,
			filter_index: index
		};		
		
		var bnIndex = index+1;
		
		jq('#filters').append('<div id="faf_filter' + index + '" class="faf_filterBox faf_newFilterBox"> \
		<input type="hidden" name="faf_filter[' + index + ']" value="' + new_filter + '"/> \
               <h4>' + new_filter_name + ' <span class="delete_filter">X</span></h4> \
                 <span class="faf_bigFilterNr">' + bnIndex + '</span> \
                 <div id="faf_loading' + index + '" class="faf_loading"><img src="' + ajax_object.loader_url + '" /></div> \
               	 <div id="faf_filterSetting' + index + '" class="faf_filterSetting">&nbsp;</div>');  
        
        // loading stuff 
        jq('#faf_filter' + index).css('opacity','0.2');
               	 
		jq.post(ajax_object.ajax_url, data, function(response) {
 				jq('#faf_filterSetting' + index).html(response);
 						data['action'] = 'faf_help_text';
				jq.post(ajax_object.ajax_url, data, function(response) {
 					jq('#faf_filterSetting' + index).after(response);
					jq('#faf_loading'+ index).hide();
					jq('#faf_filter' + index).css('opacity','');
				});	
		});		
		


		// put out notification box - saving is needed 
		jq('.faf_notBox').css('display','block'); 
		//jq('.faf_notBox').text('Please remember to click the Save button after making changes!'); 
	});

	/* Doing this on 'outer' element #filters does not work for some reason, grbml*/
	jq(document.body).on("click",".delete_filter", function() {
		
		jq(this).closest('div').fadeOut('slow',
			function()
			{
				jq(this).closest('div').remove(); 
					// put out notification box - saving is needed 
				jq('.faf_notBox').css('display','block'); 
				//jq('.faf_notBox').text('Please remember to click the Save button after making changes!'); 
				reNumber();
			}		
		
	); 
	});
	
	// prevent help text hovers from falling off screen
	jq(document.body).on('hover','.faf_filterHelpBox',function(){
		var menuHeight = jq(this).offset().left;
        var windowWidth = jq(window).width();

        	if( (menuHeight + 300) > windowWidth)
        	{	
       				jq(this).children().css('left', '-' + Math.ceil( (menuHeight + 350) - windowWidth) + 'px');
       		}
       		
	});
	/** Renumber the internal form array when a filter is removed. **/
	function reNumber()
	{
		var filters = jq("#filters").children(); 
		var id; 

		jq("#filters").children().each(function (index)
		{
			id = parseInt(jq(this).attr('id').replace('faf_filter',''));

			if ( index != id) 
			{
			  jq(this).attr('id',jq(this).attr('id').replace('faf_filter' + id,'faf_filter' + index));
			  var obj = jq('#faf_filterSetting'+ id,this); 
			  obj.attr('id',obj.attr('id').replace('faf_filterSetting' + id,'faf_filterSetting' + index));

			  jq('input, select',this).each(function() {
			  	console.log(this.name);
			  	this.name = this.name.replace('[' + id + ']','[' + index + ']');  
			  	if (this.name == ('required' + id) ) // validation hidden fields 
				  	this.name = this.name.replace('required' + id,'required' + index );  
			  });

			  jq('.faf_bigFilterNr',this).text( (index +1) );
			}
		});
	}	



	/** Form validation **/
	jq(document.body).on("submit","form",function(e) {
	// form is tricky
		var goSubmit = true; // innocent until proven guilty
		var i = 0; 
		var req, jscall;
		
		while( (jq("#faf_filter" + i ).length > 0 ))
		{
			req = jq(":input[name='required" + i + "']").val();

			if (! (typeof req == 'undefined') )
			{
				reqs = req.split(","); 
				for(j=0; j < reqs.length;j++)
				{
					// check if thing is set 
					if (! jq(":input[name='" + reqs[j] + "[" + i + "]" + "']").length > 0
						 || jq(":input[name='" + reqs[j] + "[" + i + "]" +  "']").val() == "")
					{
						
						jq(":input[name='" + reqs[j] + "[" + i + "]" + "']").parent().css("backgroundColor","#FFEBE8"); 
						jq('.faf_errorBox').css('display','block'); 
						//jq('.faf_errorBox').text('Some fields are required. Please fill them out first!'); 
						goSubmit = false;
					}
			
				}
			}
			//jscall = jq("#filters :input[name='jscallback" + i "']");		
			
			//console.log(jscall);
			i++;
		}
		if(goSubmit) return true; 
		else 
		return false;
	});
});
