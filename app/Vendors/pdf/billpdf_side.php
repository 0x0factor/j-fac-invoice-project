<?php
/**
 * @copyright ICZ Corporation (http://www.icz.co.jp/)
 * @license See the LICENCE file
 * @author <matcha@icz.co.jp>
 * @version $Id$
 */
require('simple_coverpage.php');

class BILLPDF_SIDE extends SIMPLE_COVERPAGEPDF
{
	public $cover = 0;

	//ヘッダ
	function Header()
	{
	}

	function main($_param, $_county,$_accounttype,$_direction,$_items,$_pages){
		//社判
		if($_param['Company']['SEAL'] && $_param['Bill']['CMP_SEAL_FLG']){
			$this->Image($_param['Company']['SEAL_IMAGE'],240, 25, 22,22);
		}

			//社員判
		if($_param['Bill']['CHR_ID'] && $_param['Charge']['SEAL'] && $_param['Bill']['CHR_SEAL_FLG']){
			$this->Image($_param['Charge']['SEAL_IMAGE'],252.5, 49.5, 15,15);
		}

		$this->SetMargins(0, 0, 0);
		$this->SetAutoPageBreak(true, 1.0);

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
		$this->SetFont(MINCHO,'B',16);
		$this->SetXY(28, 7);

		$str = "御 請 求 書";
		$str = $this->conv($str);
		$this->SetFillColor($column_color['R'],$column_color['G'],$column_color['B']);
		$this->Cell( 241, 8, $str, 1, 1, 'C',1);
		$this->SetTextColor(0);

		//No.
		$this->SetXY(231, 17);
		$this->SetFont(MINCHO,'',9);
		$str = 'No.';
		$str = $this->conv($str);
		$this->Write(5, $str);

		//見積書番号
		$this->SetXY(247, 17);
		$this->SetFont(MINCHO,'',9);
		$str = $_param['Bill']['NO'];
		$str = $this->conv($str);
		$this->Cell( 23, 5, $str, 0, 1, 'R');

		//日付
		$this->SetXY(249,21);
		$this->SetFont(MINCHO,'',9);
		$str = substr($_param['Bill']['ISSUE_DATE'],0,4)."年".substr($_param['Bill']['ISSUE_DATE'],5,2)."月".substr($_param['Bill']['ISSUE_DATE'],8,2)."日";
		$str = $this->conv($str);
		$this->Cell( 21, 5, $str, 0, 1, 'R');

		//部署・顧客担当者名
		if(isset($_param['CustomerCharge']['UNIT'])&&isset($_param['CustomerCharge']['CHARGE_NAME'])) {
			//顧客名
			$this->SetXY(28, 19);
			$this->SetFont(MINCHO,'',11);
			$str = $_param['Customer']['NAME'];
			$str = $this->conv($str);
			$this->Cell( 150, 4, $str, 'B');

			//部署
			$this->SetXY(28, 25);
			$this->SetFont(MINCHO,'',11);
			$str = $_param['CustomerCharge']['UNIT']."　".$_param['CustomerCharge']['CHARGE_NAME'];
			$str = $this->conv($str);
			$this->Cell( 150, 4, $str, 'B');
		}
		else{
			$this->SetXY(28, 25);
			$this->SetFont(MINCHO,'',11);
			$str = $_param['Customer']['NAME'];
			$str = $this->conv($str);
			$this->Cell( 150, 4, $str, 'B');
		}

		//御中
		$this->SetXY(164, 24);
		$this->SetFont(MINCHO,'',11);
		switch($_param['Bill']['HONOR_CODE'] ) {
			case 0:
				$str = '御中';
				break;

			case 1:
				$str = '様';
				break;

			case 2:
				$str = $_param['Bill']['HONOR_TITLE'];
		}
		$str = $this->conv($str);
		$this->Write(5, $str);

		//下記の通り御見積もり申し上げます。
		$this->SetXY(28, 29);
		$this->SetFont(MINCHO,'',8);
		$str = "下記の通りご請求申し上げます。";
		$str = $this->conv($str);
		$this->Write(5, $str);

		//件名
		$this->SetXY(28, 35.5);
		$this->SetFont(MINCHO,'',10);
		$this->SetDrawColor($column_color['R'],$column_color['G'],$column_color['B']);
		$str = "件名:".$_param['Bill']['SUBJECT'];
		$str = $this->conv($str);
		$this->Cell( 90, 4.5, $str, 'B', 1, 'L');

		$temp_y = 43.5;
		if(!empty($_param['Bill']['FEE'])){
			//振込手数料
			$this->SetXY(28, $temp_y);
			$this->SetFont(MINCHO,'',9);
			$str = "振込手数料:".$_param['Bill']['FEE'];
			$str = $this->conv($str);
			$this->Cell( 80, 4.5, $str, 'B', 1, 'L');
			$temp_y  = $temp_y + 6;
		}
		if(!empty($_param['Bill']['DUE_DATE'])){
			//振込期限
			$this->SetXY(28, $temp_y);
			$this->SetFont(MINCHO,'',9);
			$str = "振込期限:".$_param['Bill']['DUE_DATE'];
			$str = $this->conv($str);
			$this->Cell( 80, 4.5, $str, 'B', 1, 'L');
		}
		$this->SetDrawColor($line_color['R'],$line_color['G'],$line_color['B']);
		//自社名
		$this->writeCompanyInfo($_param, $_county);
		//印鑑枠
		$this->SetXY(215, 49);
		$this->Cell(18, 16, '', 1, 0, 'C');
		$this->Cell(18, 16, '', 1, 0, 'C');
		$this->Cell(18, 16, '', 1, 0, 'C');

		//複数あるか税率があるかを確認
		$tax_kinds = array('TEN_RATE_TOTAL','REDUCED_RATE_TOTAL','EIGHT_RATE_TOTAL','FIVE_RATE_TOTAL');
		$tax_kind_count = 0;
		foreach($tax_kinds as $key){
			if($_param['Bill'][$key]){
				$tax_kind_count++;
			}
		}

		//合計金額
		$this->SetXY(28, 56);
		$this->SetLineWidth(0.4);
		$this->SetFont(MINCHO,'B',13);
		$str = "合計金額";
		$str = $this->conv($str);
		$this->Cell( 100, 7, $str, 'B', 1, 'L');
		$this->Line(28, 64, 128, 64);
		$this->SetLineWidth(0.2);

		$this->SetXY(28, 57);
		$str = '\\'.number_format($_param['Bill']['TOTAL']).'-';
		$str = $this->conv($str);
		$this->Cell( 100, 7, $str, 0, 1, 'R');
		//単位・円
		$this->SetXY(256, 65);
		$this->SetFont(MINCHO,'',8);
		$str = "単位：円";
		$str = $this->conv($str);
		$this->Write(5, $str);

		//表の幅
		$w_no		=  10 ;
		$w_code		=  20 ;
		$w_item		= 100 ;
		$w_quantity	=  35 ;
		$w_unit		=  35 ;
		$w_total	=  41 ;

		// 項目数(改ページなどの非項目を含まない数)
		$num_item_count = 0;
		$max_item_count = 0;
		for($i = 0; isset($_param[$i]); $i++) {
			$fbreak = isset($_param[$i]['Billitem']['LINE_ATTRIBUTE'])
			&& intval($_param[$i]['Billitem']['LINE_ATTRIBUTE']) == 8; // 改ページ
			if (!$fbreak) {
				$max_item_count++;
			}
		}

		//表の表示
		for($j = 0 ; $j < $_pages; $j++){

			$amount=0;
			$discount_m=0;
			$item_count=0;
			$rows=0;

			//1ページのみの場合
			if($_pages == 1 && $j == 0){
				$page_num = 14;
				$this->SetXY(28, 70);
				$this->SetFont(MINCHO,'B',8);
				$this->SetDrawColor($line_color['R'],$line_color['G'],$line_color['B']);
				$this->SetFillColor($column_color['R'],$column_color['G'],$column_color['B']);
				$this->Cell($w_no, 6, $this->conv('No.'), 'T', 0, 'C',1);
				$this->Cell($w_code, 6, $this->conv('商品コード'), 'T', 0, 'C',1);
				$this->Cell($w_item, 6, $this->conv('品目名'), 'T', 0, 'C',1);
				$this->Cell($w_quantity, 6, $this->conv('数量'), 'T', 0, 'C',1);
				$this->Cell($w_unit, 6, $this->conv('単価'), 'T', 0, 'C',1);
				$this->Cell($w_total, 6, $this->conv('合計'), 'T', 0, 'C',1);
				$this->SetFont(MINCHO,'',8);
				$i = 0;

				$y = 168;

				if($_param['Bill']['REDUCED_RATE_TOTAL']){
					$this->SetXY(27, $y - 8 );
					$this->SetFont(MINCHO,'',8);
					$str = "「※」は軽減税率対象であることを示します。";
					$str = $this->conv($str);
					$this->Write(5, $str);
					$y = $y + 2;
				}

				//備考欄
				$this->SetXY(28, $y - 5);
				$this->SetFont(MINCHO,'B',9);
				$str = "備考欄";
				$str = $this->conv($str);
				$this->Write(5, $str);

				$this->SetFont(MINCHO,'',9);
				$this->SetDrawColor($line_color['R'],$line_color['G'],$line_color['B']);
				$this->SetXY(28, $y);
				$str = $_param['Bill']['NOTE'];
				$str = $this->conv($str);
				if($tax_kind_count >= 1){
					$this->Cell( 130, 24, '', 1, 1, 'C');
				}else{
					$this->Cell( 172, 24, '', 1, 1, 'C');
				}
				$this->SetXY(28, $y+0.5);
				$this->SetFont(MINCHO,'',8);
				if($tax_kind_count >= 1){
					$this->MBMultiCell(130, 4, $str, 0, 'L');
				}else{
					$this->MBMultiCell(172, 4, $str, 0, 'L');
				}
				$this->SetFont(MINCHO,'B',8);

				if($tax_kind_count >= 1){
					$tax_kind_x = 158;
					$tax_kind_y = $y;
					$this->SetFont(MINCHO,'',7);
					$this->SetXY($tax_kind_x, $tax_kind_y);
					$this->Cell( 64, 24, '', 1, 1, 'C');

					$str = '・内訳';
					$str = $this->conv($str);
					$this->SetXY($tax_kind_x, $y);
					$this->Write(5, $str);
					if($_param['Bill']['TEN_RATE_TOTAL']){
						$tax_kind_y = $tax_kind_y + 4;
						$this->SetXY($tax_kind_x, $tax_kind_y);
						$str = '10%対象      '.number_format($_param['Bill']['TEN_RATE_TOTAL']);
						$str .= ' (消費税'.number_format($_param['Bill']['TEN_RATE_TAX']).')';
						$str = $this->conv($str);
						$this->Write(5, $str);
					}
					if($_param['Bill']['REDUCED_RATE_TOTAL']){
						$tax_kind_y = $tax_kind_y + 4;
						$this->SetXY($tax_kind_x, $tax_kind_y);
						$str = '8%(軽減)対象 '.number_format($_param['Bill']['REDUCED_RATE_TOTAL']);
						$str .= ' (消費税'.number_format($_param['Bill']['REDUCED_RATE_TAX']).')';
						$str = $this->conv($str);
						$this->Write(5, $str);
					}
					if($_param['Bill']['EIGHT_RATE_TOTAL']){
						$tax_kind_y = $tax_kind_y + 4;
						$this->SetXY($tax_kind_x, $tax_kind_y);
						$str = '8%対象       '.number_format($_param['Bill']['EIGHT_RATE_TOTAL']);
						$str .= ' (消費税'.number_format($_param['Bill']['EIGHT_RATE_TAX']).')';
						$str = $this->conv($str);
						$this->Write(5, $str);
					}
					if($_param['Bill']['FIVE_RATE_TOTAL']){
						$tax_kind_y = $tax_kind_y + 4;
						$this->SetXY($tax_kind_x, $tax_kind_y);
						$str = '5%対象       '.number_format($_param['Bill']['FIVE_RATE_TOTAL']);
						$str .= ' (消費税'.number_format($_param['Bill']['FIVE_RATE_TAX']).')';
						$str = $this->conv($str);
						$this->Write(5, $str);
					}
					//小計などの下部の表示レイアウトの設定値
					$total_area_x = 222;
					$total_cell = 16;
					$price_cell = 31;
				}else{
					$total_area_x = 200;
					$total_cell = 28;
					$price_cell = 41;
				}

				$h = 8;
				if(!empty($_param['Bill']['DISCOUNT'])){
					$h = (double) 24 / 4;
					$this->SetXY($total_area_x, $y);
					$this->SetFont(MINCHO,'B',8);
					$this->SetFillColor($column_color['R'],$column_color['G'],$column_color['B']);
					$this->Cell($total_cell, $h, $this->conv('　割引'), '1', 0, 'L',1);
					$this->SetFont(MINCHO,'',8);
					$this->SetFillColor(0xFF, 0xFF, 0xFF);

					if($_param['Bill']['DISCOUNT_TYPE'] == 0 ){
						$str = $_param['Bill']['DISCOUNT'] ? number_format($_param['Bill']['DISCOUNT'] ) .'%': '0';
					}elseif($_param['Bill']['DISCOUNT_TYPE'] == 1 ){
						$str = $_param['Bill']['DISCOUNT'] ? '▲'.number_format($_param['Bill']['DISCOUNT'] ) : '0';
					}else{
						$str ='';
					}

					$str = $this->conv($str);
					$this->Cell($price_cell, $h, $str, 1, 0, 'R',1);
					$y = $y + $h;
				}
				$this->SetXY($total_area_x, $y);
				$this->SetFont(MINCHO,'B',8);
				$this->SetFillColor($column_color['R'],$column_color['G'],$column_color['B']);
				$this->Cell($total_cell, $h, $this->conv('　小計'), '1', 0, 'L',1);
				$this->SetFillColor(0xFF, 0xFF, 0xFF);
				$this->SetFont(MINCHO,'',8);
				$str = $_param['Bill']['SUBTOTAL'] ? number_format($_param['Bill']['SUBTOTAL']) : '0';
				$str = $this->conv($str);
				$this->Cell($price_cell, $h, $str, 1, 0, 'R',1);
				$y = $y + $h;

				$this->SetXY($total_area_x, $y);
				$this->SetFont(MINCHO,'B',8);
				$this->SetFillColor($column_color['R'],$column_color['G'],$column_color['B']);
				$this->Cell($total_cell, $h, $this->conv('　消費税'), '1', 0, 'L',1);
				$this->SetFillColor(0xFF, 0xFF, 0xFF);
				$this->SetFont(MINCHO,'',8);
				$str = $_param['Bill']['SALES_TAX'] ? number_format($_param['Bill']['SALES_TAX']) : '0';
				$str = $this->conv($str);
				$this->Cell($price_cell, $h, $str, 1, 0, 'R',1);
				$y = $y + $h;

				$this->SetXY($total_area_x, $y);
				$this->SetFont(MINCHO,'B',8);
				$this->SetFillColor($column_color['R'],$column_color['G'],$column_color['B']);
				$this->Cell($total_cell, $h, $this->conv('　合計'), '1', 0, 'L',1);
				$this->SetFillColor(0xFF, 0xFF, 0xFF);
				$this->SetFont(MINCHO,'',8);
				$str = $_param['Bill']['TOTAL'] ? number_format($_param['Bill']['TOTAL']) : '0';
				$str = $this->conv($str);
				$this->Cell($price_cell, $h, $str, 1, 0, 'R',1);
				$y = $y + 8;

				//振込先
				$this->SetXY(28, $y);
				$this->SetFont(MINCHO,'B',9);
				$str = "振込先：";
				$acount = '';
				if(isset($_param['Company']['ACCOUNT_TYPE'])) $acount = $_accounttype[$_param['Company']['ACCOUNT_TYPE']];
				$str = "振込先：".$_param['Company']['ACCOUNT_HOLDER']."　".$_param['Company']['BANK_NAME'].
				"　".$_param['Company']['BANK_BRANCH']."　".$acount."　".$_param['Company']['ACCOUNT_NUMBER'];
				$str = $this->conv($str);
				$this->Cell( 172, 5, $str, 'B', 1, 'L');
			}

			//2ページ以上の1ページ目
			elseif($_pages > 1 && $j == 0){
				$this->SetXY(28, 70);
				$this->SetFont(MINCHO,'B',8);
				$this->SetDrawColor($line_color['R'],$line_color['G'],$line_color['B']);
				$this->SetFillColor($column_color['R'],$column_color['G'],$column_color['B']);
				$this->Cell($w_no, 8, $this->conv('No.'), 'T', 0, 'C',1);
				$this->Cell($w_code, 8, $this->conv('商品コード'), 'T', 0, 'C',1);
				$this->Cell($w_item, 8, $this->conv('品目名'), 'T', 0, 'C',1);
				$this->Cell($w_quantity, 8, $this->conv('数量'), 'T', 0, 'C',1);
				$this->Cell($w_unit, 8, $this->conv('単価'), 'T', 0, 'C',1);
				$this->Cell($w_total, 8, $this->conv('合計'), 'T', 0, 'C',1);
				$i = 0;
				$page_num = 20;
			}

			//2ページ以上の最後のページ
			else if($_pages > 1 && $j == $_pages - 1){
				$page_num = 24;
				$this->SetXY(28, 7);
				$this->SetFont(MINCHO,'B',8);
				$this->SetDrawColor($line_color['R'],$line_color['G'],$line_color['B']);
				$this->SetFillColor($column_color['R'],$column_color['G'],$column_color['B']);
				$this->Cell($w_no, 8, $this->conv('No.'), 'T', 0, 'C',1);
				$this->Cell($w_code, 8, $this->conv('商品コード'), 'T', 0, 'C',1);
				$this->Cell($w_item, 8, $this->conv('品目名'), 'T', 0, 'C',1);
				$this->Cell($w_quantity, 8, $this->conv('数量'), 'T', 0, 'C',1);
				$this->Cell($w_unit, 8, $this->conv('単価'), 'T', 0, 'C',1);
				$this->Cell($w_total, 8, $this->conv('合計'), 'T', 0, 'C',1);

				$y = 168;

				if($_param['Bill']['REDUCED_RATE_TOTAL']){
					$this->SetXY(27, $y - 8 );
					$this->SetFont(MINCHO,'',8);
					$str = "「※」は軽減税率対象であることを示します。";
					$str = $this->conv($str);
					$this->Write(5, $str);
					$y = $y + 2;
				}

				//備考欄
				$this->SetXY(28, $y - 5);
				$this->SetFont(MINCHO,'B',9);
				$str = "備考欄";
				$str = $this->conv($str);
				$this->Write(5, $str);

				$this->SetFont(MINCHO,'',9);
				$this->SetDrawColor($line_color['R'],$line_color['G'],$line_color['B']);
				$this->SetXY(28, $y);
				$str = $_param['Bill']['NOTE'];
				$str = $this->conv($str);
				if($tax_kind_count >= 1){
					$this->Cell( 130, 24, '', 1, 1, 'C');
				}else{
					$this->Cell( 172, 24, '', 1, 1, 'C');
				}
				$this->SetXY(28, $y+0.5);
				$this->SetFont(MINCHO,'',8);
				if($tax_kind_count >= 1){
					$this->MBMultiCell(130, 4, $str, 0, 'L');
				}else{
					$this->MBMultiCell(172, 4, $str, 0, 'L');
				}
				$this->SetFont(MINCHO,'B',8);

				if($tax_kind_count >= 1){
					$tax_kind_x = 158;
					$tax_kind_y = $y;
					$this->SetFont(MINCHO,'',7);
					$this->SetXY($tax_kind_x, $tax_kind_y);
					$this->Cell( 64, 24, '', 1, 1, 'C');

					$str = '・内訳';
					$str = $this->conv($str);
					$this->SetXY($tax_kind_x, $y);
					$this->Write(5, $str);
					if($_param['Bill']['TEN_RATE_TOTAL']){
						$tax_kind_y = $tax_kind_y + 4;
						$this->SetXY($tax_kind_x, $tax_kind_y);
						$str = '10%対象      '.number_format($_param['Bill']['TEN_RATE_TOTAL']);
						$str .= ' (消費税'.number_format($_param['Bill']['TEN_RATE_TAX']).')';
						$str = $this->conv($str);
						$this->Write(5, $str);
					}
					if($_param['Bill']['REDUCED_RATE_TOTAL']){
						$tax_kind_y = $tax_kind_y + 4;
						$this->SetXY($tax_kind_x, $tax_kind_y);
						$str = '8%(軽減)対象 '.number_format($_param['Bill']['REDUCED_RATE_TOTAL']);
						$str .= ' (消費税'.number_format($_param['Bill']['REDUCED_RATE_TAX']).')';
						$str = $this->conv($str);
						$this->Write(5, $str);
					}
					if($_param['Bill']['EIGHT_RATE_TOTAL']){
						$tax_kind_y = $tax_kind_y + 4;
						$this->SetXY($tax_kind_x, $tax_kind_y);
						$str = '8%対象       '.number_format($_param['Bill']['EIGHT_RATE_TOTAL']);
						$str .= ' (消費税'.number_format($_param['Bill']['EIGHT_RATE_TAX']).')';
						$str = $this->conv($str);
						$this->Write(5, $str);
					}
					if($_param['Bill']['FIVE_RATE_TOTAL']){
						$tax_kind_y = $tax_kind_y + 4;
						$this->SetXY($tax_kind_x, $tax_kind_y);
						$str = '5%対象       '.number_format($_param['Bill']['FIVE_RATE_TOTAL']);
						$str .= ' (消費税'.number_format($_param['Bill']['FIVE_RATE_TAX']).')';
						$str = $this->conv($str);
						$this->Write(5, $str);
					}
					//小計などの下部の表示レイアウトの設定値
					$total_area_x = 222;
					$total_cell = 16;
					$price_cell = 31;
				}else{
					$total_area_x = 200;
					$total_cell = 28;
					$price_cell = 41;
				}

				$h = 8;
				if(!empty($_param['Bill']['DISCOUNT'])){
					$h = (double) 24 / 4;
					$this->SetXY($total_area_x, $y);
					$this->SetFont(MINCHO,'B',8);
					$this->SetFillColor($column_color['R'],$column_color['G'],$column_color['B']);
					$this->Cell($total_cell, $h, $this->conv('　割引'), '1', 0, 'L',1);
					$this->SetFont(MINCHO,'',8);
					$this->SetFillColor(0xFF, 0xFF, 0xFF);
					if($_param['Bill']['DISCOUNT_TYPE'] == 0 ){
						$str = $_param['Bill']['DISCOUNT'] ? number_format($_param['Bill']['DISCOUNT'] ) .'%': '0';
					}elseif($_param['Bill']['DISCOUNT_TYPE'] == 1 ){
						$str = $_param['Bill']['DISCOUNT'] ? '▲'.number_format($_param['Bill']['DISCOUNT'] ) : '0';
					}else{
						$str ='';
					}
					$str = $this->conv($str);
					$this->Cell($price_cell, $h, $str, 1, 0, 'R',1);
					$y = $y + $h;
				}
				$this->SetXY($total_area_x, $y);
				$this->SetFont(MINCHO,'B',8);
				$this->SetFillColor($column_color['R'],$column_color['G'],$column_color['B']);
				$this->Cell($total_cell, $h, $this->conv('　小計'), '1', 0, 'L',1);
				$this->SetFillColor(0xFF, 0xFF, 0xFF);
				$this->SetFont(MINCHO,'',8);
				$str = $_param['Bill']['SUBTOTAL'] ? number_format($_param['Bill']['SUBTOTAL']) : '0';
				$str = $this->conv($str);
				$this->Cell($price_cell, $h, $str, 1, 0, 'R',1);
				$y = $y + $h;

				$this->SetXY($total_area_x, $y);
				$this->SetFont(MINCHO,'B',8);
				$this->SetFillColor($column_color['R'],$column_color['G'],$column_color['B']);
				$this->Cell($total_cell, $h, $this->conv('　消費税'), '1', 0, 'L',1);
				$this->SetFillColor(0xFF, 0xFF, 0xFF);
				$this->SetFont(MINCHO,'',8);
				$str = $_param['Bill']['SALES_TAX'] ? number_format($_param['Bill']['SALES_TAX']) : '0';
				$str = $this->conv($str);
				$this->Cell($price_cell, $h, $str, 1, 0, 'R',1);
				$y = $y + $h;

				$this->SetXY($total_area_x, $y);
				$this->SetFont(MINCHO,'B',8);
				$this->SetFillColor($column_color['R'],$column_color['G'],$column_color['B']);
				$this->Cell($total_cell, $h, $this->conv('　合計'), '1', 0, 'L',1);
				$this->SetFillColor(0xFF, 0xFF, 0xFF);
				$this->SetFont(MINCHO,'',8);
				$str = $_param['Bill']['TOTAL'] ? number_format($_param['Bill']['TOTAL']) : '0';
				$str = $this->conv($str);
				$this->Cell($price_cell, $h, $str, 1, 0, 'R',1);

				$y = $y + 8;

				//振込先
				$this->SetXY(28, $y);
				$this->SetFont(MINCHO,'B',9);
				$str = "振込先：";
				$acount = '';
				if(isset($_param['Company']['ACCOUNT_TYPE'])) $acount = $_accounttype[$_param['Company']['ACCOUNT_TYPE']];
				$str = "振込先：".$_param['Company']['ACCOUNT_HOLDER']."　".$_param['Company']['BANK_NAME'].
				"　".$_param['Company']['BANK_BRANCH']."　".$acount."　".$_param['Company']['ACCOUNT_NUMBER'];
				$str = $this->conv($str);
				$this->Cell( 172, 5, $str, 'B', 1, 'L');


			}

			//2ページ以上の途中のページ
			else {
				$page_num = 30;
				$this->SetXY(28, 7);
				$this->SetFont(MINCHO,'B',8);
				$this->SetDrawColor($line_color['R'],$line_color['G'],$line_color['B']);
				$this->SetFillColor($column_color['R'],$column_color['G'],$column_color['B']);
				$this->Cell($w_no, 8, $this->conv('No.'), 'T', 0, 'C',1);
				$this->Cell($w_code, 8, $this->conv('商品コード'), 'T', 0, 'C',1);
				$this->Cell($w_item, 8, $this->conv('品目名'), 'T', 0, 'C',1);
				$this->Cell($w_quantity, 8, $this->conv('数量'), 'T', 0, 'C',1);
				$this->Cell($w_unit, 8, $this->conv('単価'), 'T', 0, 'C',1);
				$this->Cell($w_total, 8, $this->conv('合計'), 'T', 0, 'C',1);
			}

			$this->SetFont(MINCHO,'',8);

			//各ページにアイテムの表を表示
			$max_item_per_page = 60;
			$fwrite_param = true;
			for($rows=0 ; $rows < $page_num ; ){
				$this->SetFont(MINCHO,'',9);
				if($j == 0){
					if($rows % 2 == 0) {
						$this->SetXY(28, 76 + $rows * 6 + 0.5);
						$this->SetFillColor(0xFF, 0xFF, 0xFF);
						$height = 5.5;
						$border = "";

					}else {
						$this->SetXY(28, 76 + $rows * 6);
						$this->SetFillColor($row_color['R'],$row_color['G'],$row_color['B']);
						$this->SetDrawColor($column_color['R'],$column_color['G'],$column_color['B']);
						$height = 6;
						$border = "TB";
					}

				}
				else{
					if($rows % 2 == 0) {
						$this->SetXY(28, 15 + $rows * 6 + 0.5);
						$this->SetFillColor(0xFF, 0xFF, 0xFF);
						$height = 5.5;
						$border = "";

					}else {
						$this->SetXY(28, 15 + $rows * 6);
						$this->SetFillColor($row_color['R'],$row_color['G'],$row_color['B']);
						$this->SetDrawColor($column_color['R'],$column_color['G'],$column_color['B']);
						$height = 6;
						$border = "TB";
					}


				}

				//改ページの場合はこれ以上現在のページに書き出さない.
				$fbreak = isset($_param[$i]['Billitem']['LINE_ATTRIBUTE'])
				&& intval($_param[$i]['Billitem']['LINE_ATTRIBUTE']) == 8;
				if ($fbreak) {
					$fwrite_param = false;
					$i++;
				}

				//No.
				$str = '';
				if ($fwrite_param) {
					$str = isset($_param[$i]['Billitem']['ITEM_NO'])?$_param[$i]['Billitem']['ITEM_NO']:'';
					$str = $this->conv($str);
				}
				$this->Cell($w_no, $height, $str, $border, 0, 'C',1);

				//商品コード
				$str = '';
				if ($fwrite_param) {
					$str = isset($_param[$i]['Billitem']['ITEM_CODE'])?$_param[$i]['Billitem']['ITEM_CODE']:'';
					$str = $this->conv($str);
				}
				$this->Cell($w_code, $height, $str, $border, 0, 'C',1);

				//品目名
				$str = '';
				if ($fwrite_param) {
					$str = isset($_param[$i]['Billitem']['ITEM'])?$_param[$i]['Billitem']['ITEM']:'';
					//軽減税率対象の商品の場合
					if($_param[$i]['Billitem']['TAX_CLASS'] == 91 || $_param[$i]['Billitem']['TAX_CLASS'] == 92){
						$str .= '(※)';
					}
					$str = $this->conv($str);
				}
				$this->Cell($w_item, $height, $str, $border, 0, 'L',1);

				//数量
				$str = '';
				if ($fwrite_param) {
					$str = isset($_param[$i]['Billitem']['QUANTITY']) && isset($_param[$i]['Billitem']['UNIT'])
					?$this->ht2br($_param[$i]['Billitem']['QUANTITY'],null,'QUANTITY').$_param[$i]['Billitem']['UNIT']:'';
					$str = $this->conv($str);
				}
				$this->Cell($w_quantity, $height, $str, $border, 0, 'C',1);

				//単価
				$str = '';
				if ($fwrite_param) {
					$str .= isset($_param[$i]['Billitem']['UNIT_PRICE']) ? $this->ht2br($_param[$i]['Billitem']['UNIT_PRICE'],null,'UNIT_PRICE'):'';
					$str = $this->conv($str);
				}
				$this->Cell($w_unit, $height, $str, $border, 0, 'R',1);


				//合計
				$str = '';
				if ($fwrite_param) {
					if(isset($_param[$i]['Billitem']['TAX_CLASS']) && $_param[$i]['Billitem']['TAX_CLASS']%10 == 1) $str = '(内)';
					if(isset($_param[$i]['Billitem']['TAX_CLASS']) && $_param[$i]['Billitem']['TAX_CLASS']%10 == 3) $str = '(非)';
					$str .= isset($_param[$i]['Billitem']['AMOUNT']) ? number_format($_param[$i]['Billitem']['AMOUNT']):'';
					$str = $this->conv($str);
				}
				$this->Cell($w_total, $height, $str, $border, 0, 'R',1);

				// アイテムのカウンタ
				if($fwrite_param) {
					$i++;
					$num_item_count++;
				}

				// 行のカウンタ
				$rows++;

				// ページの最後の6ページに含まれる改ページ数をカウントする(この数だけページが多くなっている).
				$break_count = 0;
				if(($j == 0 && $rows == 14) || ($j != 0 && $rows == 24)) {
					for ($k = 0; $k < ($rows+6); $k++) {
						$fbreak = isset($_param[$i+$k]) && isset($_param[$i+$k]['Billitem']['LINE_ATTRIBUTE'])
						&& intval($_param[$i+$k]['Billitem']['LINE_ATTRIBUTE']) == 8;
						if ($fbreak) {
							$break_count++;
						}
					}
				}

				// 最後のページの１つ前が、項目数が残り6以下の場合は次のページに送る.
				$remain_count = $max_item_count - $num_item_count;
				if(($_pages > 1) && ($j == $_pages - 2 - $break_count)
				&& (0 < $remain_count && $remain_count <= 6)
				&& (($j == 0 && $rows == 14) || ($j != 0 && $rows == 24))) {
					if ($j == 0) {
						$max_item_per_page = 14;
					} else {
						$max_item_per_page = 24;
					}
				}

				// 今のページに書き出した項目数が上限値に達したら、このページでの書き込みを停止する.
				if ($rows >= $max_item_per_page) {
					$fwrite_param = false;
				}

				if($j == 0){
					if($rows % 2 == 0) {
						$this->SetXY(28, 76 + $rows * 6 + 0.5);
						$this->SetFillColor(0xFF, 0xFF, 0xFF);
						$height = 5.5;
						$border = "";

					}else {
						$height = 6;
						$this->SetXY(28, 76 + $rows * 6);
						$this->SetFillColor($row_color['R'],$row_color['G'],$row_color['B']);
						$this->SetDrawColor($column_color['R'],$column_color['G'],$column_color['B']);
						$height = 6;
						$border = "TB";
					}
				}else {
					if($rows % 2 == 0) {
						$this->SetXY(28, 15 + $rows * 6 + 0.5);
						$this->SetFillColor(0xFF, 0xFF, 0xFF);
						$height = 5.5;
						$border = "";

					}else {
						$height = 6;
						$this->SetXY(28, 15 + $rows * 6);
						$this->SetFillColor($row_color['R'],$row_color['G'],$row_color['B']);
						$this->SetDrawColor($column_color['R'],$column_color['G'],$column_color['B']);
						$height = 6;
						$border = "TB";
					}
				}

				if(isset($_param[$i]['Billitem']['DISCOUNT']) && $_param[$i]['Billitem']['DISCOUNT']){

					//No.
					$str = '';
					$str = $this->conv($str);
					$this->Cell($w_no, $height, $str, $border, 0, 'C',1);

					//品目名
					$str = '割引';
					$str = $this->conv($str);
					$this->Cell($w_item, $height, $str, $border, 0, 'L',1);

					//数量
					$str = '';
					$str = $this->conv($str);
					$this->Cell($w_quantity, $height, $str, $border, 0, 'C',1);

					//単価
					//				$str = $_param[$i]['Billitem']['DISCOUNT'].($_param[$i]['Billitem']['DISCOUNT_TYPE']==1?'円':'％');
					$str = $_param[$i]['Billitem']['DISCOUNT_TYPE']==1?'':$_param[$i]['Billitem']['DISCOUNT'].'%';
					$str = $this->conv($str);
					$this->Cell($w_unit, $height, $str, $border, 0, 'R',1);

					//合計
					$str = '▲'.number_format($_param[$i]['Billitem']['DISCOUNT_TYPE']==0?$_param[$i]['Billitem']['AMOUNT']*($_param[$i]['Billitem']['DISCOUNT']*0.01):$_param[$i]['Billitem']['DISCOUNT']);
					$str = $this->conv($str);
					$this->Cell($w_total, $height, $str, $border, 0, 'R',1);
					$amount-=($_param[$i]['Billitem']['DISCOUNT_TYPE']==0?$_param[$i]['Billitem']['AMOUNT']*($_param[$i]['Billitem']['DISCOUNT']*0.01):$_param[$i]['Billitem']['DISCOUNT']);
					$item_count++;
					$rows++;
				}
				$item_count++;
			}
/*
			if($_pages == 1 && $j == 0){
				$this->SetDrawColor($line_color['R'],$line_color['G'],$line_color['B']);
				$this->Line(38, 70, 38, 160);
				$this->Line(158, 70, 158, 160);
				$this->Line(193, 70, 193, 160);
				$this->Line(228, 70, 228, 160);
			}else if($_pages > 1 && $j == 0){
				$this->SetDrawColor($line_color['R'],$line_color['G'],$line_color['B']);
				$this->Line(38, 70, 38, 196);
				$this->Line(158, 70, 158, 196);
				$this->Line(193, 70, 193, 196);
				$this->Line(228, 70, 228, 196);

			}else if($_pages > 1 && $j == $_pages - 1){
				$this->SetDrawColor($line_color['R'],$line_color['G'],$line_color['B']);
				$this->Line(38, 7, 38, 159);
				$this->Line(158, 7, 158, 159);
				$this->Line(193, 7, 193, 159);
				$this->Line(228, 7, 228, 159);

			}else {
				$this->SetDrawColor($line_color['R'],$line_color['G'],$line_color['B']);
				$this->Line(38, 7, 38, 195);
				$this->Line(158, 7, 158, 195);
				$this->Line(193, 7, 193, 195);
				$this->Line(228, 7, 228, 195);

			}

*/

			if($_pages == 1 && $j == 0){
				$this->SetDrawColor($line_color['R'],$line_color['G'],$line_color['B']);
				$this->Line(28 + $w_no, 70, 28 + $w_no, 160);
				$this->Line(28 + $w_no + $w_code, 70, 28 + $w_no + $w_code, 160);
				$this->Line(28 + $w_no + $w_code + $w_item, 70, 28 + $w_no + $w_code + $w_item, 160);
				$this->Line(28 + $w_no + $w_code + $w_item + $w_quantity, 70, 28 + $w_no + $w_code + $w_item + $w_quantity, 160);
				$this->Line(28 + $w_no + $w_code + $w_item + $w_quantity + $w_unit, 70, 28 + $w_no + $w_code + $w_item + $w_quantity + $w_unit, 160);
			}else if($_pages > 1 && $j == 0){
				$this->SetDrawColor($line_color['R'],$line_color['G'],$line_color['B']);
				$this->Line(28 + $w_no, 70, 28 + $w_no, 196);
				$this->Line(28 + $w_no + $w_code, 70, 28 + $w_no + $w_code, 196);
				$this->Line(28 + $w_no + $w_code + $w_item, 70, 28 + $w_no + $w_code + $w_item, 196);
				$this->Line(28 + $w_no + $w_code + $w_item + $w_quantity, 70, 28 + $w_no + $w_code + $w_item + $w_quantity, 196);
				$this->Line(28 + $w_no + $w_code + $w_item + $w_quantity + $w_unit, 70, 28 + $w_no + $w_code + $w_item + $w_quantity + $w_unit, 196);
			}else if($_pages > 1 && $j == $_pages - 1){
				$this->SetDrawColor($line_color['R'],$line_color['G'],$line_color['B']);
				$this->Line(28 + $w_no, 7, 28 + $w_no, 159);
				$this->Line(28 + $w_no + $w_code, 7, 28 + $w_no + $w_code, 159);
				$this->Line(28 + $w_no + $w_code + $w_item, 7, 28 + $w_no + $w_code + $w_item, 159);
				$this->Line(28 + $w_no + $w_code + $w_item + $w_quantity, 7, 28 + $w_no + $w_code + $w_item + $w_quantity, 159);
				$this->Line(28 + $w_no + $w_code + $w_item + $w_quantity + $w_unit, 7, 28 + $w_no + $w_code + $w_item + $w_quantity + $w_unit, 159);
			}else {
				$this->SetDrawColor($line_color['R'],$line_color['G'],$line_color['B']);
				$this->Line(28 + $w_no, 7, 28 + $w_no, 195);
				$this->Line(28 + $w_no + $w_code, 7, 28 + $w_no + $w_code, 195);
				$this->Line(28 + $w_no + $w_code + $w_item, 7, 28 + $w_no + $w_code + $w_item, 195);
				$this->Line(28 + $w_no + $w_code + $w_item + $w_quantity, 7, 28 + $w_no + $w_code + $w_item + $w_quantity, 195);
				$this->Line(28 + $w_no + $w_code + $w_item + $w_quantity + $w_unit, 7, 28 + $w_no + $w_code + $w_item + $w_quantity + $w_unit, 195);
			}


			if($j != $_pages - 1) $this->AddPage('L');


		}

	}

			//フッター
	function Footer()
	{
		if($this->Direction==0){
			$this->setXY(100,293);
		}
		else if($this->cover){
			$this->setXY(100,293);
		}
		else{
			$this->setXY(140,202);
		}
		$Npage = $this->PageNo();
		if($this->PageNo()>$this->Total_Page){
			$Npage=$Npage-$this->Total_Page;
		}
		$this->SetFont(MINCHO,'',9);
		$str = $Npage.'/'.$this->Total_Page;
		$str = mb_convert_encoding($str, "SJIS");
		$this->Write(3,$str);
	}
}
?>