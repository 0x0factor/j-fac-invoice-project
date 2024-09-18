<?php
/**
 * @copyright ICZ Corporation (http://www.icz.co.jp/)
 * @license See the LICENCE file
 * @author <matcha@icz.co.jp>
 * @version $Id$
 */
require('mbfpdf.php');

class SIMPLE_COVERPAGEPDF extends MBFPDF
{
	function coverpage($_param, $_county,$_form){
		//自社印
		if($_param['Company']['SEAL']){
			$this->Image($_param['Company']['SEAL_IMAGE'],142, 70, 25,25);
		}

		//顧客情報
		$this->AddMBFont(GOTHIC ,'SJIS');

		//顧客郵便番号
		$this->SetFont(GOTHIC,'',9);
		$this->SetXY(21, 11);
		$str = "";
		if(empty($_param['Customer']['POSTCODE1']) && empty($_param['Customer']['POSTCODE2'])) {

		}else {
			$str = "〒";
			$str .= $_param['Customer']['POSTCODE1']."-".$_param['Customer']['POSTCODE2'];
		}
		$str = $this->conv($str);
		$this->Write(5,$str);

		//顧客住所
		$this->SetXY(21, 15);
			$str = "";
		if($_param['Customer']['CNT_ID']) {
			$str .= $_county[$_param['Customer']['CNT_ID']];
		}
		$str .= $_param['Customer']['ADDRESS'].$_param['Customer']['BUILDING']."\n\n";

		$str .= "";
		if(empty($_param['Customer']['PHONE_NO1']) && empty($_param['Customer']['PHONE_NO2']) && empty($_param['Customer']['PHONE_NO3'])) {

		}else {
			$str .= "Tel: ".$_param['Customer']['PHONE_NO1']."-".$_param['Customer']['PHONE_NO2']."-".$_param['Customer']['PHONE_NO3']."　";
		}
		$str .= "";
		if($_param['Customer']['FAX_NO1']&& $_param['Customer']['FAX_NO2']&& $_param['Customer']['FAX_NO3']){
			$str .= "Fax: ".$_param['Customer']['FAX_NO1']."-".$_param['Customer']['FAX_NO2']."-".$_param['Customer']['FAX_NO3'];
		}
		$str = $this->conv($str);
		$this->MultiCell( 110, 3, $str, 0, 'L');

		//顧客名
		$this->SetFont(GOTHIC,'',12);
		$this->SetXY(21, 30);
		$str = $_param['Customer']['NAME'];
		$str = $this->conv($str);
		$this->Write(5,$str);

		if(isset($_param['CustomerCharge']['UNIT']) && isset($_param['CustomerCharge']['CHARGE_NAME'])){
			$this->SetXY(21, 42);
		}else {
			$this->SetXY(21, 30);
		}
		$this->SetFont(GOTHIC,'',10);

		switch($_param[$_form]['HONOR_CODE'] ) {
			case 0:
				$str = '御中';
				break;

			case 1:
				$str = '様';
				break;

			case 2:
				$str = $_param[$_form]['HONOR_TITLE'];
		}
		$str = $this->conv($str);
		$this->Cell( 120, 5, $str, 'B', 1, 'R');

		//部署
		if(isset($_param['CustomerCharge']['UNIT'])){
			$this->SetXY(21, 36);
			$str = $_param['CustomerCharge']['UNIT'];
			$str = $this->conv($str);
			$this->Write(5,$str);
		}

		//顧客担当者名
		if(isset($_param['CustomerCharge']['CHARGE_NAME'])){
			$this->SetXY(21, 42);
			$str = $_param['CustomerCharge']['CHARGE_NAME'];
			$str = $this->conv($str);
			$this->Write(5,$str);
		}

		//書類送付のご案内
		$this->SetFont(GOTHIC,'',24);
		$this->SetLineWidth(0.6);
		$this->SetXY(70, 53);
		$str = "書類送付のご案内";

		$str = $this->conv($str);
		$this->Cell( 75, 10, $str, 'B', 1,'C');

		//自社名
		$this->SetFont(GOTHIC,'',12);
		$this->SetXY(112, 72);
		$str = $_param['Company']['NAME'];
		$str = $this->conv($str);
		$this->Write(5,$str);

		//自社郵便番号
		$this->SetFont(GOTHIC,'',9);
		$this->SetXY(112, 80);
		$str = "";
		if(empty($_param['Company']['POSTCODE1']) && empty($_param['Company']['POSTCODE1'])) {

		}else {
			$str = "〒".$_param['Company']['POSTCODE1']."-".$_param['Company']['POSTCODE2'];
		}
		$str = $this->conv($str);
		$this->Write(5,$str);

		//自社住所
		$this->SetXY(112, 84);
			$str = "";

		if($_param['Company']['CNT_ID']) {
			$str .= $_county[$_param['Company']['CNT_ID']];
		}
		$str.= $_param['Company']['ADDRESS'].$_param['Company']['BUILDING']."\n\n";
		if(empty($_param['Company']['PHONE_NO1']) && empty($_param['Company']['PHONE_NO2']) && empty($_param['Company']['PHONE_NO3'])) {

		}else {
			$str .= "Tel: ";
			$str .= $_param['Company']['PHONE_NO1']."-".$_param['Company']['PHONE_NO2']."-".$_param['Company']['PHONE_NO3']."　";

		}
		if($_param['Company']['FAX_NO1']&& $_param['Company']['FAX_NO2']&& $_param['Company']['FAX_NO3']){
			$str .= "Fax: ";
			$str .= $_param['Company']['FAX_NO1']."-".$_param['Company']['FAX_NO2']."-".$_param['Company']['FAX_NO3'];
		}
		$str = $this->conv($str);
		$this->MultiCell( 90, 3.3, $str, 0, 'L');


		//線の太さをもとに戻す
		$this->SetLineWidth(0.2);

		//件名枠
		$this->SetFont(GOTHIC,'B',13);
		$this->SetXY(19, 110);
		$str = "件名:".$_param[$_form]['SUBJECT'];
		$str = $this->conv($str);
		$this->Cell(177, 9, $str, 'T', 1, 'L');

		//発行日枠
		$this->SetXY(130, 112);
		$str = '発行日：'.substr($_param[$_form]['ISSUE_DATE'],0,4)."年".substr($_param[$_form]['ISSUE_DATE'],5,2)."月".substr($_param[$_form]['ISSUE_DATE'],8,2)."日";
		$str = $this->conv($str);
		$this->write(5,$str);

		//担当者枠
		if(isset($_param['Charge']['CHARGE_NAME'])){
			$this->SetXY(19, 119);
			$str = "担当者 : ".$_param['Charge']['CHARGE_NAME'];
			$str = $this->conv($str);
			$this->Cell(177, 9, $str, 'T', 1, 'L');
		}
		//送付書類
		$this->SetFont(GOTHIC,'B',12);
		$this->SetXY(19, 128);
		$str = "送付書類 : ";
		$str = $this->conv($str);
		$this->Cell(177, 9, $str, 'T', 1, 'L');

		$this->SetXY(55, 138 + 7);
		$str = "1.　";
		if($_form === 'Quote')		$str .= '見積書';
		if($_form === 'Bill') 		$str .= '請求書';
		if($_form === 'Delivery') 	$str .= '納品書';
		$str = $this->conv($str);
		$this->Write(5,$str);

		$this->SetXY(130, 138 + 7);
		$str = "・・・　 1部";
		$str = $this->conv($str);
		$this->Write(5,$str);


		$this->SetXY(19, 265);
		$this->Cell(177, 1, "", 'T', 1, 'L');
	}


	//顧客名のフォントサイズを動的に変更する
	function customer_font($length) {

		switch($length) {
			case 0:
			case 1:
			case 2:
			case 3:
			case 4:
			case 5:
			case 6:
			case 7:
			case 8:
			case 9:
			case 10:
			case 11:
				return 12.5;
			case 12:
			case 13:
			case 14:
				return 11;
			case 15:
			case 16:
			case 17:
				return 10.5;
			case 18:
			case 19:
			case 20:
				return 9.5;
			case 21:
			case 22:
			case 23:
				return 9;
			case 24:
			case 25:
			case 26:
				return 8.5;
			case 27:
			case 28:
				return 8;
			case 29:
			case 30:
				return 7.2;

			default :
				return 7;

		}
	}
	
	/**
	 * Writes the company & charge info on PDF
	 * @param unknown $_param
	 */
	
	function writeCompanyInfo($_param, $_county){
	    
	    $x = 129;
	    $y = 37;
	    $siz = 9;
		$offset = 0.5;
	    
	    // 横向きの場合
	    if($this->Direction == "1"){
	        $x = 214;
	        $y = 24;
	        $siz = 8;
			$offset = 0;
	    }
	    
	    //自社名
	    $this->SetXY( $x , $y );
	    $this->SetFont(MINCHO,'',$siz);
	    $str = $_param['Company']['NAME'];
	    $str = $this->conv($str);
	    $this->Write(5, $str);
	    
	    // 住所表示機能
	    if(!empty($_param['Company']['POSTCODE1']) && !empty($_param['Company']['POSTCODE2'])) {
	        $postcode = "〒" . $_param['Company']['POSTCODE1']."-".$_param['Company']['POSTCODE2'];
	    }
	    
	    $address = $_param['Company']['ADDRESS'];
	    if(!empty($_param['Company']['CNT_ID'])) {
	        $address = $_county[$_param['Company']['CNT_ID']] . $address;
	    }
	    
	    $building = $_param['Company']['BUILDING'];
	    if(!empty($_param['Company']['PHONE_NO1']) && !empty($_param['Company']['PHONE_NO2']) && !empty($_param['Company']['PHONE_NO3'])) {
	        $phone_no = "TEL ".$_param['Company']['PHONE_NO1']."-".$_param['Company']['PHONE_NO2']."-".$_param['Company']['PHONE_NO3'];
	    }
	    
	    if(!empty($_param['Company']['FAX_NO1']) && !empty($_param['Company']['FAX_NO2']) && !empty($_param['Company']['FAX_NO3'])) {
	        $fax_no = "FAX ".$_param['Company']['FAX_NO1']."-".$_param['Company']['FAX_NO2']."-".$_param['Company']['FAX_NO3'];
	    }
		if($_param['Company']['INVOICE_NUMBER']) {
			$invoice_number = "登録番号:" . $_param['Company']['INVOICE_NUMBER'];
		}

	    if(isset($_param['Charge']['UNIT']) && isset($_param['Charge']['CHARGE_NAME'])){
	        if(!empty($_param['Charge']['POSTCODE1']) && !empty($_param['Charge']['POSTCODE2'])) {
    	        $charge_postcode = "〒" . $_param['Charge']['POSTCODE1']."-".$_param['Charge']['POSTCODE2'];
    	    }
    	    
    	    $charge_address = $_param['Charge']['ADDRESS'];
    	    if(!empty($_param['Charge']['CNT_ID'])) {
    	        $charge_address = $_county[$_param['Charge']['CNT_ID']] . $charge_address;
    	    }
    	    $charge_building = $_param['Charge']['BUILDING'];
    	    if(!empty($_param['Charge']['PHONE_NO1']) && !empty($_param['Charge']['PHONE_NO2']) && !empty($_param['Charge']['PHONE_NO3'])) {
    	        $charge_phone_no = "TEL ".$_param['Charge']['PHONE_NO1']."-".$_param['Charge']['PHONE_NO2']."-".$_param['Charge']['PHONE_NO3'];
    	    }
    	    
    	    if(!empty($_param['Charge']['FAX_NO1']) && !empty($_param['Charge']['FAX_NO2']) && !empty($_param['Charge']['FAX_NO3'])) {
    	        $charge_fax_no = "FAX ".$_param['Charge']['FAX_NO1']."-".$_param['Charge']['FAX_NO2']."-".$_param['Charge']['FAX_NO3'];
    	    }
	    }
	    
	    if(isset($_param['Charge']['UNIT']) && isset($_param['Charge']['CHARGE_NAME'])){
    	    if(configure::read('PdfForceOverwriteChargeAddressEvenEmpty')){
    	        $postcode = $charge_postcode;
    	        $address = $charge_address;
    	        $building = $charge_building;
    	        $phone_no = $charge_phone_no;
    	        $fax_no = $charge_fax_no;
    	    } else {
    	        if(!empty($charge_postcode) && !empty($charge_address) ){
    	            $postcode = $charge_postcode;
    	            $address = $charge_address;
    	            if(!empty($charge_building) ){
    	                $building = $charge_building;
    	            } else {
    	                $building = "";
    	            }
    	        }
    	        if(!empty($charge_phone_no) ){
    	            $phone_no = $charge_phone_no;
    	        }
    	        if(!empty($charge_fax_no) ){
    	            $fax_no = $charge_fax_no;
    	        }
    	    }
	    }
	    
	    //郵便番号
	    $this->SetXY( $x , $y = $y + 5 + $offset);
	    $this->SetFont(MINCHO,'',$siz);
	    $str = $this->conv($postcode);
	    $this->Write(5, $str);
	    
	    //住所
	    $this->SetXY($x, $y = $y + 3 + $offset);
	    $this->SetFont(MINCHO,'',$siz);
	    $str = $this->conv($address);
	    $this->Write(5, $str);
	    
	    //建物名
	    $this->SetXY($x, $y = $y + 3 + $offset);
	    $this->SetFont(MINCHO,'',$siz);
	    $str = $this->conv($building);
	    $this->Write(5, $str);

		
	    
	    $this->SetXY($x, $y = $y + 3 + $offset);
	    $this->SetFont(MINCHO,'',$siz);
	    
	    // 電話番号 + FAX
	    $str ="";
	    if(!empty($phone_no)){
	        $str = $phone_no;
	    }
	    if(!empty($fax_no)){
	        if(!empty($str)){
	            $str .= "　";
	        }
	        $str .= $fax_no;
	    }
	    
	    $str = $this->conv($str);
	    $this->Write(5, $str);
	    
		//登録番号
	    $this->SetXY($x, $y = $y + 3 + $offset);
		$this->SetFont(MINCHO,'',$siz);
		$str = $this->conv($invoice_number);
		$this->Write(5, $str);
	    
	    //部署・自社担当者名
	    if(isset($_param['Charge']['UNIT']) && isset($_param['Charge']['CHARGE_NAME'])){
	        $str = "";
	        $unit = $_param['Charge']['UNIT'];
	        $charge_name = $_param['Charge']['CHARGE_NAME'];

	        $this->SetXY($x, $y = $y + 3 + $offset);
	        $this->SetFont(MINCHO,'',$siz);
	        
	        if(!empty($unit)){
	            $str .= $unit;
	        }
	        if(!empty($charge_name)){
	            if(!empty($str)){
	                $str .= "　";
	            }
	            $str .= $charge_name;
	        }
	        $str = $this->conv($str);
	        $this->Write(5, $str);
	    }
	}
	
	
}