jQuery(document).ready(function($) {

	// The number of the next page to load (/page/x/).
	var pageNum = parseInt(wpLoadMorePosts.startPage) + 1;
	// The maximum number of pages the current query can return.
	var max = parseInt(wpLoadMorePosts.maxPages);
	// The link of the next page of posts.
	var nextLink = wpLoadMorePosts.nextLink;
	
	// The content selector
	var contentSelector = wpLoadMorePosts.contentSelector;
	// The post class selector
	var postClassSelector = wpLoadMorePosts.postClassSelector;
	// The post navigation selector
	var pagerSelector = wpLoadMorePosts.pagerSelector;
	// Class to assign to the load more button
	var btnClass = wpLoadMorePosts.btnClass;
	// Text to display for load more posts button
	var loadMoreText = wpLoadMorePosts.loadMoreText;
	// Text to display while posts are loading
	var loadingText = wpLoadMorePosts.loadingText;
	// Text to display when there are no more posts left to load
	var noPostsText = wpLoadMorePosts.noPostsText;
	
	/**
	 * Replace the traditional navigation with the load more posts button,
	 * but only if there is at least one page of new posts to load.
	 */
	if(pageNum <= max) {
		// Insert the "More Posts" link.
		$(contentSelector)
			.append('<div class="wp-load-more-posts-page-'+ pageNum +'"></div>')
			.append('<button id="wp-load-more-btn" class="'+ btnClass +'">'+ loadMoreText +'</button>');
			
		// Remove the traditional navigation.
		$(pagerSelector).remove();
	}
	
	
	/**
	 * Load new posts when the button is clicked.
	 */
	$('#wp-load-more-btn').click(function() {
	
		// Are there more posts to load?
		if(pageNum <= max) {
		
			// Show that we're working.
			$(this).text(loadingText);
			
			$('.wp-load-more-posts-page-'+ pageNum).load(nextLink + ' ' + postClassSelector,
				function() {
					// Update page number and nextLink.
					pageNum++;
					nextLink = nextLink.replace(/\/page\/[0-9]*/, '/page/'+ pageNum);
					
					// Add a new placeholder, for when user clicks again.
					$('#wp-load-more-btn')
						.before('<div class="wp-load-more-posts-page-'+ pageNum +'"></div>')
					
					// Update the button message.
					if(pageNum <= max) {
						$('#wp-load-more-btn').text(loadMoreText);
					} else {
						$('#wp-load-more-btn').text(noPostsText);
					}
				}
			);
		} else {
			$('#wp-load-more-btn').fadeOut();
		}	
		
		return false;
	});
});