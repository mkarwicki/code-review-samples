<?php

ini_set("display_errors", "1");
error_reporting(E_ALL);


/*DANE DO UPLOADU ZDJEC*/
if ( !function_exists('media_handle_upload') ) {
	require_once(ABSPATH . "wp-admin" . '/includes/image.php');
	require_once(ABSPATH . "wp-admin" . '/includes/file.php');
	require_once(ABSPATH . "wp-admin" . '/includes/media.php');
}
ini_set('max_execution_time', 9999);
class LogicNetCarImporter {
	public $xmlPath = 'xxx';


	public function __construct() {




	}


    function curl_get_file_contents($URL)
    {
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ( $c, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($c, CURLOPT_URL, $URL);
        $contents = curl_exec($c);
        curl_close($c);

        if ($contents) return $contents;
        else return FALSE;
    }


	public function runImport() {
        //$c = $this->curl_get_file_contents($this->xmlPath);

        $doc = new DOMDocument();
        $doc->load( $this->xmlPath);
        $doc->saveXML();


        $cars = $doc->getElementsByTagName( 'vehicle' );

		$noMedia=false; //ZAWSZE LADUJE OBRAZKI
		if ( $cars->length > 0 ):
			foreach ( $cars as $key => $car ):
				$xmlCarID = $car->getAttribute( 'id' );
				$carData = $this->getCarData( $car, $xmlCarID );

				if($carData['active']=='false')continue;
				$wpCarID=$this->isPageInSystem( $xmlCarID );
                var_dump('test3');
				$carClass=new car();
				if($carData['action']=='delete'){
					if($wpCarID){
						$carClass->remove($wpCarID );
					}
				}else{
					/*TERAZ NIE SPRAWDZAM CZY ACTION W XML JEST USTAWIONY ADD LUB MODIFY, SPRAWDZAM CZY W WP JEST MODEL LUB NIE*/
					if($wpCarID) {
						/*AUTO JEST W WP*/
						$newCarId=$carClass->modify( $carData, $wpCarID,$noMedia);
					}else{
						/*AUTA NIE MA W WP*/
						$newCarId=$carClass->add( $carData,$noMedia);
					}
					update_field('logicNet',true,$newCarId);
				}

				/*SYNCHRONIZACJA*/
				$syncService=new LogicNetCarSynchronizationService();
				echo ' - Opdowiedz serwera (akcja:'.$carData['action'].') - '.$syncService->sync($carData['action'],$xmlCarID).'<HR>';
			endforeach;
		endif;
	}


	function getCarData( $car, $xmlCarID ) {
		global $carSettings;
		$data = [];
		$data['action']=$car->getElementsByTagName('action')->item(0)->nodeValue;
		$data['active']=$car->getElementsByTagName('active')->item(0)->nodeValue;
		/*PODSTAWOWE DANE O AUCIE*/
		{
			foreach ( $carSettings as $key => $val ) {
				/*ID INTEGRACJI JAKO WYJATEK DANE POBIERA Z ATRYBUTU VEHICLE XML*/
				if($val['handle']=='integration_id') {
					$data['basic-settings'][ $val['handle'] ] = $xmlCarID;
				}elseif($val['handle']=='date_added'){
					$data['basic-settings'][$val['handle']] = date($val['acf_display_format']);
				}else{
					$data['basic-settings'][$val['handle']] = $car->getElementsByTagName( $val['handle'] )->item( 0 )->nodeValue;
				}
			}
		}
		/*SZABLON DO NAZWY AUTA ORZAZ JEGO URL*/
		{
			$data['carName'] = $data['basic-settings']['brand'] . ' ' . $data['basic-settings']['model'] . ' ' . $data['basic-settings']['model_version'];
			$data['carURL']  = sanitize_title(  $xmlCarID );
		}
		/*Wyposażenie dodatkowe*/
		{
			$description = $car->getElementsByTagName( 'special_equipment' );
			$data['carSpecialEquipment'] = $description->item( 0 )->nodeValue;
		}
		/*OPIS*/
		{
			$description = $car->getElementsByTagName( 'description' );
			$data['carDescription'] = $description->item( 0 )->nodeValue;
		}
		/*GALERIA*/
		{
			$images = $car->getElementsByTagName( 'image' );
			if ( $images->length > 0 ) {
				foreach ( $images as $key => $image ):
					$data['images'][] = $image->nodeValue;
				endforeach;
			}
		}
		/*WYPOSAŻENIE - GRUPY WYPOSAŻENIA*/
		{
			/*NAJPIERW POBIERAM NAZWY GRUP*/
			{
				$groups = $car->getElementsByTagName( 'param_group' );
				if ( $groups->length > 0 ) {
					foreach ( $groups as $key => $group ):
						$groupName   = $group->getAttribute( 'name' );
						$groupParams = $group->getElementsByTagName( 'param' );
						foreach ( $groupParams as $key2 => $parm ):
							$data['equipmentGroupsNames'][ $groupName ][ $parm->getAttribute( 'id' ) ] = $parm->nodeValue;
						endforeach;
					endforeach;
				}
			}
			/*TERAZ POBIERAM WARTOSCI DLA TYCH GRUP*/
			$groups = $car->getElementsByTagName( 'group' );
			{
				if ( $groups->length > 0 ) {
					foreach ( $groups as $key => $group ):
						$groupName   = $group->getAttribute( 'name' );
						$groupParams = $group->getElementsByTagName( 'param_value' );
						$list=[];
						foreach ( $groupParams as $key2 => $parm ):
							$propDescription = $data['equipmentGroupsNames'][ $groupName ][ $parm->getAttribute( 'id' ) ];
							if ( strlen( $parm->nodeValue ) > 0 ) {
								if ( $parm->nodeValue == 'Tak' ) {
									$list[] = ['element'=>$propDescription];
								} else {
									$list[] = ['element'=>$propDescription . ' - ' . $parm->nodeValue];
								}
							}
						endforeach;
						$data['equipmentGroups'][]=['name'=>$groupName,'list'=>$list];
					endforeach;
				}
			}
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
			//*TYLKO JEZELI BYL TO IPMORT MODELU Z BAZY LOGIC NET
			if(get_field('logicNet',$post->ID)==true){
				$post_id = $post->ID;
				return $post_id;
			}
		}
		return 0;
	}



}



