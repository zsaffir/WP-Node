<?php

class WP_Node {
	private $taxonomy;
	private $post_type;

	public function __construct($taxonomy, $post_type = null){
		$this->taxonomy = $taxonomy;
		$this->post_type = (!empty($post_type)) ? $post_type : $this->taxonomy;
		$this->actions();
	}

	/**
	 *
	 */
	public function actions(){
		add_action("created_$this->taxonomy", array($this, 'insert_post'));
		add_action('init', array($this, 'register_post_type'), 11);
	}

	public function insert_post($term_id, $tt_id = null){
		$taxonomy = get_taxonomy($tt_id);
		$term = get_term($term_id, $this->taxonomy);

		$post_id = wp_insert_post(array(
			'post_status' 	=> 'publish',
			'post_type' 	=> $this->post_type,
			'post_name'		=> $term->slug,
			'post_title' 	=> ucfirst($this->taxonomy).': '.$term->name,
			'tax_input'		=> array( $this->post_type => array($term_id))


		));
		return get_post($post_id);
	}


	/**
	 * 
	 */
	public function register_post_type(){

		$labels = apply_filters("wp_node_post_type_{$this->taxonomy}_labels", array(
			'name' 					=> _x(ucfirst($this->post_type) .'s', 'post type general name'),
			'singular_name' 		=> _x(ucfirst($this->post_type), 'post type singular name'),
			'add_new' 				=> _x('Add New', $this->post_type),
			'add_new_item' 			=> __('Add New ' . ucfirst($this->post_type)),
			'edit_item' 			=> __('Edit ' . ucfirst($this->post_type)),
			'new_item' 				=> __('New ' . ucfirst($this->post_type)),
			'all_items' 			=> __('All ' . ucfirst($this->post_type) . 's'),
			'view_item' 			=> __('View ' . ucfirst($this->post_type) . 's'),
			'search_items' 			=> __('Search ' .ucfirst($this->post_type) .'s'),
			'not_found' 			=> __("No {$this->post_type}s found"),
			'not_found_in_trash' 	=> __("No {$this->post_type}s found in Trash"), 
			'parent_item_colon' 	=> '',
			'menu_name' 			=> __(ucfirst($this->post_type))
		));


		$args = apply_filters("wp_node_post_type_{$this->taxonomy}_args", array(
			'label'					=> $this->post_type,
			'labels'				=> $labels,
			'public' 				=> true,
			'publicly_queryable' 	=> true,
			'show_ui' 				=> true, 
			'show_in_menu' 			=> true, 
			'query_var' 			=> false,
			'rewrite' 				=> false,
			'capability_type' 		=> 'post',
			'has_archive' 			=> true, 
			'hierarchical' 			=> false,
			'menu_position' 		=> null,
			'supports' 				=> array( 'title', 'custom-fields' )
		)); 

		register_post_type($this->post_type, $args);
	}
}


