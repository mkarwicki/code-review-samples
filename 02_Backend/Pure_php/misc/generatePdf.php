<?php
	function addIntVal($current,$toAdd){
		$toAdd=explode(' ',$toAdd);
		$current=$current+$toAdd[0];
		return $current;
	}
	function maxDepth($current,$new){
		$new=explode(' ',$new);
		return $current>$new[0] ? $current : $new[0];
	}
	include('backend/dispatcher.php');
	include('includes/pdf/mpdf/mpdf.php');
	include('includes/pdf/pdf/pdfContent.php');
	$mpdf=new mPDF('HelveticaCustom','A4-L','0','0','0','0');
	$mpdf->SetAutoFont(AUTOFONT_HELVETICA);
	$mpdf->SetMargins(0,0,0);
	//HEADER AND BODY DEFINITION
	$printStyle=file_get_contents('css/pdf.css');
    $mpdf->WriteHTML($printStyle,1);
	$mpdf->WriteHTML($contentTop);
	$newQuantityTable=array();
	if(count($_SESSION['elements'][0])):
		$dilemeter=1;
		$targetWidth=500/$dilemeter;
		foreach($_SESSION['elements'] as $key=>$element):
			$targetHeight=$element['workspaceHeight'];
			break;
		endforeach;
		$targetHeight=$targetHeight/$dilemeter;
		$mpdf->WriteHTML('<div id="projectionContainer" style="height:'.$targetHeight.'px">');
			$mpdf->WriteHTML('<div id="projection" style="width:'.$targetWidth.'px;height:'.$targetHeight.'px">');
			foreach($_SESSION['elements'] as $key=>$element):
				$newQuantityTable[$element['identifier']]=$newQuantityTable[$element['identifier']] >0 ? $newQuantityTable[$element['identifier']]+1 : 1;
				$elementWidth=$element['elementWidth']/$dilemeter;
				$elementHeight=$element['elementHeight']/$dilemeter;
				$elementTop=$element['elementTop']/$dilemeter;
				$elementLeft=$element['elementLeft']/$dilemeter;
				$mpdf->WriteHTML('
					<div class="wrapper">
						<div class="positionFix" style="margin-top:'.$elementTop.'px; margin-left:'.$elementLeft.'px">
							<img 
								src="'.$element['ImageSrc'].'" 
								width="'.$elementWidth.'"
								height="'.$elementHeight.'"
							/>
						</div>
					</div>
				');
			endforeach;
			$mpdf->WriteHTML('</div>');
		$mpdf->WriteHTML('</div>');



		//*ELEMENTS LISTS
		$sumPrice=0;
		$sumWidth=0;
		$sumHeight=0;
		$maxDepth=0;
		$mpdf->WriteHTML('<table id="elementsList" cellpadding="0" cellspacing="0"><tr>');
			$mpdf->WriteHTML('<th colspan="5">'.t('Elements list').'</th><tr>');
			$mpdf->WriteHTML('<tr>	
					  <td class="label nameLabel" width="200">'.t('Name').'</td>
					  <td class="label dimentions" width="285">'.t('Width x Height x Depth').'</td>
					  <td class="label qtyLabel" width="55">'.t('Q-ty').'</td>
					  <td class="label unitPriceLabel" width="125">'.t('Unit price').'</td>
					  <td class="label priceLabel" width="145">'.t('Price').'</td>
			  </tr>');
				$wasAllready=array();
				foreach($_SESSION['elements'] as $key=>$element):
					if($wasAllready[$element['identifier']]>0){continue;}
					$qty=$newQuantityTable[$element['identifier']];
					if(count($_SESSION['elements'])==($key+1)){$iflast='last';}
					$maxDepth=maxDepth($maxDepth,$element['Depth']);
					//$sumPrice['price']+=$qty*$element['price'];
					$sumPrice=addIntVal($sumPrice,($element['Price']*$qty));
					$mpdf->WriteHTML('<tr>
						  <td class="value nameValue '.$iflast.'"><small>'.$element['identifier'].'</small> '.$element['Name'].'</td>
						  <td class="value dimentions '.$iflast.'">'.$element['Width'].' x '.$element['Height'].' x '.$element['Depth'].'</td>
						  <td class="value qtyValue '.$iflast.'">'.$qty.'</td>														  
						  <td class="value unitPriveValue '.$iflast.'">'.number_format($element['Price'],0).' '.$_SESSION['currencySymbol'].'</td>
						  <td class="value priceValue '.$iflast.'">'.number_format(($qty*$element['Price']),0).' '.$_SESSION['currencySymbol'].'</td>
					</tr>');
					$sumWidth=$element['totalWidth'];
					$sumHeight=$element['totalHeight'];
					$wasAllready[$element['identifier']]=$wasAllready[$element['identifier']] >0 ? $wasAllready[$element['identifier']]+1 : 1;
				endforeach;
				if(($key+1)==1){
					$quantity='1 '.t('element');
				}else{
					$quantity=($key+1).' '.t('elements');
				}
				$mpdf->WriteHTML('<tr>
				  <td class="summary elementQuantityValue">'.$quantity.'</td>
				  <td class="summary dimentions">'.$sumWidth.' x '.$sumHeight.' x '.$maxDepth.' cm</td>
				  <td class="summary qtyValue">'.($key+1).'</td>
				  <td class="summary unitPrice"></td>
				  <td class="summary priceValue">'.number_format($sumPrice,0).' '.$_SESSION['currencySymbol'].'</td>
				</tr>');
		$mpdf->WriteHTML('</table>');
	endif;



	//*CLOSE BODY
	$mpdf->WriteHTML($contentBottom);
	$date=date('Y-m-d');
	if(!isset($_REQUEST['email'])):
		$mpdf->Output('englesson-set-'.$date.'.pdf','D');
		//$mpdf->Output();
	else:
		include('includes/pdf/sendMail.php');
		// You can now optionally also send it to the browser
		//$mpdf->Output();
		exit;
	endif;


	function validateEmail($email) {
      return filter_var($email, FILTER_VALIDATE_EMAIL);
   }
?>
