<?php

namespace Lipe\Lib\CMB2;

/**
 * Options_Page
 *
 * @author  Mat Lipe
 *
 * @package Lipe\Lib\CMB2
 */
class Options_Page extends Box {

	/**
	 * This parameter is for options-page metaboxes only and defaults to 'admin_menu',
	 * to register your options-page at the network level:
	 *
	 * @link    https://github.com/CMB2/CMB2/wiki/Box-Properties#admin_menu_hook
	 *
	 * @example 'network_admin_menu'
	 *
	 * @var string
	 */
	public $admin_menu_hook = 'admin_menu';

	/**
	 * This parameter is for options-page metaboxes only,
	 * and is sent along to add_menu_page()/add_submenu_page()
	 * to define the capability required to view the options page.
	 *
	 * @link    https://github.com/CMB2/CMB2/wiki/Box-Properties#capability
	 *
	 * @example 'edit_posts'
	 *
	 * @var string
	 */
	public $capability = 'manage_options';

	/**
	 * On settings pages (not options-general.php sub-pages), allows disabling
	 * of error displaying
	 *
	 * @notice  only exists in example-functions.php and not in docs. May require
	 *         more testing.
	 *
	 * @example = true
	 *
	 * @var bool
	 */
	public $disable_settings_errors;

	/**
	 * This parameter is for options-page metaboxes only
	 * and allows overriding the options page form output.
	 *
	 * @link    https://github.com/CMB2/CMB2/wiki/Box-Properties#display_cb
	 *
	 * @example 'my_callback_function_to_display_output'
	 *
	 * @var callable
	 */
	public $display_cb = false;

	/**
	 * This parameter is for options-page metaboxes only,
	 * and is sent along to add_menu_page() to define the menu icon.
	 * Only applicable if parent_slug is left empty.
	 *
	 * @link    https://github.com/CMB2/CMB2/wiki/Box-Properties#icon_url
	 *
	 * @example 'dashicons-chart-pie'
	 *
	 * @var string
	 */
	public $icon_url;

	/**
	 * This parameter is for options-page metaboxes only,
	 * and is sent along to add_menu_page()/add_submenu_page() to define the menu title.
	 *
	 * @link    https://github.com/CMB2/CMB2/wiki/Box-Properties#menu_title
	 *
	 * @example 'Site Options
	 *
	 * @var string
	 */
	public $menu_title;

	/**
	 * Allows specifying a custom callback for setting the
	 * error/success message on save.
	 *
	 * @link    https://github.com/CMB2/CMB2/wiki/Box-Properties#message_cb
	 *
	 * @example 'my_callback_function_to_display_messages( $cmb, $args )'
	 *
	 * @var callable
	 */
	public $message_cb;

	/**
	 * This parameter is for options-page metaboxes only,
	 * and is sent along to add_submenu_page() to define the parent-menu item slug.
	 *
	 * @link    https://github.com/CMB2/CMB2/wiki/Box-Properties#parent_slug
	 *
	 * @example 'tools.php'
	 *
	 *
	 * @var string
	 */
	public $parent_slug;

	/**
	 * This parameter is for options-page metaboxes only,
	 * and is sent along to add_menu_page() to define the menu position.
	 * Only applicable if parent_slug is left empty.
	 *
	 * @link    https://github.com/CMB2/CMB2/wiki/Box-Properties#position
	 *
	 * @example 6
	 *
	 * @var int
	 */
	public $position;

	/**
	 * This parameter is for options-page metaboxes only and
	 * defines the text for the options page save button. defaults to 'Save'.
	 *
	 * @link    https://github.com/CMB2/CMB2/wiki/Box-Properties#save_button
	 *
	 * @example 'Save Settings'
	 *
	 * @internal
	 * @see     Options_Page::save_button();
	 *
	 * @var string
	 */
	public $save_button;

	/**
	 * This parameter is for options-page metaboxes only and
	 * defines the settings page slug. Defaults to $id.
	 *
	 * @example 'my_options_page_slug'
	 *
	 * @var string
	 */
	public $option_key;

	/**
	 * Break the settings page into tabs by specifying more than one
	 * settings page to the same tab group.
	 *
	 * All settings pages set to tab group will display on the same settings
	 * page with tabs to navigate between them. The settings may be registered anywhere
	 * in the admin menu and will still combine when displayed.
	 *
	 * @example $settings->tab_group = 'first'
	 *          $another_settings->tab_group = 'first'
	 *          Both will display on the same settings page with different menus
	 *          to get there.
	 *
	 * @var
	 */
	public $tab_group;

	/**
	 * Holds default values specified on individual fields
	 * for use with the get filter.
	 * CMB2 saves options as a single blob so we use the
	 * filter to inject the default on retrieve.
	 *
	 * @var array
	 */
	protected $default_values = [];


	/**
	 * Options Page constructor.
	 *
	 * @param string $id
	 * @param string $title
	 */
	public function __construct( $id, $title ) {
		if ( null === $this->option_key ) {
			$this->option_key = $id;
		}

		add_action( "cmb2_init_hookup_{$id}", [ $this, 'run_options_hookup_on_front_end' ] );

		parent::__construct( $id, [ 'options-page' ], $title );
	}


	/**
	 * Is this a network level settings page?
	 *
	 * @param bool $is_network
	 *
	 * @since 2.4.0
	 *
	 * @return void
	 */
	public function network( bool $is_network = true ) : void {
		if ( $is_network ) {
			$this->admin_menu_hook = 'network_admin_menu';
		} else {
			$this->admin_menu_hook = 'admin_menu'; //default
		}
	}


	/**
	 * Specify the save button text.
	 * Set to `null` to create a read-only form which has no save button.
	 *
	 * @param null|string $text
	 *
	 * @since 1.18.0
	 *
	 * @return void
	 */
	public function save_button( ?string $text ) : void {
		$this->save_button = $text;
		if ( null === $text ) {
			add_action( "cmb2_before_options-page_form_{$this->id}", function () {
				// Hide so we never see it.
				// Remove so we can't submit the form via enter.
				?>
				<style>
					[id="<?= esc_js( $this->id ); ?>"] .submit {
						display: none;
					}
				</style>
				<script>
					jQuery( function( $ ) {
						$( '[id="<?= esc_js( $this->id ); ?>"] .submit' ).remove();
					} );
				</script>
				<?php
			} );
		}
	}


	/**
	 * The options hookups translate options to sitemeta for the network
	 * options, among other things.
	 *
	 * The CMB2 plugin only runs these hookups in the admin because
	 * it expects direct calls to `get_site_option`. We mimic the admin
	 * functionality here, so we get the correct values when using the Repo.
	 *
	 * @param \CMB2 $cmb
	 *
	 * @since 2.18.1
	 */
	public function run_options_hookup_on_front_end( \CMB2 $cmb ) : void {
		if ( ! is_admin() ) {
			$hookup = new \CMB2_Hookup( $cmb );
			$hookup->options_page_hooks();
		}
	}


	/**
	 * Option pages are stored in one big blob which means we
	 * must implement logic to separate the fields when registering.
	 *
	 * Gives a universal place for ammending the config.
	 *
	 * @param Field $field
	 * @param array $config
	 *
	 * @since 2.19.0
	 */
	public function register_meta_on_all_types( Field $field, array $config ) : void {
		unset( $config['single'] );

		if ( $field->show_in_rest ) {
			$config = $this->translate_rest_keys( $field, $config );
			add_filter( 'rest_pre_get_setting', function ( $pre, $option ) use ( $field, $config ) {
				return $option === $config['show_in_rest']['name'] ? \cmb2_options( $this->id )->get( $field->get_id(), $field->default ) : $pre;
			}, 9, 2 );
		}

		// Legacy, prior to lipemat/cmb2 2.7.0.7.
		if ( \defined( 'CMB2_VERSION' ) && null !== $field->default && version_compare( CMB2_VERSION ?? '9.9.9.9', '2.7.0.7', '<' ) ) {
			$this->default_values[ $field->get_id() ] = $field->default;
			$this->once( function() {
				add_filter( "cmb2_override_option_get_{$this->id}", [ $this, 'inject_defaults' ] );
			}, __METHOD__ );
		}

		// Nothing to register.
		if ( 2 > \count( $config ) ) {
			return;
		}

		register_setting( 'options', $field->get_id(), $config );
	}


	/**
	 * @deprecated 2.23.0
	 */
	public function inject_defaults() {
		_deprecated_function( 'cmb2_override_option_get_{$this->id}', '2.7.0.7', 'Version 2.7.0.7 of lipemat/cmb2 has more reliable filters.' );
		// Leaving the filter intact, creates infinite loops.
		remove_filter( "cmb2_override_option_get_{$this->id}", [ $this, 'inject_defaults' ] );
		// Get values either from network or regular options.
		$values = cmb2_options( $this->id )->get_options();

		foreach ( $this->default_values as $field => $default ) {
			if ( empty( $values[ $field ] ) ) {
				$values[ $field ] = $default;
			}
		}
		// Add filter back in for subsequent calls.
		add_filter( "cmb2_override_option_get_{$this->id}", [ $this, 'inject_defaults' ] );

		return $values;
	}
}
