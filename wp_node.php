<?php

class WP_Node {
	public $term;
	public $post;


	public function __construct($term_id, $taxonomy = null){
		$this->term = get_term($term_id, $taxonomy);
		$this->post = $this->get_post($term_id, $taxonomy);
	}


	/**
	 * TODO: Allow for some way of mapping logic to metadata or other taxonomies/terms
	 */
	public function register_term_meta(){
		$this->post = $this->insert_post();
	}

	public function add_meta_data($key, $value){
		add_post_meta($this->post->ID, $key, $value);
	}


	public function get_meta_data($key){
		return get_post_meta($this->post->ID, $key, true);
	}

	/**
	 * 
	 */
	private function insert_post(){
		
		$post_id = wp_insert_post(array(
			'post_status' 	=> 'publish',
			'post_type' 	=> $this->term->taxonomy,
			'post_name'		=> $this->term->slug,
			'post_title' 	=> ucfirst($this->term->taxonomy).': '.$this->term->name,
			'tax_input'		=> array( $this->term->taxonomy => array($this->term->term_id))
		));

		return get_post($post_id);
	}


	private function get_post($term_id, $taxonomy = null){
		$post = get_posts( 
			array(
				'post_type' => 'skcategory',
				'tax_query' => array(
					array(
						'terms' => $this->term->slug,
						'taxonomy' => $taxonomy,
						'field' => 'slug',
						'include_children' => false //wtf
					)
				)

			)
		);
		return $post[0];
	}

}




