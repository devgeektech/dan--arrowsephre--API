<?php get_header(); ?>
	<div id="content-wrapper">
		<div id="content-main">
			<div id="home-main">
				<div id="post-area" <?php post_class(); ?>>
					<div id="woo-content">
						<?php woocommerce_content(); ?>
						<?php wp_link_pages(); ?>
					</div><!--woo-content-->
				</div><!--post-area-->
			</div><!--home-main-->
		</div><!--content-main-->
		<?php get_sidebar('woo'); ?>
	</div><!--content-wrapper-->
</div><!--main-wrapper-->
<?php get_footer(); ?>