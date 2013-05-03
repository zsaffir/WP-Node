<?php

class Meta_Importer_CSV{
	private $taxonomy;
	private $filepath;
	private $meta = array();


	public function __construct($taxonomy){
		$this->taxonomy = $taxonomy;
	}

	public function parse($file){
		$this->filepath = $file;

		if (($handle = fopen($this->filepath, "r")) !== false) {

		    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		    	
				$this->meta[$data[1]] = array(
					'catgroupid' => array_pop($data),
					'verticle' => $data[0],
					'wcs_slug' => implode('-', $data)
					);
		    }
		    fclose($handle);
		}
	}

	public function add_meta(){
		foreach($this->terms as $term){

			$node = new WP_Node($term->slug, $this->taxonomy, 'slug');

			$node->register_term_meta();
			$node->add_meta_data('catgroupid', $term->meta['catgroupid']);
			$node->add_meta_data('wcs_slug', $term->meta['wcs_slug']);
		}	

		echo "<p>Metadata added</p>";
	}

	public function test_matches(){

		$parents = get_terms($this->taxonomy, array(
			'parent' => 0, 
			'fields' => 'ids',
			'hide_empty' => false
			));

		$terms = get_terms($this->taxonomy, array(
			'exclude' => $parents,
			'hide_empty' => false
			));

		foreach($terms as $term){

			$slug = $term->slug;

			if(isset($this->meta[$slug]) && $term->parent != 0){
				$term->meta = $this->meta[$slug];
				$matches[$slug] = $term;
			} else {
				$not_matched[$slug] = $term;
			}	
		}

		if(!empty($not_matched)){
			foreach($not_matched as $term){

				$parent = get_term($term->parent, $this->taxonomy);
				$slug = str_replace('-'.$parent->slug, '', $term->slug); //. '-' . $parent->slug;
				//int_pre($slug);

				if(isset($this->meta[$slug]) && $term->parent != 0){
					$term->meta = $this->meta[$slug];
					//print_pre($)
					$matches[$slug] = $term;
					unset($not_matched[$term->slug]);
				}
			}
		}

		if(!empty($not_matched)){
	 		echo "Not Matched:";
	 		print_pre($not_matched);
	 	} else {
	 		echo "<p>Everything Matched</p>";
	 	}
	 
	 	$this->terms = $matches;
	 	//print_pre($matches);
	}

	public function test_cr_links($offest, $end){


		$urls = array();

		foreach(array_slice($this->terms, $offset , $end)as $term){
			$node = new WP_Node($term->slug, $this->taxonomy, 'slug');

			$wcs_slug = $node->get_meta_data('wcs_slug');
			$catgroupid = $node->get_meta_data('catgroupid');

			$url = 'http://sears.com/' . $wcs_slug . '/cr-' . $catgroupid . '?sName=View+All';
		
			 $response = $this->curl($url);

			if( $response == 200 || $response == 301 ){
				$this->success[$term->slug] = $response;
			} else {
				$this->errors[$terms->slug] = $response;
			}

		}


	}

	private function curl($url){

		$options = array(
			CURLOPT_RETURNTRANSFER => 1, // return web page
			CURLOPT_URL => $url,
			CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
			CURLOPT_CONNECTTIMEOUT => 20,
			CURLOPT_FOLLOWLOCATION => TRUE
		);

		$curl = curl_init();

		curl_setopt_array($curl, $options);

		$content = curl_exec($curl);

		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		return $code;
			
	}
}
