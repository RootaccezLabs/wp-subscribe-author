<?php 

if (!class_exists('WPSA_Shortcode')) {
	
	class WPSA_Shortcode{
		
		private $wpsamodel = null;
		
		function __construct(){
		
			## Register shortcodes
			add_shortcode( 'favourite-author-posts', array(&$this, 'favourite_author_posts_handler' ) );
			add_shortcode( 'subscribe-author-button', array(&$this, 'subscribe_author_button_handler' ) );
			
			$this->wpsamodel = new Wpsa_Model();
		
		}	
		/*
		 * [favourite-author-posts] shortcode handler
		 */
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
		
		
		
		
		/*
		 * [subscribe-author-button] shortcode handler
		 * Use this shortcode to display the subscribe author button on pages/post
		 */
		public function subscribe_author_button_handler($atts){
			
			$html = '';
			
			$authorID =  $_GET['authorID'];
			
			$authorID = get_the_author_meta('ID');
			
			
			
			if(!is_numeric($authorID)){
				$authorID = $this->wpsamodel->getAuthorIDbyNicename($authorID);
			}
			
			
			
			$user_id = get_current_user_id();
			
			
			$num_subscribers =  $this->wpsamodel->get_num_subscribers($authorID);
	
	
			
			if($authorID != 0){
				$html  .= '<div class="wpsa-button-wrap">';
				
				$first_name = get_the_author_meta('first_name');
				
			
				if($user_id !=0 || ($user_id ==0 && $user_id != $authorID)){
					$html  .= '<h4>'.__('Subscribe this author','wp-subscribe-author').'</h4>';
					//$html  .= '<span>'.sprintf(_n('%1$s having %2$s Subscriber','%1$s having %2$s Subscribers',ucfirst($first_name),$num_subscribers,"wp-subscribe-author"),ucfirst($first_name),$num_subscribers).'</span>';	
				}
				
				$html  .= '<div class="wpsa-footer">';
			
			
				if($user_id ==0){
			
					$html  .= '<input type="email" name="wpsa-subcriber-mail" id="wpsa-subcriber-mail" value="" placeholder="'. __('Enter your email to subscribe with author','wp-subscribe-author').'"> 
					<button class="wpsa-subscribe-btn" data-authorID="'.$authorID.'" data-userID="0">'.__('Subscribe','wp-subscribe-author').'</button>';
			
				
				}	
				else if($user_id != $authorID){ 
					if($this->wpsamodel->is_user_subscribed($authorID, $user_id)){
						//unsubscribe
						$btn_txt = __('Unsubscribe','wp-subscribe-author');
				
					}
					else{
						//subscribe
						$btn_txt = __('Subscribe','wp-subscribe-author');
		
					}
					
		
					$html  .= '<button class="wpsa-subscribe-btn" data-authorID="'.$authorID.'" data-userID="'.$user_id.'" >'.$btn_txt.'</button>';
					
					
					
				}
				$html  .= '<div class="wpsa-message"></div>';
				$html  .= '</div>';
				$html  .= '</div>';
			
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