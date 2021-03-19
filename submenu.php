add_filter( 'wp_nav_menu_objects', 'wpg2byt_add_term_parent' );
function wpg2byt_add_term_parent( $items ) {
    $terms = get_terms( array(
      'taxonomy' => 'product_cat'
    ));
	
	if(wp_is_mobile()) {
		foreach ($terms as $term) {
        	//format its data to be compatible with the filter
        	$link = array (
            	'title'            => $term->name,
            	'menu_item_parent' => 152,
            	'ID'               => $term->term_id,
            	'db_id'            => '',
            	'url'              => get_term_link($term)
        	);
        	$items[] = (object) $link;
    	}
	}
	else {
		foreach ($terms as $term) {
        	//format its data to be compatible with the filter
        	$link = array (
           		'title'            => $term->name,
            	'menu_item_parent' => 84,
            	'ID'               => $term->term_id,
            	'db_id'            => '',
            	'url'              => get_term_link($term)
        	);
        	$items[] = (object) $link;
    	}
	}
    
    return $items;
}