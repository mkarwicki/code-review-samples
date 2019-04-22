<?php
	class HostEventListener {
		private $hash;

		/**
		 * HostEventListener constructor.
		 *
		 * @param $hash
		 */
		public function __construct( $hash ) {
			$this->hash = $hash;
		}

		public function onEmailLinkClickedEventSubscriber(){
			$page = $this->getHostPageByHash();
			if($page && $page->ID>0){
				if(!get_field('date_clicked',$page->ID) && get_field('status',$page->ID) == 'Wysłany'){
					update_field('status','Odwiedził',$page->ID);
					update_field('date_clicked',strtotime("now"),$page->ID);
				}
			}
		}

		private function getHostPageByHash(){
			$args = array(
				'numberposts'	=> 1,
				'post_type'		=> 'hosts',
				'meta_key'		=> 'hash',
				'meta_value'	=> $this->hash
			);
			$post = new WP_Query( $args );
			return $post->get_posts()[0];
		}

	}