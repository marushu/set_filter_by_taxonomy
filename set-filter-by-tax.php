<?php
/**
 * Plugin Name:     Set fileter by tax.
 * Plugin URI:      http://
 * Description:     Set multiple tax filter at custom post type admin panel.
 * Author:          maruhsu
 * Author URI:      https://private.hibou-web.com/
 * Text Domain:     set-filter-by-tax
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Set_Filter_By_Tax
 */

/**
 * Set multiple filter ad custom post type admin panel.
 */
function multiple_filter_at_cpt() {

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
add_action( 'restrict_manage_posts', 'multiple_filter_at_cpt' );

/**
 * Set multiple filter at custom post type. And fire.
 *
 * @param $query
 */
function multiple_filter_taxonomy_term( $query ) {

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
add_filter( 'parse_query', 'multiple_filter_taxonomy_term' );
