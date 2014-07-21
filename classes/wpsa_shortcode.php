<?php 

if (!class_exists('WPSA_Shortcode')) {
	
	class WPSA_Shortcode{
		
		private $wpsamodel = null;
		
		function __construct(){
		
			## Register shortcodes
			add_shortcode( 'favourite-author-posts', array(&$this, 'favourite_author_posts_handler' ) );
			
			$this->wpsamodel = new Wpsa_Model();
		
		}	

		public function  favourite_author_posts_handler($atts){
		
			$html = '';
			$user_id = get_current_user_id();
			
			if(!empty($user_id)){

				
				$authors = $this->wpsamodel->getFavouriteAuthors($user_id);
				
				if(count($authors)){
					
				$author_ids = array();
				foreach($authors as $author){
					$author_ids[] = $author->author_id;
				}

				$args = array( 'author__in' => $author_ids );
				
				
				// the query
				$author_query = new WP_Query( $args ); ?>
				
				<?php if ( $author_query->have_posts() ) : ?>

				  <?php while ( $author_query->have_posts() ) : $author_query->the_post(); ?>
				    <?php $html .= $this->load_template_part('content','favourite-author-posts'); ?>
				  <?php endwhile; ?>

				  <?php wp_reset_postdata(); ?>
				
				<?php else:  ?>
				  	<p><?php _e( 'Sorry, no posts found from your favourite authors.' ); ?></p>
				<?php endif; 	

				}
			
			}
			return $html;
		}

		
		private function load_template_part($template_name, $part_name=null) {
			ob_start();
			get_template_part($template_name, $part_name);
			$var = ob_get_contents();
			ob_end_clean();
			return $var;
		}
		
		
		
	}
	
	
	$wpsa_shortcode =new WPSA_Shortcode();
	
}