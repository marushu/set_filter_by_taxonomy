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
 * Display a custom taxonomy dropdown in admin
 * @author Mike Hemberger
 * @link http://thestizmedia.com/custom-post-type-filter-admin-custom-taxonomy/
 */
add_action( 'restrict_manage_posts', 'renove_set_filter_post_type_taxonomy' );
function renove_set_filter_post_type_taxonomy() {

	global $typenow;
	$taxonomies = get_object_taxonomies( $typenow );
	$taxonomies = implode( ',', $taxonomies );

	$post_type = $typenow;
	$taxonomy  = $taxonomies;
	if ($typenow == $post_type) {
		$selected      = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
		$info_taxonomy = get_taxonomy($taxonomy);
		wp_dropdown_categories(array(
			'show_option_all' => __("全ての {$info_taxonomy->label}"),
			'taxonomy'        => $taxonomy,
			'name'            => $taxonomy,
			'orderby'         => 'name',
			'selected'        => $selected,
			'show_count'      => true,
			'hide_empty'      => true,
		));
	};
}

/**
 * Filter posts by taxonomy in admin
 * @author  Mike Hemberger
 * @link http://thestizmedia.com/custom-post-type-filter-admin-custom-taxonomy/
 */
add_filter( 'parse_query', 'tsm_convert_id_to_term_in_query' );
function tsm_convert_id_to_term_in_query( $query ) {

	global $pagenow, $typenow;

	$taxonomies = get_object_taxonomies( $typenow );
	$taxonomies = implode( ',', $taxonomies );
	$post_type = $typenow;
	$taxonomy  = $taxonomies;
	$q_vars    = $query->query_vars;

	if ( $pagenow == 'edit.php'
	     && isset($q_vars['post_type'])
	     && $q_vars['post_type'] == $post_type
	     && isset($q_vars[$taxonomy])
	     && is_numeric($q_vars[$taxonomy])
	     && $q_vars[$taxonomy] != 0
	) {

		$term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
		$q_vars[$taxonomy] = $term->slug;

	}
}
