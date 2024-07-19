<?php
/**
 * @copyright ICZ Corporation (http://www.icz.co.jp/)
 * @license See the LICENCE file
 * @author <matcha@icz.co.jp>
 * @version $Id$
 */
require('mbfpdf.php');

class RECEIPTPDF extends MBFPDF
{

	//ヘッダ
	function Header()
	{

	}

	function main($_param, $_county)
	{

		switch($_param['Company']['COLOR']) {
			//黒
			case 0:
			$line_color = array('R' => 136, 'G' => 136, 'B' => 136);
			$column_color = array('R' => 204, 'G' => 204, 'B' => 204);
			$row_color = array('R' => 238, 'G' => 238, 'B' => 238);
			break;

			//青
			case 1:
			$line_color = array('R' => 0, 'G' => 99, 'B' => 244);
			$column_color = array('R' => 135, 'G' => 179, 'B' => 230);
			$row_color = array('R' => 212, 'G' => 237, 'B' => 255);
			break;

			//赤
			case 2:
			$line_color = array('R' => 255, 'G' => 89, 'B' => 158);
			$column_color = array('R' => 255, 'G' => 181, 'B' => 184);
			$row_color = array('R' => 255, 'G' => 240, 'B' => 255);
			break;

			//緑
			case 3:
			$line_color = array('R' => 0, 'G' => 88, 'B' =>52);
			$column_color = array('R' => 160, 'G' => 217, 'B' => 168);
			$row_color = array('R' => 223, 'G' => 242, 'B' => 226);
			break;
		}

		$this->SetDrawColor($line_color['R'],$line_color['G'],$line_color['B']);
		$this->SetFillColor($column_color['R'],$column_color['G'],$column_color['B']);

		//大枠
		$this->SetLineWidth(0.3);
		$this->SetXY(21,25);
		$this->SetFont(MINCHO,'',16);
		$this->Cell( 168, 80, NULL, 1, 'L');

		$this->SetFont(MINCHO,'B',14);
		$this->SetXY(31, 30);
		$str = "領 収 書";
		$str = $this->conv($str);
		$this->Write(5,$str);

		//大枠（控）
		$this->SetXY(21,110);
		$this->SetFont(MINCHO,'',16);
		$this->Cell( 168, 80, NULL, 1, 'L');

		$this->SetFont(MINCHO,'B',14);
		$this->SetXY(31, 115);
		$str = "領 収 書 (控)";
		$str = $this->conv($str);
		$this->Write(5,$str);
		$this->SetLineWidth(0.2);



		//下マージンの設定
		$this->SetAutoPageBreak(true, 5);


		//顧客名枠
		$font_size = $this->customer_font(mb_strlen($_param['Bill']['CST_ID']));

		$this->SetXY(31, 40);
		$this->SetFont(MINCHO,'',$font_size);
		$str = "様";
		$str = $this->conv($str);
		$this->Cell( 85, 4, $str, 'B', 1, 'R');

		//顧客名
		$this->SetXY(31, 40);
		$this->SetFont(MINCHO,'',$font_size);
		$str = $_param['Bill']['CST_ID'];
		$str = $this->conv($str);
		$this->Cell( 85, 4, $str,'', 0, 'L');

		//請求金額枠
		$this->SetXY(31,53);
		$this->SetFont(MINCHO,'B',16);
		$str =  $_param['Bill']['TOTAL'] ? '\\'.number_format($_param['Bill']['TOTAL']).'-' : '\\0-';
		$str = $this->conv($str);
		$this->Cell( 150, 8, $str, 0, 1, 'C', 1);

		//但書き枠
		$this->SetXY(71, 65);
		$this->SetFont(MINCHO,'',11);
		$str = "但 ".$_param['Bill']['PROVISO'];
		$str = $this->conv($str);
		$this->Cell( 75, 4, $str, 'B', 1, 'L');

		//発行日
		$this->SetXY(71, 70);
		$this->SetFont(MINCHO,'',11);
		$str = "発行日 ".substr($_param['Bill']['DATE'],0,4)."年".substr($_param['Bill']['DATE'],5,2)."月".substr($_param['Bill']['DATE'],8,2)."日\n";
		$str = $this->conv($str);
		$this->Write(5,$str);

		//自社項目
		$this->SetLeftMargin(130);
		$this->SetY(75);
		$this->SetFont(MINCHO,'B',8);
		$str = $_param['Company']['NAME']."\n";
		$str = $this->conv($str);
		$this->Write(5,$str);

		$this->SetLeftMargin(130);
		$this->SetY(80);;
		$this->SetFont(MINCHO,'',8);

		$str  = "〒";
		if(empty($_param['Company']['POSTCODE1']) && empty($_param['Company']['POSTCODE2']) && empty($_param['Company']['POSTCODE3'])) {

		}else {
			$str .= $_param['Company']['POSTCODE1']."-".$_param['Company']['POSTCODE2']."\n";
		}

		if($_param['Company']['CNT_ID']) {
			$str .= $_county[$_param['Company']['CNT_ID']];
		}else {

		}
		$str .= $_param['Company']['ADDRESS']."\n";
		$str .= $_param['Company']['BUILDING']."\n";


		$str .= "TEL ";

		if(empty($_param['Company']['PHONE_NO1']) && empty($_param['Company']['PHONE_NO2']) && empty($_param['Company']['PHONE_NO3'])) {

		}else {
			$str .= $_param['Company']['PHONE_NO1']."-".$_param['Company']['PHONE_NO2']."-".$_param['Company']['PHONE_NO3']."\n";
		}

		$str = $this->conv($str);
		$this->Write(4,$str);

		//印鑑枠
		$this->SetXY(31,75);
		$this->SetFont(MINCHO,'',16);
		$this->Cell( 22, 22, NULL, 1, 'L');

		//領収書番号枠
		$this->SetLeftMargin(152);
		$this->SetY(31);
		$this->SetFont(MINCHO,'',9);
		$str = "No.".$_param['Bill']['RECEIPT_NUMBER'];
		$str = $this->conv($str);
		$this->Cell( 35, 4, $str, '', 1, 'L');




		//控え

		//下マージンの設定
		$this->SetAutoPageBreak(true, 5);



		//枠及びラインの色の設定　※ここをいじれば以下すべての枠の色が変わります。


		//顧客名枠
		$font_size = $this->customer_font(mb_strlen($_param['Bill']['CST_ID']));

		$this->SetXY(31, 125);
		$this->SetFont(MINCHO,'', $font_size);
		$str = "様";
		$str = $this->conv($str);
		$this->Cell( 85, 4, $str, 'B', 1, 'R');

		//顧客名
		$this->SetXY(31, 125);
		$this->SetFont(MINCHO,'', $font_size);
		$str = $_param['Bill']['CST_ID'];
		$str = $this->conv($str);
		$this->Cell( 85, 4, $str,'', 0, 'L');

		//請求金額枠
		$this->SetXY(31,138);
		$this->SetFont(MINCHO,'B',16);
		$str =  $_param['Bill']['TOTAL'] ? '\\'.number_format($_param['Bill']['TOTAL']).'-' : '\\0-';
		$str = $this->conv($str);
		$this->Cell( 150, 8, $str, 0, 1, 'C', 1);

		//但書き枠
		$this->SetXY(71, 150);
		$this->SetFont(MINCHO,'',11);
		$str = "但 ".$_param['Bill']['PROVISO'];
		$str = $this->conv($str);
		$this->Cell( 75, 4, $str, 'B', 1, 'L');

		//発行日
		$this->SetXY(71, 155);
		$this->SetFont(MINCHO,'',11);
		$str = "発行日 ".substr($_param['Bill']['DATE'],0,4)."年".substr($_param['Bill']['DATE'],5,2)."月".substr($_param['Bill']['DATE'],8,2)."日\n";
		$str = $this->conv($str);
		$this->Write(5,$str);

		//自社項目
		$this->SetLeftMargin(130);
		$this->SetY(160);
		$this->SetFont(MINCHO,'B',8);
		$str = $_param['Company']['NAME']."\n";
		$str = $this->conv($str);
		$this->Write(5,$str);

		$this->SetLeftMargin(130);
		$this->SetY(165);
		$this->SetFont(MINCHO,'',8);
			$str  = "〒";
		if(empty($_param['Company']['POSTCODE1']) && empty($_param['Company']['POSTCODE2']) && empty($_param['Company']['POSTCODE3'])) {

		}else {
			$str .= $_param['Company']['POSTCODE1']."-".$_param['Company']['POSTCODE2']."\n";
		}

		if($_param['Company']['CNT_ID']) {
			$str .= $_county[$_param['Company']['CNT_ID']];
		}else {

		}
		$str .= $_param['Company']['ADDRESS']."\n";
		$str .= $_param['Company']['BUILDING']."\n";


		$str .= "TEL ";

		if(empty($_param['Company']['PHONE_NO1']) && empty($_param['Company']['PHONE_NO2']) && empty($_param['Company']['PHONE_NO3'])) {

		}else {
			$str .= $_param['Company']['PHONE_NO1']."-".$_param['Company']['PHONE_NO2']."-".$_param['Company']['PHONE_NO3']."\n";
		}		$str = $this->conv($str);
		$this->Write(4,$str);

		//印鑑枠
		$this->SetXY(31,75);
		$this->SetFont(MINCHO,'',16);
		$this->Cell( 22, 22, NULL, 1, 'L');

		//領収書番号枠
		$this->SetLeftMargin(152);
		$this->SetY(116);
		$this->SetFont(MINCHO,'',9);
		$str = "No.".$_param['Bill']['RECEIPT_NUMBER'];
		$str = $this->conv($str);
		$this->Cell( 35, 4, $str, '', 1, 'L');
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
			case 12:
				return 11;
			case 13:
			case 14:
			case 15:
				return 10;
			case 15:
			case 16:
			case 17:
			case 18:
				return 9;
			case 19:
			case 20:
			case 21:
				return 8.5;
			case 22:
			case 23:
			case 24:
				return 8;
			case 25:
			case 26:
			case 27:
				return 7.5;
			case 28:
			case 29:
			case 30:
				return 7;

			default :
				return 7;

		}


	}



	//フッター
  function Footer()
    {
    }
}