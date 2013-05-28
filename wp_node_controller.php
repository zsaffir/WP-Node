<?php

class WP_Node_Controller {
	private $taxonomy;
	private $post_type;

	/**
	 * @author Eddie Moya
	 */
	public function __construct($taxonomy, $post_type = null){
		$this->taxonomy = $taxonomy;
		$this->post_type = (is_null($post_type)) ? $taxonomy : $post_type;
		$this->actions();
	}

	/**
	 * @author Eddie Moya
	 */
	public function actions(){

		add_action("created_$this->taxonomy", array($this, 'create_node'));
		add_action('init', array($this, 'register_post_type'), 11);
	}
	
	public function create_node($term_id, $tt_id = null){
		$node = new WP_Node($term_id, $this->taxonomy, 'id', $this->post_type);
		$node->register_term_meta();
		
	}


	/**
	 * 
	 */
	public function register_post_type(){

		$labels = apply_filters("wp_node_post_type_{$this->taxonomy}_labels", array(
			'name' 					=> _x(ucfirst($taxonomy->name) .'s', 'post type general name'),
			'singular_name' 		=> _x(ucfirst($taxonomy->singular_name), 'post type singular name'),
			'add_new' 				=> _x('Add New', ucfirst($taxonomy->add_new_item)),
			'add_new_item' 			=> __('Add New ' . ucfirst($taxonomy->add_new_item)),
			'edit_item' 			=> __('Edit ' . ucfirst($taxonomy->edit_item)),
			'new_item' 				=> __('New ' . ucfirst($taxonomy->new_item_name)),
			'all_items' 			=> __('All ' . ucfirst($taxonomy->name) . 's'),
			'view_item' 			=> __('View ' . ucfirst($taxonomy->view_item) . 's'),
			'search_items' 			=> __('Search ' .ucfirst($taxonomy->search_items) .'s'),
			'not_found' 			=> __("No {$this->post_type}s found"),
			'not_found_in_trash' 	=> __("No {$this->post_type}s found in Trash"), 
			'parent_item_colon' 	=> '',
			'menu_name' 			=> __(ucfirst($taxonomy->menu_name))
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
			'supports' 				=> array( 'title', 'custom-fields',  ),
			'taxonomies'			=> array( $this->taxonomy )
		)); 

		register_post_type($this->post_type, $args);
	}

	public function add_post_type_support($support){
		add_post_type_support($this->post_type, $support);
	}

	public function remove_post_type_support($support){
		remove_post_type_support($this->post_type, $support);
	}
}



