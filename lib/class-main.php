<?php
/**
 * This file contains the main controller class for the plugin.
 *
 * @package Tax_Collector
 */

namespace Tax_Collector;

/**
 * Tax Collector
 */
class Main {
	use Singleton;

	/**
	 * The slug to reference the top-level menu item.
	 *
	 * @var string
	 */
	protected $slug = 'taxonomies';

	/**
	 * Taxonomies to collect.
	 *
	 * @var array
	 */
	protected $taxonomies = [];

	/**
	 * Setup the singleton.
	 */
	public function setup() {
		/**
		 * Filter the taxonomies to collect.
		 *
		 * @param array $taxonomies Taxonomy slugs, e.g. [ 'post_tag' ].
		 */
		$this->taxonomies = apply_filters( 'tax_collector_taxonomies', [] );

		if ( ! empty( $this->taxonomies ) ) {
			add_action( 'init', [ $this, 'remove_add_to_menus' ] );
			add_action( 'admin_menu', [ $this, 'add_menu_page' ], 8 );
			add_action( 'admin_menu', [ $this, 'add_taxonomies_to_menu' ] );
			add_action( 'admin_menu', [ $this, 'admin_submenus' ], 20 );
			add_action( 'admin_head', [ $this, 'activate_parent_menu' ] );
		}
	}

	/**
	 * Get the taxonomies that are being collected.
	 *
	 * @return array
	 */
	public function get_taxonomies() {
		return $this->taxonomies;
	}

	/**
	 * Set 'show_in_menu' to false for all the collected taxonomies.
	 */
	public function remove_add_to_menus() {
		global $wp_taxonomies;

		foreach ( $this->taxonomies as $taxonomy ) {
			if ( ! empty( $wp_taxonomies[ $taxonomy ] ) ) {
				$wp_taxonomies[ $taxonomy ]->show_in_menu = false;
			}
		}
	}

	/**
	 * Add top-level menu item.
	 */
	public function add_menu_page() {
		$page_title = apply_filters( 'tax_collector_submenu_page_title', __( 'Taxonomies', 'tax-collector' ) );
		add_menu_page(
			$page_title,
			$page_title,
			'manage_categories',
			'taxonomies',
			'__return_false',
			'dashicons-tag',
			'11.1'
		);
	}

	/**
	 * Add taxonomies to the menu.
	 */
	public function add_taxonomies_to_menu() {
		foreach ( $this->taxonomies as $taxonomy ) {
			$tax_obj = get_taxonomy( $taxonomy );
			if ( $tax_obj ) {
				add_submenu_page( $this->slug, $tax_obj->labels->all_items, $tax_obj->labels->menu_name, $tax_obj->cap->manage_terms, apply_filters( 'tax_collector_menu_link', 'edit-tags.php?taxonomy=' . $taxonomy, $taxonomy ) );
			}
		}
	}

	/**
	 * The top-level menu items are by themselves useless, so we have to remove
	 * the blank links.
	 *
	 * @see Main::admin_menu()
	 */
	public function admin_submenus() {
		global $submenu;

		if ( isset( $submenu[ $this->slug ] ) ) {
			array_shift( $submenu[ $this->slug ] );
		}
	}

	/**
	 * Highlight the parent menu if one of the submenu items is active.
	 */
	public function activate_parent_menu() {
		global $parent_file, $submenu_file, $taxonomy;

		if ( $taxonomy && in_array( $taxonomy, $this->taxonomies ) ) {
			$submenu_file = 'edit-tags.php?taxonomy=' . $taxonomy;
			$parent_file = $this->slug;
		}
	}
}
