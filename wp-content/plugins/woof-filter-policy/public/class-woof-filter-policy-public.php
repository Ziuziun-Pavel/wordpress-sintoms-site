<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Woof_Filter_Policy
 * @subpackage Woof_Filter_Policy/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Woof_Filter_Policy
 * @subpackage Woof_Filter_Policy/public
 * @author     Techin
 */
class Woof_Filter_Policy_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $woof_filter_policy    The ID of this plugin.
	 */
	private $woof_filter_policy;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $woof_filter_policy       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $woof_filter_policy, $version ) {

		$this->woof_filter_policy = $woof_filter_policy;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woof_Filter_Policy_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woof_Filter_Policy_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->woof_filter_policy, plugin_dir_url( __FILE__ ) . 'css/woof-filter-policy-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woof_Filter_Policy_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woof_Filter_Policy_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->woof_filter_policy, plugin_dir_url( __FILE__ ) . 'js/woof-filter-policy-public.js', array( 'jquery' ), $this->version, false );

	}

}
