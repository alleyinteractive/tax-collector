<?php
/**
 * This file contains the singleton class for Fields.
 *
 * @package Tax_Collector
 */

namespace Tax_Collector;

/**
 * Fields
 */
class Fields {

	/**
	 * Handle all term relationships using a cleaner interface.
	 */
	public static function meta_box( $post_type ) {
		if ( empty( $post_type ) ) {
			$post_type = 'post';
			if ( ! empty( $GLOBALS['post_type'] ) ) {
				$post_type = $GLOBALS['post_type'];
			} elseif ( ! empty( $_POST['post_type'] ) ) {
				$post_type = sanitize_text_field( $_POST['post_type'] );
			} elseif ( ! empty( $_GET['post_type'] ) ) {
				$post_type = sanitize_text_field( $_GET['post_type'] );
			} elseif ( ! empty( $_GET['post'] ) ) {
				$post_type = get_post_type( intval( $_GET['post'] ) );
			} elseif ( ! empty( $_POST['fm_subcontext'] ) ) {
				$post_type = sanitize_text_field( $_POST['fm_subcontext'] );
			}
		}

		$tabs = array();

		// Specify all taxonomies we're listing in order to control the order
		$taxonomies = Main::instance()->get_taxonomies();

		foreach ( $taxonomies as $tax ) {
			$tax_obj = get_taxonomy( $tax );
			if ( ! in_array( $post_type, $tax_obj->object_type ) ) {
				continue;
			}

			// All other tax selections utilize the FM autocomplete field
			$tabs[ $tax ] = new \Fieldmanager_Group( [
				'label' => $tax_obj->labels->name,
				'children' => [
					'terms' => new \Fieldmanager_Select( [
						'label' => $tax_obj->labels->name,
						'one_label_per_item' => false,
						'label_element' => 'h3',
						'limit' => 0,
						'extra_elements' => 0,
						'sortable' => true,
						'add_more_label' => sprintf( _x( 'Add %s', 'add taxonomy term button', 'tax-collector' ), $tax_obj->labels->singular_name ),
						'remove_default_meta_boxes' => true,
						'datasource' => new \Fieldmanager_Datasource_Term( [
							'use_ajax' => true,
							'taxonomy' => [ $tax ],
							'only_save_to_taxonomy' => true,
							'taxonomy_hierarchical' => $tax_obj->hierarchical,
						] ),
					] ),
				],
			] );
		}

		/**
		 * Filter the relationship tabs prior to output.
		 *
		 * @param array $tabs The list of relationship tabs. Note that since this is
		 *                    tabbed, there's an extra group layer.
		 * @param string $post_type The current post type.
		 */
		$tabs = apply_filters( 'tax_collector_fm_taxonomies_tabs', $tabs, $post_type );

		if ( ! empty( $tabs ) ) {
			$fm = new \Fieldmanager_Group( [
				'name' => 'taxonomies',
				'tabbed' => 'vertical',
				'serialize_data' => false,
				'add_to_prefix' => false,
				'children' => $tabs,
			] );
			$fm->add_meta_box( __( 'Taxonomies', 'tax-collector' ), $post_type );
		}
	}
}
