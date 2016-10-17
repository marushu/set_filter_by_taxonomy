<?php
/**
 * Plugin Name:     Set Filter By Tax
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     PLUGIN DESCRIPTION HERE
 * Author:          YOUR NAME HERE
 * Author URI:      YOUR SITE HERE
 * Text Domain:     set-filter-by-tax
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Set_Filter_By_Tax
 */

/**
 * Add admin filter
 */
/**
 * Display a custom taxonomy dropdown in admin
 * @author Mike Hemberger
 * @link http://thestizmedia.com/custom-post-type-filter-admin-custom-taxonomy/
 */
add_action( 'restrict_manage_posts', 'renove_filter_post_type_by_taxonomy' );

function renove_filter_post_type_by_taxonomy() {
	global $typenow;

	$args       = array(
		'public'   => true,
		'_builtin' => true,
	);
	$custom_post_types = get_post_types( $args );
	$custom_post_types = array_values( $custom_post_types );

	if( empty( $typenow ) || in_array( $typenow, $custom_post_types ) === true )
		return;

	$taxonomies = get_object_taxonomies( $typenow );
	$post_type = get_post_type();

	if ( $typenow == $post_type ) {

		foreach ( $taxonomies as $tax ) {

			if ( ! empty( $tax ) && ! is_wp_error( $tax ) ) {

				$selected = isset( $_GET[ $tax ] ) ? $_GET[ $tax ] : '';
				$info_taxonomy = get_taxonomy( $tax );

				wp_dropdown_categories( array(
					'show_option_all' => __( "Show All {$info_taxonomy->label}" ),
					'taxonomy'        => $tax,
					'name'            => $tax,
					'orderby'         => 'name',
					'selected'        => $selected,
					'show_count'      => true,
					'hide_empty'      => true,
				) );

			}

		}
	}
}

/**
 * Filter posts by taxonomy in admin
 * @author  Mike Hemberger
 * @link    http://thestizmedia.com/custom-post-type-filter-admin-custom-taxonomy/
 */
add_filter( 'parse_query', 'renove_convert_id_to_term_in_query' );
function renove_convert_id_to_term_in_query( $query ) {
	global $pagenow, $typenow;
	$taxonomies = get_object_taxonomies( $typenow );
	$post_type = $query->query['post_type'];

	if ( ! empty( $taxonomies ) && ! is_wp_error( $taxonomies ) ) {

		foreach ( $taxonomies as $tax ) {

			if ( ! empty( $tax ) ) {

				$q_vars = &$query->query_vars;
				if ( $pagenow == 'edit.php'
				     && isset( $q_vars[ 'post_type' ] )
				     && $q_vars[ 'post_type' ] == $post_type
				     && isset( $q_vars[ $tax ] )
				     && is_numeric( $q_vars[ $tax ] )
				     && $q_vars[ $tax ] != 0
				) {

					$term           = get_term_by( 'id', $q_vars[ $tax ], $tax );
					$q_vars[ $tax ] = $term->slug;

				}

			}

		}

	}
}
