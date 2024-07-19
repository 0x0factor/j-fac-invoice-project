<?php
/**
 * @copyright ICZ Corporation (http://www.icz.co.jp/)
 * @license See the LICENCE file
 * @author <matcha@icz.co.jp>
 * @version $Id$
 */
require('mbfpdf.php');

class COVERPAGEPDF extends MBFPDF
{

	//ヘッダ
  function Header()
    {

	}

	function main($_param, $_county){
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
			$str .= "Tel: ";
			$str .= $_param['Customer']['PHONE_NO1']."-".$_param['Customer']['PHONE_NO2']."-".$_param['Customer']['PHONE_NO3']."　";
		}
		$str .= "";
		if($_param['Customer']['FAX_NO1']&& $_param['Customer']['FAX_NO2']&& $_param['Customer']['FAX_NO3']){
			$str .= "Fax: ";
			$str .= $_param['Customer']['FAX_NO1']."-".$_param['Customer']['FAX_NO2']."-".$_param['Customer']['FAX_NO3'];
		}
		$str = $this->conv($str);
		$this->MultiCell( 110, 3, $str, 0, 'L');

		//顧客名
		$this->SetFont(GOTHIC,'',12);
		$this->SetXY(21, 30);
		$str = $_param['Data']['Coverpages']['CUSTOMER_NAME'];
		$str = $this->conv($str);
		$this->Write(5,$str);


		//様
		$this->SetFont(GOTHIC,'',10);

		if(empty($_param['Data']['Coverpages']['CUSTOMER_CHARGE_UNIT']) && empty($_param['Data']['Coverpages']['CUSTOMER_CHARGE_NAME'])) {
			$this->SetXY(21, 30);
		}else {
			$this->SetXY(21, 42);
		}

		$str = "様";
		$str = $this->conv($str);
		$this->Cell( 120, 5, $str, 'B', 1, 'R');

		//部署
		$this->SetXY(21, 36);
		$str = $_param['Data']['Coverpages']['CUSTOMER_CHARGE_UNIT'];
		$str = $this->conv($str);
		$this->Write(5,$str);


		//顧客担当者名
		$this->SetXY(21, 42);
		$str = $_param['Data']['Coverpages']['CUSTOMER_CHARGE_NAME'];
		$str = $this->conv($str);
		$this->Write(5,$str);

		//書類送付のご案内
		$this->SetFont(GOTHIC,'',24);
		$this->SetLineWidth(0.6);
		$this->SetXY(70, 53);
		if($_param['Data']['Coverpages']['SEND_METHOD'] == 1) $str = "FAX送信のご案内";
		else $str = "書類郵送のご案内";

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
			$str .= "〒";
			$str .= $_param['Company']['POSTCODE1']."-".$_param['Company']['POSTCODE2'];
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
		$str .= "";
		if(empty($_param['Company']['PHONE_NO1']) && empty($_param['Company']['PHONE_NO2']) && empty($_param['Company']['PHONE_NO3'])) {

		}else {
			$str .= "Tel: ";
			$str .= $_param['Company']['PHONE_NO1']."-".$_param['Company']['PHONE_NO2']."-".$_param['Company']['PHONE_NO3']."　";

		}
		$str .= "";
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
		$str = "件名 : ".$_param['Data']['Coverpages']['TITLE'];
		$str = $this->conv($str);
		$this->Cell(177, 9, $str, 'T', 1, 'L');

		//発行日枠
		$this->SetXY(130, 112);
		$str = "発行日 : ".$_param['Data']['Coverpages']['DATE'];
		$str = $this->conv($str);
		$this->write(5,$str);

		//担当者枠
		$this->SetXY(19, 119);
		$str = "担当者 : ".$_param['Data']['Coverpages']['CHARGE_NAME'];
		$str = $this->conv($str);
		$this->Cell(177, 9, $str, 'T', 1, 'L');
		//状態
		$this->SetFont(GOTHIC,'',9);
		$this->SetXY(19, 128);
		$this->Cell(177, 9, '', 'T', 1, 'L');

		$this->SetXY(30, 131);
		$str = $_param['Data']['Coverpages']['STATUS_ASAP']?"■":"□";
		$str .= "　至急";
		$str = $this->conv($str);
		$this->Write(3,$str);
		$this->SetXY(30, 131);

		$this->SetXY(70, 131);
		$str = $_param['Data']['Coverpages']['STATUS_REFERENCE']?"■":"□";
		$str .= "　ご参考まで";
		$str = $this->conv($str);
		$this->Write(3,$str);

		$this->SetXY(110, 131);
		$str = $_param['Data']['Coverpages']['STATUS_COMFIRMATION']?"■":"□";
		$str .= "　ご確認ください";
		$str = $this->conv($str);
		$this->Write(3,$str);

		$this->SetXY(150, 131);
		$str = $_param['Data']['Coverpages']['STATUS_REPLY']?"■":"□";
		$str .= "　ご返信ください";
		$str = $this->conv($str);
		$this->Write(3,$str);

		//送付書類
		$this->SetFont(GOTHIC,'B',12);
		$this->SetXY(19, 137);
		$str = "送付書類 : ";
		$str = $this->conv($str);
		$this->Cell(177, 9, $str, 'T', 1, 'L');

		for($i = 0; $i < $_param['Data']['Coverpages']['dataformline']; $i++){
			$this->SetXY(55, 147 + 7*$i);
			$str = ($i+1).".　".$_param['Data'][$i]['Reports']['DOCUMENT_TITLE'];
			$str = $this->conv($str);
			$this->Write(5,$str);

			$this->SetXY(130, 147 + 7*$i);
			$str = "・・・　".$_param['Data'][$i]['Reports']['DOCUMENT_NUMBER']."部";
			$str = $this->conv($str);
			$this->Write(5,$str);
		}

		//連絡事項
		$this->SetFont(GOTHIC,'B',12);
		$this->SetXY(19, 217);
		$str = "連絡事項 : ";
		$str = $this->conv($str);
		$this->Cell(177, 9, $str, 'T', 1, 'L');

		$this->SetXY(19, 227);
		$str = $_param['Data']['Coverpages']['CONTACT'];
		$str = $this->conv($str);
		$this->Write(5,$str);

		$this->SetXY(19, 265);
		$this->Cell(177, 1, "", 'T', 1, 'L');
	}



	//フッター
  function Footer()
    {
    }
}