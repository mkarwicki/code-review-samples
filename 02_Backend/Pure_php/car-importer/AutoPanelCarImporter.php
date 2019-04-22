<?php

if ( !function_exists('media_handle_upload') ) {
	require_once(ABSPATH . "wp-admin" . '/includes/image.php');
	require_once(ABSPATH . "wp-admin" . '/includes/file.php');
	require_once(ABSPATH . "wp-admin" . '/includes/media.php');
}
ini_set('max_execution_time', 9999);
class AutoPanelCarImporter {
	public $xmlPath = 'xxx';
	public function __construct() {


	}

	public function runImport() {
		$doc = new DOMDocument();
		$doc->load( $this->xmlPath );
		$cars = $doc->getElementsByTagName( 'ogloszenie' );
		if($_REQUEST['uploadMedia']){
			$uploadMedia=false;
		}else{
			$uploadMedia=true;
		}
		if ( $cars->length > 0 ):
			foreach ( $cars as $key => $car ):
				$xmlCarID=$car->getElementsByTagName('ofertaid')->item( 0 )->nodeValue;
				$carData = $this->getCarData($car,$xmlCarID);
				$carClass=new car();
				$wpCarID=$this->isPageInSystem( $xmlCarID );
				if($wpCarID>0) {
					$newCarID=$carClass->modify( $carData, $wpCarID,$uploadMedia );

				} else {
					/*AUTA NIE MA W WP*/
					$newCarID=$carClass->add( $carData,$uploadMedia );
				}
				/*USTAWIAM ATRYBUT LEGACY DLA STARYCH SAMOCHODOW*/
				update_field('legacy',true,$newCarID);
			endforeach;
		endif;
	}


	function getCarData( $car,$xmlCarID ) {
		global $carSettings;
		$data = [];
		/*PODSTAWOWE DANE O AUCIE*/
		{
			foreach ( $carSettings as $key => $val ) {
				/*ID INTEGRACJI JAKO WYJATEK DANE POBIERA Z ATRYBUTU VEHICLE XML*/
				if($val['handle']=='integration_id') {
					$data['basic-settings'][ $val['handle'] ] = $xmlCarID;
				}elseif($val['handle']=='date_added'){
					$data['basic-settings'][$val['handle']] = date($val['acf_display_format'],strtotime('2018-01-01')); //*USTAWIAM DATE NA 1 STYCZNIA 2018r W ten sposob zawsze nowo dodane samochody beda przed starym XML
				}else{
					if($val['old_xml_handle'] ){
						$tmp=$car->getElementsByTagName( $val['old_xml_handle'] )->item( 0 )->nodeValue;
						if($tmp=='olej napędowy (diesel)'){
							$tmp='Diesel';
						}
						$data['basic-settings'][$val['handle']] = ucfirst($tmp);
					}
				}
			}
		}
		/*SZABLON DO NAZWY AUTA ORZAZ JEGO URL*/
		{
			$data['carName'] = $data['basic-settings']['brand'] . ' ' . $data['basic-settings']['model'] . ' ' . $data['basic-settings']['model_version'];
			$data['carURL']  = 'c'.sanitize_title(  $xmlCarID );
		}
		/*OPIS*/
		{
			$description = $car->getElementsByTagName( 'opis' );
			$data['carDescription'] = str_replace('<br />','',$description->item( 0 )->nodeValue);
		}


		/*GALERIA*/
		{
			$images = $car->getElementsByTagName( 'zdjecie' );
			if ( $images->length > 0 ) {
				foreach ( $images as $key => $image ):
					$data['images'][] = $image->nodeValue;
				endforeach;
			}
		}
		/*WYPOSAŻENIE - GRUPY WYPOSAŻENIA*/
		/*LISTA*/
		{
			$list = explode(';',$car->getElementsByTagName( 'wyposazenie' )->item(0)->nodeValue);
			$tab=[];
			if(count($list)>0){

				foreach($list as $key=>$val){
					if(strlen($val)>0){
						$tab[]=['element'=>ucfirst($val)];
					}
				}
			}
			$data['equipmentGroups'][]=['name'=>'Wyposażenie','list'=>$tab];
		}
		return $data;
	}


	function isPageInSystem( $id ) {
		$posts = get_posts( array(
			'numberposts' => - 1,
			'post_type'   => 'car',
			'meta_key'    => 'integration_id',
			'meta_value'  => $id,
		) );
		foreach ( $posts as $key => $post ) {
			//*TYLKO JEZELI BYL TO IMPORRT ZE STAREJ BAZY (LEGACY) AUTOPANEL to model musi miec ustawione pole leagacy na true
			if(get_field('legacy',$post->ID)==true) {
				$post_id = $post->ID;
				return $post_id;
			}
		}
		return 0;
	}


}



