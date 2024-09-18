<?php
/**
 * @copyright ICZ Corporation (http://www.icz.co.jp/)
 * @license See the LICENCE file
 * @author <matcha@icz.co.jp>
 * @version $Id$
 */
require('billpdf.php');

class TOTALBILLPDF_D extends BILLPDF
{
	var $count_page=0;
	var $past_pagenum=0;

	//ヘッダ
	function Header()
	{

	}

	function main($_param, $_county,$_count,$_accounttype=null,$_direction=null,$_billparam)
	{

		//印鑑
		//社判
		if($_param['Company']['SEAL']){
			$this->Image($_param['Company']['SEAL_IMAGE'],150, 65, 25,25);
		}
		//社印判
		if($_param['Customer']['CHR_ID'] && $_param['Charge']['SEAL']){
			$this->Image($_param['Charge']['SEAL_IMAGE'],168, 95, 15,15);
		}

		//印鑑
		//社判
		if($_param['Company']['SEAL']){
			$this->Image($_param['Company']['SEAL_IMAGE'],150, 186, 25,25);
		}
		//社印判
		if($_param['Customer']['CHR_ID'] && $_param['Charge']['SEAL']){
			$this->Image($_param['Charge']['SEAL_IMAGE'],168, 216, 15,15);
		}

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

		$this->SetLineWidth(0.3);
		$this->SetXY(19,32);
		$this->SetFont(MINCHO,'',13);
		$this->Cell( 172, 112, NULL, 1, 'L');

		$this->SetFont(MINCHO,'B',15);
		$this->SetXY(83, 45);
		$str = "合 計 請 求 書\n";
		$str = $this->conv($str);
		$this->Write(5,$str);

		//大枠（控）
		$this->SetXY(19,153);
		$this->SetFont(MINCHO,'',13);
		$this->Cell( 172, 112, NULL, 1, 'L');

		$this->SetFont(MINCHO,'B',15);
		$this->SetXY(76, 166);
		$str = "合 計 請 求 書 (控)\n";
		$str = $this->conv($str);
		$this->Write(5,$str);

		$this->SetLineWidth(0.2);
		//請求書番号
		$this->SetXY(163, 52);
		$this->SetFont(MINCHO,'',8);
		$str = 'No. '.$_param['Totalbill']['NO'];
		$str = $this->conv($str);
		$this->Cell( 35, 4, $str, 0, 'L');

		//発行日
		$this->SetXY(163, 56);
		$this->SetFont(MINCHO,'',8);
		$str = substr($_param['Totalbill']['ISSUE_DATE'],0,4)."年".substr($_param['Totalbill']['ISSUE_DATE'],5,2)."月".substr($_param['Totalbill']['ISSUE_DATE'],8,2)."日\n";
		$str = $this->conv($str);
		$this->Write(5,$str);

		//顧客住所
		$this->SetXY(28, 36);
		$this->SetFont(MINCHO,'',8);
		$str  = "";
		if(empty($_param['Customer']['POSTCODE1']) && empty($_param['Customer']['POSTCODE2'])) {

		}else {
			$str .= "〒";
			$str .= $_param['Customer']['POSTCODE1']."-".$_param['Customer']['POSTCODE2']."\n";
		}
		$str = $this->conv($str);
		$this->Write( 50, $str);

		$this->SetXY(28, 39);
		$this->SetFont(MINCHO,'',8);
		if($_param['Customer']['CNT_ID']) {
			$str = $_county[$_param['Customer']['CNT_ID']].$_param['Customer']['ADDRESS'];
		} else {
			$str = $_param['Customer']['ADDRESS'];
		}
		$str = $this->conv($str);
		$this->Write( 50, $str);

		$this->SetXY(28, 42);
		$this->SetFont(MINCHO,'',8);
		$str  = $_param['Customer']['BUILDING'];
		$str = $this->conv($str);
		$this->Write( 50, $str);

		//顧客名枠
		$this->SetXY(28, 70);
		$this->SetFont(MINCHO,'',$font_size);
		$str = "";
		$str = $this->conv($str);
		$this->Cell( 90, 4, $str, 'B', 1, 'R');

		if(mb_strlen($_param['Customer']['NAME']) > mb_strlen($_param['CustomerCharge']['UNIT']."　".$_param['CustomerCharge']['CHARGE_NAME'])) {
			$font_size = $this->customer_font(mb_strlen($_param['Customer']['NAME']));
		}else {
			$font_size = $this->customer_font(mb_strlen($_param['CustomerCharge']['UNIT']."　".$_param['CustomerCharge']['CHARGE_NAME']));
		}

		//顧客名
		$this->SetXY(28, 70);
		$this->SetFont(MINCHO,'',$font_size);
		$str = $_param['Customer']['NAME'];
		if(empty($_param['CustomerCharge']['UNIT']) && empty($_param['CustomerCharge']['CHARGE_NAME'])) {
			switch($_param['Totalbill']['HONOR_CODE'] ) {
				case 0:
					$str .= '　　御中';
					break;

				case 1:
					$str .= '　　様';
					break;

				case 2:
					$str .= "　　".$_param['Totalbill']['HONOR_TITLE'];
			}
		}

		$str = $this->conv($str);
		$this->Cell( 90, 4, $str);

		//顧客担当者・部署名枠
		if($_param['CustomerCharge']['UNIT'] || $_param['CustomerCharge']['CHARGE_NAME']) {
			$this->SetXY(28, 76);
			$this->SetFont(MINCHO,'',$font_size);

			$str = "";
			if($_param['CustomerCharge']['UNIT']) {
				$str .= $_param['CustomerCharge']['UNIT']."　";
			}
			$str .=$_param['CustomerCharge']['CHARGE_NAME'];

				switch($_param['Totalbill']['HONOR_CODE'] ) {
					case 0:
						$str .= '　　御中';
						break;

					case 1:
						$str .= '　　様';
						break;

					case 2:
						$str .= "　　".$_param['Totalbill']['HONOR_TITLE'];
				}

			$str = $this->conv($str);
			$this->Cell( 90, 4, $str, 'B', 1, 'L');
		}

		//件名枠
		$this->SetXY(28, 85);
		$this->SetFont(MINCHO,'',10);
		$str = "";
		$str = $this->conv($str);
		$this->Cell( 75, 4, $str, 'B', 1, 'R');

		//件名
		$this->SetXY(28, 85);
		$this->SetFont(MINCHO,'',10);
		$str = "件名：".$_param['Totalbill']['SUBJECT'];
		$str = $this->conv($str);
		$this->Cell( 70, 4, $str);

		//支払期限枠
		$this->SetXY(28, 92);
		$this->SetFont(MINCHO,'',8);
		$str = "振込期限　:";
		$str .= $_param['Totalbill']['DUE_DATE'];
		$str = $this->conv($str);
		$this->Cell( 65, 4, $str, 'B', 1, 'L');

		//自社項目
		$this->SetXY(130, 71);
		$this->SetFont(MINCHO,'B',11);
		$str = $_param['Company']['NAME'];
		$str = $this->conv($str);
		$this->MultiCell( 55, 4, $str, 0, 'L');



		$this->SetXY(130, 76);
		$this->SetFont(MINCHO,'',8);

		$str  = "";
		if(empty($_param['Company']['POSTCODE1']) && empty($_param['Company']['POSTCODE2'])) {

		}else {
			$str .= "〒";
			$str .= $_param['Company']['POSTCODE1']."-".$_param['Company']['POSTCODE2']."\n";

		}

		if($_param['Company']['CNT_ID']) {
			$str .= $_county[$_param['Company']['CNT_ID']];
		}else{

		}

		$str .= $_param['Company']['ADDRESS']."\n";
		$str .= $_param['Company']['BUILDING']."\n";
		$str .= "";
		if(empty($_param['Company']['PHONE_NO1']) && empty($_param['Company']['PHONE_NO2']) && empty($_param['Company']['PHONE_NO3'])) {

		} else {
			$str .= "TEL ";
			$str .= $_param['Company']['PHONE_NO1']."-".$_param['Company']['PHONE_NO2']."-".$_param['Company']['PHONE_NO3']."";

		}

		if($_param['Company']['FAX_NO1'] && $_param['Company']['FAX_NO2'] && $_param['Company']['FAX_NO3'])
		{
			$str .= "  FAX ".$_param['Company']['FAX_NO1']."-".$_param['Company']['FAX_NO2']."-".$_param['Company']['FAX_NO3'];
		}
		if(isset($_param['Charge']['UNIT'])&& $_param['Charge']['CHARGE_NAME']){
			$str  .= "\n".$_param['Charge']['UNIT']."　".$_param['Charge']['CHARGE_NAME']."\n";
		}
		$str = $this->conv($str);
		$this->MultiCell( 55, 3.3, $str, 0, 'L');

		//代表者印枠
		$this->SetXY(130, 95);
		$this->Cell(18, 15, '', 1, 0, 'C');
		$this->Cell(18, 15, '', 1, 0, 'C');
		$this->Cell(18, 15, '', 1, 0, 'C');


		//下記の通りご請求申し上げます。
		$this->SetXY(28, 114);
		$this->SetFont(MINCHO,'B',9);
		$str = "下記の通りご請求申し上げます。";
		$str = $this->conv($str);
		$this->MultiCell( 55, 4, $str, 0, 'L');

		//下記の通りご請求申し上げます。
		$this->SetXY(170, 114);
		$this->SetFont(MINCHO,'B',9);
		$str = "単位：円";
		$str = $this->conv($str);
		$this->MultiCell( 55, 4, $str, 0, 'L');

		//項目一覧
			$this->SetLeftMargin(29);
		$this->SetY(120);
		$this->SetFont(MINCHO,'',8);

		$str = "前回御請求額";
		$str = $this->conv($str);
		$this->Cell(25, 5, $str, 1, 0, 'C', 1);

		$str = "御入金額";
		$str = $this->conv($str);
		$this->Cell(25, 5, $str, 1, 0, 'C', 1);

		$str = "繰越金額";
		$str = $this->conv($str);
		$this->Cell( 25, 5, $str, 1, 0, 'C', 1);

		$str = "今回御買上額";
		$str = $this->conv($str);
		$this->Cell( 27, 5, $str, 1, 0, 'C', 1);

		$str = "消費税";
				$str = $this->conv($str);
		$this->Cell( 25, 5, $str, 1, 0, 'C', 1);

		$str = "今回請求額";
		$str = $this->conv($str);
		$this->Cell( 28, 5, $str, 1, 1, 'C', 1);

		//項目一覧
		$this->SetLeftMargin(29);
		$this->SetY(125);
		$this->SetFont(MINCHO,'',10);

		$str = ($_param['Totalbill']['LASTM_BILL'])?number_format($_param['Totalbill']['LASTM_BILL']):'0';
		$str = $this->conv($str);
				$this->Cell(25, 10, $str, 1, 0, 'R');

		$str = ($_param['Totalbill']['DEPOSIT'])?number_format($_param['Totalbill']['DEPOSIT']):'0';
		$str = $this->conv($str);
				$this->Cell(25, 10, $str, 1, 0, 'R');

		$str = ($_param['Totalbill']['CARRY_BILL'])?number_format($_param['Totalbill']['CARRY_BILL']):'0';
		$str = $this->conv($str);
				$this->Cell( 25, 10, $str, 1, 0, 'R');

		$str = ($_param['Totalbill']['SALE'])?number_format($_param['Totalbill']['SALE']):'0';
		$str = $this->conv($str);
				$this->Cell( 27, 10, $str, 1, 0, 'R');

		$str = ($_param['Totalbill']['SALE_TAX'])?number_format($_param['Totalbill']['SALE_TAX']):'0';
		$str = $this->conv($str);
				$this->Cell( 25, 10, $str, 1, 0, 'R');

		$str = ($_param['Totalbill']['THISM_BILL'])?number_format($_param['Totalbill']['THISM_BILL']):'0';
		$str = $this->conv($str);
				$this->Cell( 28, 10, $str, 1, 1, 'R');

		//ここから控え

		//請求書番号
		$this->SetXY(163, 173);
		$this->SetFont(MINCHO,'',8);
		$str = 'No. '.$_param['Totalbill']['NO'];
		$str = $this->conv($str);
		$this->Cell( 35, 4, $str, 0, 'L');

		//発行日
		$this->SetXY(163, 177);
		$this->SetFont(MINCHO,'',8);
		$str = substr($_param['Totalbill']['ISSUE_DATE'],0,4)."年".substr($_param['Totalbill']['ISSUE_DATE'],5,2)."月".substr($_param['Totalbill']['ISSUE_DATE'],8,2)."日\n";
		$str = $this->conv($str);
		$this->Write(5,$str);

		//顧客住所
		$this->SetXY(28, 157);
		$this->SetFont(MINCHO,'',8);

			$str  = "";
		if(empty($_param['Customer']['POSTCODE1']) && empty($_param['Customer']['POSTCODE2'])) {

		}else {
			$str  .= "〒";
			$str .= $_param['Customer']['POSTCODE1']."-".$_param['Customer']['POSTCODE2']."\n";
		}
		$str = $this->conv($str);
		$this->Write( 50, $str);

		$this->SetXY(28, 160);
		$this->SetFont(MINCHO,'',8);
		if($_param['Customer']['CNT_ID']) {
			$str = $_county[$_param['Customer']['CNT_ID']].$_param['Customer']['ADDRESS'];
		}else {
			$str = $_param['Customer']['ADDRESS'];

		}
		$str = $this->conv($str);
		$this->Write( 50, $str);

		$this->SetXY(28, 163);
		$this->SetFont(MINCHO,'',8);
		$str  = $_param['Customer']['BUILDING'];
		$str = $this->conv($str);
		$this->Write( 50, $str);

		//顧客名枠
		$this->SetXY(28, 191);
		$this->SetFont(MINCHO,'',10);
		$str = "";
		$str = $this->conv($str);
		$this->Cell( 90, 4, $str, 'B', 1, 'R');

		if(mb_strlen($_param['Customer']['NAME']) > mb_strlen($_param['CustomerCharge']['UNIT']."　".$_param['CustomerCharge']['CHARGE_NAME'])) {
			$font_size = $this->customer_font(mb_strlen($_param['Customer']['NAME']));
		}else {
			$font_size = $this->customer_font(mb_strlen($_param['CustomerCharge']['UNIT']."　".$_param['CustomerCharge']['CHARGE_NAME']));
		}

		//顧客名
		$this->SetXY(28, 191);
		$this->SetFont(MINCHO,'',$font_size);
		$str = $_param['Customer']['NAME'];
		if(empty($_param['CustomerCharge']['UNIT']) && empty($_param['CustomerCharge']['CHARGE_NAME'])) {
			switch($_param['Totalbill']['HONOR_CODE'] ) {
				case 0:
					$str .= '　　御中';
					break;

				case 1:
					$str .= '　　様';
					break;

				case 2:
					$str .= "　　".$_param['Totalbill']['HONOR_TITLE'];
			}
		}
		$str = $this->conv($str);
		$this->Cell( 90, 4, $str);

		//顧客担当者・部署名枠
		if($_param['CustomerCharge']['UNIT'] || $_param['CustomerCharge']['CHARGE_NAME']) {
			$this->SetXY(28, 197);
			$this->SetFont(MINCHO,'',$font_size);

			$str = "";
			if($_param['CustomerCharge']['UNIT']) {
				$str .= $_param['CustomerCharge']['UNIT']."　";
			}
			$str .=$_param['CustomerCharge']['CHARGE_NAME'];

			switch($_param['Totalbill']['HONOR_CODE'] ) {
				case 0:
					$str .= '御中';
					break;

				case 1:
					$str .= '様';
					break;

				case 2:
					$str .= $_param['Totalbill']['HONOR_TITLE'];
					break;
			}

			$str = $this->conv($str);
			$this->Cell( 90, 4, $str, 'B', 1, 'L');

		}

		//件名枠
		$this->SetXY(28, 206);
		$this->SetFont(MINCHO,'',10);
		$str = "";
		$str = $this->conv($str);
		$this->Cell( 75, 4, $str, 'B', 1, 'R');

		//件名
		$this->SetXY(28, 206);
		$this->SetFont(MINCHO,'',10);
		$str = "件名：".$_param['Totalbill']['SUBJECT'];
		$str = $this->conv($str);
		$this->Cell( 70, 4, $str);

		//支払期限枠
		$this->SetXY(28, 213);
		$this->SetFont(MINCHO,'',8);
		$str = "振込期限　:";
		$str .= $_param['Totalbill']['DUE_DATE'];
		$str = $this->conv($str);
		$this->Cell( 65, 4, $str, 'B', 1, 'L');

		//自社項目
		$this->SetXY(130, 192);
		$this->SetFont(MINCHO,'B',11);
		$str = $_param['Company']['NAME'];
		$str = $this->conv($str);
		$this->MultiCell( 55, 4, $str, 0, 'L');



		$this->SetXY(130, 197);
		$this->SetFont(MINCHO,'',8);
			$str  = "";
		if(empty($_param['Company']['POSTCODE1']) && empty($_param['Company']['POSTCODE2'])) {

		}else {
			$str  .= "〒";
			$str .= $_param['Company']['POSTCODE1']."-".$_param['Company']['POSTCODE2']."\n";

		}

		if($_param['Company']['CNT_ID']) {
			$str .= $_county[$_param['Company']['CNT_ID']];
		}else{

		}

		$str .= $_param['Company']['ADDRESS']."\n";
		$str .= $_param['Company']['BUILDING']."\n";
		$str .= "";
		if(empty($_param['Company']['PHONE_NO1']) && empty($_param['Company']['PHONE_NO2']) && empty($_param['Company']['PHONE_NO3'])) {

		} else {
			$str .= "TEL ";
			$str .= $_param['Company']['PHONE_NO1']."-".$_param['Company']['PHONE_NO2']."-".$_param['Company']['PHONE_NO3']."";

		}

		if($_param['Company']['FAX_NO1'] && $_param['Company']['FAX_NO2'] && $_param['Company']['FAX_NO3'])
		{
			$str .= "  FAX ".$_param['Company']['FAX_NO1']."-".$_param['Company']['FAX_NO2']."-".$_param['Company']['FAX_NO3'];
		}
		if(isset($_param['Charge']['UNIT'])&& $_param['Charge']['CHARGE_NAME']){
			$str  .= "\n".$_param['Charge']['UNIT']."　".$_param['Charge']['CHARGE_NAME']."\n";
		}
		$str = $this->conv($str);
		$this->MultiCell( 55, 3.3, $str, 0, 'L');

		//代表者印枠
		$this->SetXY(130, 216);
		$this->Cell(18, 15, '', 1, 0, 'C');
		$this->Cell(18, 15, '', 1, 0, 'C');
		$this->Cell(18, 15, '', 1, 0, 'C');

		//下記の通りご請求申し上げます。
		$this->SetXY(28, 235);
		$this->SetFont(MINCHO,'B',9);
		$str = "下記の通りご請求申し上げます。";
		$str = $this->conv($str);
		$this->MultiCell( 55, 4, $str, 0, 'L');

		//下記の通りご請求申し上げます。
		$this->SetXY(170, 235);
		$this->SetFont(MINCHO,'B',9);
		$str = "単位：円";
		$str = $this->conv($str);
		$this->MultiCell( 55, 4, $str, 0, 'L');

		$this->SetLeftMargin(29);
		$this->SetY(241);
		$this->SetFont(MINCHO,'',8);

		$str = "前回御請求額";
		$str = $this->conv($str);
		$this->Cell(25, 5, $str, 1, 0, 'C', 1);

		$str = "御入金額";
		$str = $this->conv($str);
		$this->Cell(25, 5, $str, 1, 0, 'C', 1);

		$str = "繰越金額";
		$str = $this->conv($str);
		$this->Cell( 25, 5, $str, 1, 0, 'C', 1);

		$str = "今回御買上額";
		$str = $this->conv($str);
		$this->Cell( 27, 5, $str, 1, 0, 'C', 1);

		$str = "消費税";
		$str = $this->conv($str);
		$this->Cell( 25, 5, $str, 1, 0, 'C', 1);

		$str = "今回請求額";
		$str = $this->conv($str);
		$this->Cell( 28, 5, $str, 1, 1, 'C', 1);

		//項目一覧
		$this->SetLeftMargin(29);
		$this->SetY(246);
		$this->SetFont(MINCHO,'',10);

		$str = ($_param['Totalbill']['LASTM_BILL'])?number_format($_param['Totalbill']['LASTM_BILL']):'0';
		$str = $this->conv($str);
		$this->Cell(25, 10, $str, 1, 0, 'R');

		$str = ($_param['Totalbill']['DEPOSIT'])?number_format($_param['Totalbill']['DEPOSIT']):'0';
		$str = $this->conv($str);
		$this->Cell(25, 10, $str, 1, 0, 'R');

		$str = ($_param['Totalbill']['CARRY_BILL'])?number_format($_param['Totalbill']['CARRY_BILL']):'0';
		$str = $this->conv($str);
		$this->Cell( 25, 10, $str, 1, 0, 'R');

		$str = ($_param['Totalbill']['SALE'])?number_format($_param['Totalbill']['SALE']):'0';
		$str = $this->conv($str);
		$this->Cell( 27, 10, $str, 1, 0, 'R');

		$str = ($_param['Totalbill']['SALE_TAX'])?number_format($_param['Totalbill']['SALE_TAX']):'0';
		$str = $this->conv($str);
		$this->Cell( 25, 10, $str, 1, 0, 'R');

		$str = ($_param['Totalbill']['THISM_BILL'])?number_format($_param['Totalbill']['THISM_BILL']):'0';
		$str = $this->conv($str);
		$this->Cell( 28, 10, $str, 1, 1, 'R');

		for($i=0;$i<$_count;$i++){
			$this->AddPage();
			parent::main($_billparam[$i],$_county,$_accounttype,$_direction,$_billparam[$i]['items'],$_billparam[$i]['pages']);
		}

	}

	//フッター
	function Footer()
	{
		if($this->Direction==0){
			$this->setXY(100,293);
		}
		else{
			$this->setXY(140,207);
		}


		if($this->PageNo()>1){

			$this->SetFont(MINCHO,'',9);
			$str = ($this->PageNo()-1-$this->past_pagenum).'/'.$this->TotalPage[$this->count_page];
			$str = mb_convert_encoding($str, "SJIS");
			$this->Write(3,$str);
			if($this->TotalPage[$this->count_page]==$this->PageNo()-1-$this->past_pagenum){
				$this->past_pagenum += $this->TotalPage[$this->count_page];
				$this->count_page++;
			}
		}
	}

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
				return 10;
			case 12:
			case 13:
			case 14:
				return 9.5;
			case 15:
			case 16:
			case 17:
				return 9;
			case 18:
			case 19:
			case 20:
				return 8.5;
			case 21:
			case 22:
			case 23:
				return 8;
			case 24:
			case 25:
			case 26:
				return 7.5;
			case 27:
			case 28:
			case 29:
				return 7;
			case 30:
			case 31:
			case 32:
				return 6.5;
			case 33:
			case 34:
			case 35:
				return 6;
			default :
				return 6;
		}
	}


}