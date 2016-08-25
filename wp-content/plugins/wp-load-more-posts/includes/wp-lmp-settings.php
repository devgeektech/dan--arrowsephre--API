<?php
/**
 * WP Load More Posts Settings
 */
class WP_LMP_Admin {

	/**
 	 * Option key, and option page slug
 	 * @var string
 	 */
	private $key = 'wp_lmp_options';

	/**
 	 * Options page metabox id
 	 * @var string
 	 */
	private $metabox_id = 'wp_lmp_option_metabox';

	/**
	 * Options Page title
	 * @var string
	 */
	protected $title = '';

	/**
	 * Options Page hook
	 * @var string
	 */
	protected $options_page = '';

	/**
	 * Constructor
	 * @since 0.1.0
	 */
	public function __construct() {
		// Set our title
		$this->title = __( 'WP Load More Posts Settings', 'wp_lmp' );
	}

	/**
	 * Initiate our hooks
	 * @since 0.1.0
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action( 'cmb2_init', array( $this, 'add_options_page_metabox' ) );
	}


	/**
	 * Register our setting to WP
	 * @since  0.1.0
	 */
	public function init() {
		register_setting( $this->key, $this->key );
	}

	/**
	 * Add menu options page
	 * @since 0.1.0
	 */
	public function add_options_page() {
		//$this->options_page = add_menu_page( $this->title, $this->title, 'manage_options', $this->key, array( $this, 'admin_page_display' ) );
		$this->options_page = add_submenu_page( 'options-general.php', $this->title, $this->title, 'manage_options', $this->key, array( $this, 'admin_page_display' ) );
	}

	/**
	 * Admin page markup. Mostly handled by CMB2
	 * @since  0.1.0
	 */
	public function admin_page_display() {
		?>
		<div class="wrap cmb2-options-page <?php echo $this->key; ?>">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<?php cmb2_metabox_form( $this->metabox_id, $this->key ); ?>
		</div>
		<?php
	}

	/**
	 * Add the options metabox to the array of metaboxes
	 * @since  0.1.0
	 */
	function add_options_page_metabox() {

		$cmb = new_cmb2_box( array(
			'id'      => $this->metabox_id,
			'hookup'  => false,
			'show_on' => array(
				// These are important, don't remove
				'key'   => 'options-page',
				'value' => array( $this->key, )
			),
		) );

		// Set our CMB2 fields

		$cmb->add_field( array(
			'name' => __( 'Content Selector', 'wp_lmp' ),
			'desc' => __( 'The Content Selector is the ID or class (CSS selector) of the div or html element that contains the posts section in your WordPress theme.  Common examples include #main or #content.  Basically the id of the container for your posts.', 'wp_lmp' ),
			'id'   => 'content_selector',
			'type' => 'text_medium',
			'default' => '#main',
			'attributes' => array(
				'placeholder' => '#main',
				'required' => 'required',
			),
		) );
		
		$cmb->add_field( array(
			'name' => __( 'Post Class Selector', 'wp_lmp' ),
			'desc' => __( 'Where the Content Selector was the div surrounding all of the posts, the Post Class selector is the div that surrounds a single post.  Common examples include .post or .blog-post', 'wp_lmp' ),
			'id'   => 'post_class_selector',
			'type' => 'text_medium',
			'default' => '.post',
			'attributes' => array(
				'placeholder' => '.post',
				'required' => 'required',
			),
		) );

		$cmb->add_field( array(
			'name'    => __( 'Pager Navigation Selector', 'wp_lmp' ),
			'desc'    => __( 'Since we want to replace the default pager (i.e. << Newer Posts 2 3 4 Older Posts >>) we need to target that html element with its CSS ID or Class selector so we can replace it with our new Load More Posts button.', 'wp_lmp' ),
			'id'      => 'pager_selector',
			'type'    => 'text_medium',
			'default' => '.paging-navigation',
			'attributes' => array(
				'placeholder' => '.paging-navigation',
				'required' => 'required',
			),
		) );
		
		$cmb->add_field( array(
			'name'    => __( 'Load More Posts button class', 'wp_lmp' ),
			'desc'    => __( 'Here you can assign a CSS class to the Load More Posts button to allow for easy styling of the button in your CSS.', 'wp_lmp' ),
			'id'      => 'btn_class',
			'type'    => 'text_medium',
			'attributes' => array(
				'placeholder' => 'ex.) btn-default',
			),
		) );
		
		$cmb->add_field( array(
			'name'    => __( 'Load More Posts display text', 'wp_lmp' ),
			'desc'    => __( 'Here you can alter the button text displayed when a user want to see more posts.  The default is "Load More Posts", but you can change it to whatever you like, for example "Keep Reading" or "Older Posts".', 'wp_lmp' ),
			'id'      => 'load_more_text',
			'type'    => 'text_medium',
			'default' => 'Load More Posts',
		) );
		
		$cmb->add_field( array(
			'name'    => __( 'Posts Loading text', 'wp_lmp' ),
			'desc'    => __( 'Here you can enter a short loading message displayed in the button while the posts load after a user has clicked the Load More Posts button.  The default is "Loading..." but feel free to change it to whatever you like.', 'wp_lmp' ),
			'id'      => 'loading_text',
			'type'    => 'text_medium',
			'default' => 'Loading...',
		) );
		
		$cmb->add_field( array(
			'name'    => __( 'No More Posts text', 'wp_lmp' ),
			'desc'    => __( 'Here you can enter a short message telling the user that there are all your posts have been displayed and there are no more posts left to load.  The default text is "No Posts Left!', 'wp_lmp' ),
			'id'      => 'no_posts_text',
			'type'    => 'text_medium',
			'default' => 'No Posts Left!',
		) );

	}

	/**
	 * Public getter method for retrieving protected/private variables
	 * @since  0.1.0
	 * @param  string  $field Field to retrieve
	 * @return mixed          Field value or exception is thrown
	 */
	public function __get( $field ) {
		// Allowed fields to retrieve
		if ( in_array( $field, array( 'key', 'metabox_id', 'title', 'options_page' ), true ) ) {
			return $this->{$field};
		}

		throw new Exception( 'Invalid property: ' . $field );
	}

}

/**
 * Helper function to get/return the WP_LMP_Admin object
 * @since  0.1.0
 * @return WP_LMP_Admin object
 */
function wp_lmp_admin() {
	static $object = null;
	if ( is_null( $object ) ) {
		$object = new WP_LMP_Admin();
		$object->hooks();
	}

	return $object;
}

/**
 * Wrapper function around cmb2_get_option
 * @since  0.1.0
 * @param  string  $key Options array key
 * @return mixed        Option value
 */
function wp_lmp_get_option( $key = '' ) {
	return cmb2_get_option( wp_lmp_admin()->key, $key );
}

// Get it started
wp_lmp_admin();
