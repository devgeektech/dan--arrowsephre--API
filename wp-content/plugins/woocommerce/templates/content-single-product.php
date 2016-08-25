<style>
.main-content{float:left; width:100%;}
.x-sm.x-1-6 {
    float: left;
    width: 60%;
    margin-top: 2.2%;
}
.single-product .arrow-wrap{position: absolute!important;
    top: 7%;
    right: 0;}
.x-column.x-1-3 {
    width: 30.66666%;
}
.feature, .feature h2{    margin-top: 0!important;
    padding: 0;}
.right{float:right;}
.x-sm.x-1-6 .summary{width:100%!important; float:left!important;}
.single-product .x-container.offset{margin:0 auto;}
.single-product .entry-wrap{padding:10px; box-shadow:none;}
.single-product .woocommerce-tabs{margin-top:0!important; width:60%!important;}
.single-product ul.tabs.x-nav.x-nav-tabs.three-up.top {
    display: none;
}
.single-product .x-tab-content {
    border: none;
}
.single-product .x-tab-content{box-shadow:none;}
.single-product .x-tab-content .x-tab-pane{padding:0}
.single-product .product_meta {
    display:none;
}
.single-product .x-tab-content .panel h2 {
    display:none;
}
.single-product .saboxplugin-wrap{ display:none;}
.single-product .related{ display:none;}
.x-btn, .button, [type="submit"] {
    color: #ffffff;
    border-color: #086d8e;
    background-color: #127ea9;
    margin-bottom: 0.25em;
    text-shadow: 0 0.075em 0.075em rgba(0,0,0,0.5);
    box-shadow: 0 0.25em 0 0 #086d8e,0 4px 9px rgba(0,0,0,0.75);
    border-radius: 0.25em;
    padding: 7px 7px;
    margin-left: 10px;
	    font-size: 13px;
}
.logies {
    float: left;
    width: 100%;
    margin-bottom: 30px;
}
</style>
<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * Override this template by copying it to yourtheme/woocommerce/content-single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<?php
	/**
	 * woocommerce_before_single_product hook
	 *
	 * @hooked wc_print_notices - 10
	 */
	 do_action( 'woocommerce_before_single_product' );

	 if ( post_password_required() ) {
	 	echo get_the_password_form();
	 	return;
	 }
?>

<div itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>" id="product-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php
		/**
		 * woocommerce_before_single_product_summary hook
		 *
		 * @hooked woocommerce_show_product_sale_flash - 10
		 * @hooked woocommerce_show_product_images - 20
		 */
		//do_action( 'woocommerce_before_single_product_summary' );
	?>
<div class="x-main full">
			  <div class="x-sm x-1-6">
				<header class="x-main full">
				<span style="color: #808080;"><strong>McAfee</strong></span>
				 <div class="summary entry-summary">

		
				<h1 itemprop="name" class="product_title entry-title"><?php the_title(); ?></h1> 
				 
		<?php
		
			/**
			 * woocommerce_single_product_summary hook
			 *
			 * @hooked woocommerce_template_single_title - 5
			 * @hooked woocommerce_template_single_rating - 10
			 * @hooked woocommerce_template_single_price - 10
			 * @hooked woocommerce_template_single_excerpt - 20
			 * @hooked woocommerce_template_single_add_to_cart - 30
			 * @hooked woocommerce_template_single_meta - 40
			 * @hooked woocommerce_template_single_sharing - 50
			 */
			//do_action( 'woocommerce_single_product_summary' );
		?>

	</div><!-- .summary --> 
				 
				</header>
	
			  </div>
			
	
				
	<?php
		/**
		 * woocommerce_after_single_product_summary hook
		 *
		 * @hooked woocommerce_output_product_data_tabs - 10
		 * @hooked woocommerce_upsell_display - 15
		 * @hooked woocommerce_output_related_products - 20
		 */
		do_action( 'woocommerce_after_single_product_summary' );
	?>

	<meta itemprop="url" content="<?php the_permalink(); ?>" />

</div><!-- #product-<?php the_ID(); ?> -->


<div class="x-sm x-1-6 feature">

<?php 
//$valef=get_field( "feature" );
//if($valef){?>
	<h2 class="service-summary-heading">Features</h2>
	<?php// echo $valef;
//}
//else{
	
	
//}
?>
		<!--<ul class="service-summary-features-and-benefits">

  	<li>Top-rated</li>
    <li>Layered endpoint defense</li>
	<li>Top-rated</li>  
	<li>Top-rated</li>from chip to cloud - Advanced antivirus, anti-malware, host intrusion prevention,
	<li>from chip to cloud - Advanced antivirus</li>

</ul>--->
</div>

<div class="right x-column x-sm x-1-3 arrow-wrap">
    <div id="meta">
	<div class="logies"><div class="logospider"><?php the_post_thumbnail();?></div></div>
  <h2 class="visuallyhidden">Pricing</h2>
 
  <p class="price"><?php
  global $product;
  ?>
  <div itemprop="offers" itemscope itemtype="http://schema.org/Offer">

	<p class="price"><?php echo $product->get_price_html(); ?></p>

	<meta itemprop="price" content="<?php echo esc_attr( $product->get_price() ); ?>" />
	<meta itemprop="priceCurrency" content="<?php echo esc_attr( get_woocommerce_currency() ); ?>" />
	<link itemprop="availability" href="http://schema.org/<?php echo $product->is_in_stock() ? 'InStock' : 'OutOfStock'; ?>" />

</div>
<?php
	// Availability
	$availability      = $product->get_availability();
	$availability_html = empty( $availability['availability'] ) ? '' : '<p class="stock ' . esc_attr( $availability['class'] ) . '">' . esc_html( $availability['availability'] ) . '</p>';

	echo apply_filters( 'woocommerce_stock_html', $availability_html, $availability['availability'], $product );
?>

<?php if ( $product->is_in_stock() ) : ?>

	<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

	<form class="cart" method="post" enctype='multipart/form-data'>
	 	<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

	 	<?php
	 		if ( ! $product->is_sold_individually() ) {
	 			woocommerce_quantity_input( array(
	 				'min_value'   => apply_filters( 'woocommerce_quantity_input_min', 1, $product ),
	 				'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product ),
	 				'input_value' => ( isset( $_POST['quantity'] ) ? wc_stock_amount( $_POST['quantity'] ) : 1 )
	 			) );
	 		}
	 	?>

	 	<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->id ); ?>" />

	 	<button type="submit" class="single_add_to_cart_button button alt"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>

		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
	</form>

	<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

<?php endif; ?>

  </p>
  <ul class="price-caveats">
  

  
    <li>Minimum contract period: Month</li>
  
    <li>Trial option available</li>
  
  </ul>
 
<?php 
//$valefc=get_field( "contact" );
//if($valefc){?>
	<h2 class="service-summary-heading">Contact</h2>
	<?php //echo //$valefc;
//}
//else{
	
	
//}
?>
 </div>
  </div>


  <div class="main-content"> <?php 
//$valefsp=get_field( "support" );
//if($valefsp){?>
  <span class="summary-item-heading" style="font-size: 27px;color:#000;">
    Support service type
  </span>
  <span>


	<?php //echo $valefsp;
//}
//else{
	
	
//}
?>
   </span><?php 
//$valefclo=get_field( "cloud" );
//if($valefclo){?>

<h2 class="summary-item-heading">
    
    Cloud features
</h2>
<?php //echo $valefclo;
//}?>
<?php 
//$valefclobro=get_field( "supported_devices" );
//if($valefclobro){?>
   
    <thead class="summary-item-field-headings">
      <tr>
        
          <th scope="col" class="summary-item-field-heading-first">
            
              <span class="visuallyhidden">Name</span>
            
          </th>
        
          <th scope="col" class="summary-item-field-heading">
            
              <span class="visuallyhidden">Content</span>
            
          </th>
        
      </tr>
    </thead>
    <tbody>
      
    
      
    <tr class="summary-item-row">
    
      <td class="summary-item-field-first">
    <span>
    Elastic cloud approach supported
  </span>
  </td>
      
    <td class="summary-item-field">
    <span>
    Yes
  </span>
  </td>
  
    
  </tr>
  
    
      
    <tr class="summary-item-row">
    
      <td class="summary-item-field-first">
    <span>
    Guaranteed resources defined
  </span>
  </td>
      
    <td class="summary-item-field">
    <span>
    Yes
  </span>
  </td>
  
    
  </tr>
  
    
      
    <tr class="summary-item-row">
    
      <td class="summary-item-field-first">
    <span>
    Persistent storage supported
  </span>
  </td>
      
    <td class="summary-item-field">
    <span>
    Yes
  </span>
  </td>
  
    
  </tr>
  
		
  
    </tbody>
  </table>
<?php //echo $valefclobro;
//}?>
 
</div>
<?php do_action( 'woocommerce_after_single_product' ); ?>