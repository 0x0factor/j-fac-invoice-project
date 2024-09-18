<?php
/**
 * @copyright ICZ Corporation (http://www.icz.co.jp/)
 * @license See the LICENCE file
 * @author <matcha@icz.co.jp>
 * @version $Id$
 */
require('simple_coverpage.php');

class QUOTEPDF extends SIMPLE_COVERPAGEPDF
{

	//ヘッダ
  function Header()
    {
	}

	function main($_param, $_county,$_direction,$_items,$_pages){
		//社判
		if($_param['Company']['SEAL'] && $_param['Quote']['CMP_SEAL_FLG']){
			$this->Image($_param['Company']['SEAL_IMAGE'],160, 40, 25,25,'PNG');
		}

		//社員判
		if($_param['Quote']['CHR_ID'] && $_param['Charge']['SEAL'] && $_param['Quote']['CHR_SEAL_FLG']){
			$this->Image($_param['Charge']['SEAL_IMAGE'],171, 67, 18,18);
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
		$this->SetXY(18, 17);

		$str = "御 見 積 書";
		$str = $this->conv($str);
		$this->SetFillColor($column_color['R'],$column_color['G'],$column_color['B']);
		$this->Cell( 172, 10, $str, 1, 1, 'C',1);
		$this->SetTextColor(0);

		//No.
		$this->SetXY(152, 29);
		$this->SetFont(MINCHO,'',9);
		$str = 'No.';
		$str = $this->conv($str);
		$this->Write(5, $str);

		//見積書番号
		$this->SetXY(168, 29);
		$this->SetFont(MINCHO,'',9);
		$str = $_param['Quote']['NO'];
		$str = $this->conv($str);
		$this->Cell( 23, 5, $str, 0, 1, 'R');

		//日付
		$this->SetXY(170,33);
		$this->SetFont(MINCHO,'',9);
		$str = substr($_param['Quote']['ISSUE_DATE'],0,4)."年".substr($_param['Quote']['ISSUE_DATE'],5,2)."月".substr($_param['Quote']['ISSUE_DATE'],8,2)."日";
		$str = $this->conv($str);
		$this->Cell( 21, 5, $str, 0, 1, 'R');

			//部署・顧客担当者名
		if(isset($_param['CustomerCharge']['UNIT'])&&isset($_param['CustomerCharge']['CHARGE_NAME'])) {
			if(mb_strlen($_param['Customer']['NAME']) > mb_strlen($_param['CustomerCharge']['UNIT']."　".$_param['CustomerCharge']['CHARGE_NAME'])) {
				$font_size = $this->customer_font(mb_strlen($_param['Customer']['NAME']));
			}else {
				$font_size = $this->customer_font(mb_strlen($_param['CustomerCharge']['UNIT']."　".$_param['CustomerCharge']['CHARGE_NAME']));
			}

			//顧客名
			$this->SetXY(18, 30);
			$this->SetFont(MINCHO,'',$font_size);
			$str = $_param['Customer']['NAME'];
			$str = $this->conv($str);
			$this->Cell( 105, 4, $str, 'B');

			//部署
			$this->SetXY(18, 36);
			$this->SetFont(MINCHO,'',$font_size);

			$str = "";
			if($_param['CustomerCharge']['UNIT']) {
				$str .= $_param['CustomerCharge']['UNIT']."　";
			}
			$str .=$_param['CustomerCharge']['CHARGE_NAME'];

			switch($_param['Quote']['HONOR_CODE'] ) {
				case 0:
					$str .= '　　御中';
					break;

				case 1:
					$str .= '　　様';
					break;

				case 2:
					$str .= '　　'.$_param['Quote']['HONOR_TITLE'];
					break;
			}

			$str = $this->conv($str);
			$this->Cell( 105, 4, $str, 'B');
		}

		else{


			$this->SetXY(18, 36);
			$this->SetFont(MINCHO,'',$this->customer_font(mb_strlen($_param['Customer']['NAME'])));
			$str = $_param['Customer']['NAME'];

			switch($_param['Quote']['HONOR_CODE'] ) {
				case 0:
					$str .= '　　御中';
					break;

				case 1:
					$str .= '　　様';
					break;

				case 2:
					$str .= '　　'.$_param['Quote']['HONOR_TITLE'];
					break;
			}


			$str = $this->conv($str);
			$this->Cell( 105, 4, $str, 'B');
		}


		//件名
		$this->SetXY(18, 47.5);
		$this->SetFont(MINCHO,'',10);
		$this->SetDrawColor($column_color['R'],$column_color['G'],$column_color['B']);
		$str = "件名:".$_param['Quote']['SUBJECT'];
		$str = $this->conv($str);
		$this->Cell( 90, 4.5, $str, 'B', 1, 'L');

		$temp_y = 55.5;
		if(!empty($_param['Quote']['DEADLINE'])){
			//納入期限
			$this->SetXY(18, $temp_y);
			$this->SetFont(MINCHO,'',9);
			$str = "納入期限:".$_param['Quote']['DEADLINE'];
			$str = $this->conv($str);
			$this->Cell( 80, 4.5, $str, 'B', 1, 'L');
			$temp_y = $temp_y + 6;
		}
		if(!empty($_param['Quote']['DEAL'])){
			//取引方法
			$this->SetXY(18, $temp_y);
			$this->SetFont(MINCHO,'',9);
			$str = "取引方法:".$_param['Quote']['DEAL'];
			$str = $this->conv($str);
			$this->Cell( 80, 4.5, $str, 'B', 1, 'L');
			$temp_y = $temp_y + 6;
		}
		if(!empty($_param['Quote']['DELIVERY'])){
			//納入場所
			$this->SetXY(18, $temp_y);
			$this->SetFont(MINCHO,'',9);
			$str = "納入場所:".$_param['Quote']['DELIVERY'];
			$str = $this->conv($str);
			$this->Cell( 80, 4.5, $str, 'B', 1, 'L');
			$temp_y = $temp_y + 6;
		}
		if(!empty($_param['Quote']['DUE_DATE'])){
			//有効期限
			$this->SetXY(18, $temp_y);
			$this->SetFont(MINCHO,'',9);
			$str = "有効期限:".$_param['Quote']['DUE_DATE'];
			$str = $this->conv($str);
			$this->Cell( 80, 4.5, $str, 'B', 1, 'L');
		}


		$this->SetDrawColor($line_color['R'],$line_color['G'],$line_color['B']);


		$this->writeCompanyInfo($_param, $_county);


		//印鑑枠
		$this->SetXY(130, 66);
		$this->Cell(20, 20, '', 1, 0, 'C');
		$this->Cell(20, 20, '', 1, 0, 'C');
		$this->Cell(20, 20, '', 1, 0, 'C');



		//合計金額
		$this->SetXY(18, 80);
		$this->SetLineWidth(0.4);
		$this->SetFont(MINCHO,'B',13);
		$str = "合計金額";
		$str = $this->conv($str);
		$this->Cell( 100, 7, $str, 'B', 1, 'L');
		$this->Line(18, 86, 118, 86);
		$this->SetLineWidth(0.2);

		$this->SetXY(18, 80);
		$str = '\\'.number_format($_param['Quote']['TOTAL']).'-';
		$str = $this->conv($str);
		$this->Cell( 100, 7, $str, 0, 1, 'R');
		//単位・円
		$this->SetXY(175, 87);
		$this->SetFont(MINCHO,'',10);
		$str = "単位：円";
		$str = $this->conv($str);
		$this->Write(5, $str);

		//表の幅
		$w_no		=  8;
		$w_code		= 15;
		$w_item		= 73;
		$w_quantity	= 23;
		$w_unit		= 23;
		$w_total	= 30;

		// 項目数(改ページなどの非項目を含まない数)
		$num_item_count = 0;
		$max_item_count = 0;
		for($i = 0; isset($_param[$i]); $i++) {
			$fbreak = isset($_param[$i]['Quoteitem']['LINE_ATTRIBUTE'])
				&& intval($_param[$i]['Quoteitem']['LINE_ATTRIBUTE']) == 8; // 改ページ
			if (!$fbreak) {
				$max_item_count++;
			}
		}

    //複数あるか税率があるかを確認
		$tax_kinds = array('TEN_RATE_TOTAL','REDUCED_RATE_TOTAL','EIGHT_RATE_TOTAL','FIVE_RATE_TOTAL');
		$tax_kind_count = 0;
		foreach($tax_kinds as $key){
			if($_param['Quote'][$key]){
				$tax_kind_count++;
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
				$page_num = 20;
				$this->SetXY(18, 92);
				$this->SetFont(MINCHO,'B',8);
				$this->SetDrawColor($line_color['R'],$line_color['G'],$line_color['B']);
				$this->SetFillColor($column_color['R'],$column_color['G'],$column_color['B']);
				$this->Cell($w_no, 8, $this->conv('No.'), 'T', 0, 'C',1);
				$this->Cell($w_code, 8, $this->conv('商品コード'), 'T', 0, 'C',1);
				$this->Cell($w_item, 8, $this->conv('品目名'), 'T', 0, 'C',1);
				$this->Cell($w_quantity, 8, $this->conv('数量'), 'T', 0, 'C',1);
				$this->Cell($w_unit, 8, $this->conv('単価'), 'T', 0, 'C',1);
				$this->Cell($w_total, 8, $this->conv('合計'), 'T', 0, 'C',1);
				$this->SetFont(MINCHO,'',8);
				$i = 0;

				$y = 222;

				if($_param['Quote']['DISCOUNT_TYPE'] != 2){
          if($_param['Quote']['REDUCED_RATE_TOTAL']){
						$this->SetXY(17, $y);
						$this->SetFont(MINCHO,'',8);
						$str = "「※」は軽減税率対象であることを示します。";
						$str = $this->conv($str);
						$this->Write(5, $str);
					}
					$this->SetXY(123, $y);
					$this->SetFont(MINCHO,'B',8);
					$this->SetFillColor($column_color['R'],$column_color['G'],$column_color['B']);
					$this->Cell(28, 6, $this->conv('　割引'), '1', 0, 'L',1);
					$this->SetFont(MINCHO,'',8);
					$this->SetFillColor(0xFF, 0xFF, 0xFF);
					if($_param['Quote']['DISCOUNT_TYPE'] == 0 ){
						$str = $_param['Quote']['DISCOUNT'] ? number_format($_param['Quote']['DISCOUNT'] ) .'%': '0';
					}elseif($_param['Quote']['DISCOUNT_TYPE'] == 1 ){
						$str = $_param['Quote']['DISCOUNT'] ? '▲'.number_format($_param['Quote']['DISCOUNT'] ) : '0';
					}else{
						$str ='';
					}

					$str = $this->conv($str);
					$this->Cell(39, 6, $str, 1, 0, 'R',1);
					$y = $y + 6;
        }elseif($_param['Quote']['REDUCED_RATE_TOTAL']){
					$this->SetXY(17, $y);
					$this->SetFont(MINCHO,'',8);
					$str = "「※」は軽減税率対象であることを示します。";
					$str = $this->conv($str);
					$this->Write(5, $str);
					$y = $y + 6;
				}

        if($tax_kind_count >= 1){
					$tax_kind_x = 18;
					$tax_kind_y = $y;
					$this->SetXY($tax_kind_x, $y);
					$this->Cell( 105, 18, '', 1, 1, 'C');
					$this->SetXY($tax_kind_x, $y);
					$this->SetFont(MINCHO,'',7);
					$str = '・内訳';
					$str = $this->conv($str);
					$this->SetXY($tax_kind_x, $y);
					$this->Write(5, $str);
					if($_param['Quote']['TEN_RATE_TOTAL']){
						$tax_kind_y = $tax_kind_y + 4;
						$this->SetXY($tax_kind_x, $tax_kind_y);
						$str = '10%対象      '.number_format($_param['Quote']['TEN_RATE_TOTAL']);
						$str .= ' (消費税'.number_format($_param['Quote']['TEN_RATE_TAX']).')';
						$str = $this->conv($str);
						$this->Write(5, $str);
					}
					if($_param['Quote']['REDUCED_RATE_TOTAL']){
						$tax_kind_y = $tax_kind_y + 3;
						$this->SetXY($tax_kind_x, $tax_kind_y);
						$str = '8%(軽減)対象 '.number_format($_param['Quote']['REDUCED_RATE_TOTAL']);
						$str .= ' (消費税'.number_format($_param['Quote']['REDUCED_RATE_TAX']).')';
						$str = $this->conv($str);
						$this->Write(5, $str);
					}
					if($_param['Quote']['EIGHT_RATE_TOTAL']){
						$tax_kind_y = $tax_kind_y + 3;
						$this->SetXY($tax_kind_x, $tax_kind_y);
						$str = '8%対象       '.number_format($_param['Quote']['EIGHT_RATE_TOTAL']);
						$str .= ' (消費税'.number_format($_param['Quote']['EIGHT_RATE_TAX']).')';
						$str = $this->conv($str);
						$this->Write(5, $str);
					}
					if($_param['Quote']['FIVE_RATE_TOTAL']){
						$tax_kind_y = $tax_kind_y + 3;
						$this->SetXY($tax_kind_x, $tax_kind_y);
						$str = '5%対象       '.number_format($_param['Quote']['FIVE_RATE_TOTAL']);
						$str .= ' (消費税'.number_format($_param['Quote']['FIVE_RATE_TAX']).')';
						$str = $this->conv($str);
						$this->Write(5, $str);
					}
				}

				$this->SetXY(123, $y);
				$this->SetFont(MINCHO,'B',8);
				$this->SetFillColor($column_color['R'],$column_color['G'],$column_color['B']);
				$this->Cell(28, 6, $this->conv('　小計'), '1', 0, 'L',1);
				$this->SetFillColor(0xFF, 0xFF, 0xFF);
				$this->SetFont(MINCHO,'',8);
				$str = $_param['Quote']['SUBTOTAL'] ? number_format($_param['Quote']['SUBTOTAL']) : '0';
				$str = $this->conv($str);
				$this->Cell(39, 6, $str, 1, 0, 'R',1);
				$y = $y + 6;

				$this->SetXY(123, $y);
				$this->SetFont(MINCHO,'B',8);
				$this->SetFillColor($column_color['R'],$column_color['G'],$column_color['B']);
				$this->Cell(28, 6, $this->conv('　消費税'), '1', 0, 'L',1);
				$this->SetFillColor(0xFF, 0xFF, 0xFF);
				$this->SetFont(MINCHO,'',8);
				$str = $_param['Quote']['SALES_TAX'] ? number_format($_param['Quote']['SALES_TAX']) : '0';
				$str = $this->conv($str);
				$this->Cell(39, 6, $str, 1, 0, 'R',1);
				$y = $y + 6;

				$this->SetXY(123, $y);
				$this->SetFont(MINCHO,'B',8);
				$this->SetFillColor($column_color['R'],$column_color['G'],$column_color['B']);
				$this->Cell(28, 6, $this->conv('　合計'), '1', 0, 'L',1);
				$this->SetFillColor(0xFF, 0xFF, 0xFF);
				$this->SetFont(MINCHO,'',8);
				$str = $_param['Quote']['TOTAL'] ? number_format($_param['Quote']['TOTAL']) : '0';
				$str = $this->conv($str);
				$this->Cell(39, 6, $str, 1, 0, 'R',1);
				$y = $y + 8;

				//備考欄
				$this->SetXY(18, $y);
				$this->SetFont(MINCHO,'B',9);
				$str = "備考欄";
				$str = $this->conv($str);
				$this->Write(5, $str);
				$y = $y + 5;


				$this->SetFont(MINCHO,'',9);
				$this->SetDrawColor($line_color['R'],$line_color['G'],$line_color['B']);
				$this->SetXY(18, $y);
				$str = $_param['Quote']['NOTE'];
				$str = $this->conv($str);
				$this->Cell( 172, 25, '', 1, 1, 'C');
				$this->SetXY(18, $y+1);
				$this->MBMultiCell(172, 4, $str, 0, 'L');
				$this->SetFont(MINCHO,'B',8);

			}

			//2ページ以上の1ページ目
			elseif($_pages > 1 && $j == 0){
				$this->SetXY(18, 92);
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
				$page_num = 30;
			}

			//2ページ以上の最後のページ
			else if($_pages > 1 && $j == $_pages - 1){
				$page_num = 30;
				$this->SetXY(18, 17);
				$this->SetFont(MINCHO,'B',8);
				$this->SetDrawColor($line_color['R'],$line_color['G'],$line_color['B']);
				$this->SetFillColor($column_color['R'],$column_color['G'],$column_color['B']);
				$this->Cell($w_no, 8, $this->conv('No.'), 'T', 0, 'C',1);
				$this->Cell($w_code, 8, $this->conv('商品コード'), 'T', 0, 'C',1);
				$this->Cell($w_item, 8, $this->conv('品目名'), 'T', 0, 'C',1);
				$this->Cell($w_quantity, 8, $this->conv('数量'), 'T', 0, 'C',1);
				$this->Cell($w_unit, 8, $this->conv('単価'), 'T', 0, 'C',1);
				$this->Cell($w_total, 8, $this->conv('合計'), 'T', 0, 'C',1);


				$y = 208;

				if($_param['Quote']['DISCOUNT_TYPE'] != 2){
          if($_param['Quote']['REDUCED_RATE_TOTAL']){
						$this->SetXY(17, $y);
						$this->SetFont(MINCHO,'',8);
						$str = "「※」は軽減税率対象であることを示します。";
						$str = $this->conv($str);
						$this->Write(5, $str);
					}
					$this->SetXY(123, $y);
					$this->SetFont(MINCHO,'B',8);
					$this->SetFillColor($column_color['R'],$column_color['G'],$column_color['B']);
					$this->Cell(28, 6, $this->conv('　割引'), '1', 0, 'L',1);
					$this->SetFont(MINCHO,'',8);
					$this->SetFillColor(0xFF, 0xFF, 0xFF);

					if($_param['Quote']['DISCOUNT_TYPE'] == 0 ){
						$str = $_param['Quote']['DISCOUNT'] ? number_format($_param['Quote']['DISCOUNT'] ) .'%': '0';
					}elseif($_param['Quote']['DISCOUNT_TYPE'] == 1 ){
						$str = $_param['Quote']['DISCOUNT'] ? '▲'.number_format($_param['Quote']['DISCOUNT'] ) : '0';
					}else{
						$str ='';
					}

					$str = $this->conv($str);
					$this->Cell(39, 6, $str, 1, 0, 'R',1);
					$y = $y + 6;
        }elseif($_param['Quote']['REDUCED_RATE_TOTAL']){
					$this->SetXY(17, $y);
					$this->SetFont(MINCHO,'',8);
					$str = "「※」は軽減税率対象であることを示します。";
					$str = $this->conv($str);
					$this->Write(5, $str);
					$y = $y + 6;
				}

        if($tax_kind_count >= 1){
					$tax_kind_x = 18;
					$tax_kind_y = $y;
					$this->SetXY($tax_kind_x, $y);
					$this->Cell( 105, 18, '', 1, 1, 'C');
					$this->SetXY($tax_kind_x, $y);
					$this->SetFont(MINCHO,'',7);
					$str = '・内訳';
					$str = $this->conv($str);
					$this->SetXY($tax_kind_x, $y);
					$this->Write(5, $str);
					if($_param['Quote']['TEN_RATE_TOTAL']){
						$tax_kind_y = $tax_kind_y + 4;
						$this->SetXY($tax_kind_x, $tax_kind_y);
						$str = '10%対象      '.number_format($_param['Quote']['TEN_RATE_TOTAL']);
						$str .= ' (消費税'.number_format($_param['Quote']['TEN_RATE_TAX']).')';
						$str = $this->conv($str);
						$this->Write(5, $str);
					}
					if($_param['Quote']['REDUCED_RATE_TOTAL']){
						$tax_kind_y = $tax_kind_y + 3;
						$this->SetXY($tax_kind_x, $tax_kind_y);
						$str = '8%(軽減)対象 '.number_format($_param['Quote']['REDUCED_RATE_TOTAL']);
						$str .= ' (消費税'.number_format($_param['Quote']['REDUCED_RATE_TAX']).')';
						$str = $this->conv($str);
						$this->Write(5, $str);
					}
					if($_param['Quote']['EIGHT_RATE_TOTAL']){
						$tax_kind_y = $tax_kind_y + 3;
						$this->SetXY($tax_kind_x, $tax_kind_y);
						$str = '8%対象       '.number_format($_param['Quote']['EIGHT_RATE_TOTAL']);
						$str .= ' (消費税'.number_format($_param['Quote']['EIGHT_RATE_TAX']).')';
						$str = $this->conv($str);
						$this->Write(5, $str);
					}
					if($_param['Quote']['FIVE_RATE_TOTAL']){
						$tax_kind_y = $tax_kind_y + 3;
						$this->SetXY($tax_kind_x, $tax_kind_y);
						$str = '5%対象       '.number_format($_param['Quote']['FIVE_RATE_TOTAL']);
						$str .= ' (消費税'.number_format($_param['Quote']['FIVE_RATE_TAX']).')';
						$str = $this->conv($str);
						$this->Write(5, $str);
					}
				}

				$this->SetXY(123, $y);
				$this->SetFont(MINCHO,'B',8);
				$this->SetFillColor($column_color['R'],$column_color['G'],$column_color['B']);
				$this->Cell(28, 6, $this->conv('　小計'), '1', 0, 'L',1);
				$this->SetFillColor(0xFF, 0xFF, 0xFF);
				$this->SetFont(MINCHO,'',8);
				$str = $_param['Quote']['SUBTOTAL'] ? number_format($_param['Quote']['SUBTOTAL']) : '0';
				$str = $this->conv($str);
				$this->Cell(39, 6, $str, 1, 0, 'R',1);
				$y = $y + 6;

				$this->SetXY(123, $y);
				$this->SetFont(MINCHO,'B',8);
				$this->SetFillColor($column_color['R'],$column_color['G'],$column_color['B']);
				$this->Cell(28, 6, $this->conv('　消費税'), '1', 0, 'L',1);
				$this->SetFillColor(0xFF, 0xFF, 0xFF);
				$this->SetFont(MINCHO,'',8);
				$str = $_param['Quote']['SALES_TAX'] ? number_format($_param['Quote']['SALES_TAX']) : '0';
				$str = $this->conv($str);
				$this->Cell(39, 6, $str, 1, 0, 'R',1);
				$y = $y + 6;

				$this->SetXY(123, $y);
				$this->SetFont(MINCHO,'B',8);
				$this->SetFillColor($column_color['R'],$column_color['G'],$column_color['B']);
				$this->Cell(28, 6, $this->conv('　合計'), '1', 0, 'L',1);
				$this->SetFillColor(0xFF, 0xFF, 0xFF);
				$this->SetFont(MINCHO,'',8);
				$str = $_param['Quote']['TOTAL'] ? number_format($_param['Quote']['TOTAL']) : '0';
				$str = $this->conv($str);
				$this->Cell(39, 6, $str, 1, 0, 'R',1);
				$y = $y + 8;

				//備考欄
				$this->SetXY(18, $y);
				$this->SetFont(MINCHO,'B',9);
				$str = "備考欄";
				$str = $this->conv($str);
				$this->Write(5, $str);
				$y = $y + 5;


				$this->SetFont(MINCHO,'',9);
				$this->SetDrawColor($line_color['R'],$line_color['G'],$line_color['B']);
				$this->SetXY(18, $y);
				$str = $_param['Quote']['NOTE'];
				$str = $this->conv($str);
				$this->Cell( 172, 25, '', 1, 1, 'C');
				$this->SetXY(18, $y+1);
				$this->MBMultiCell(172, 4, $str, 0, 'L');


			}

			//2ページ以上の途中のページ
			else {
				$page_num = 40;
				$this->SetXY(18, 17);
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

			$max_item_per_page = 60;
			$fwrite_param = true;
			for($rows=0 ; $rows < $page_num ; ){

				if($j == 0){
					if($rows % 2 == 0) {
						$this->SetXY(18, 99 + $rows * 6 + 0.5);
						$this->SetFillColor(0xFF, 0xFF, 0xFF);
						$height = 5.5;
						$border = "";

					}else {
						$this->SetXY(18, 99 + $rows * 6);
						$this->SetFillColor($row_color['R'],$row_color['G'],$row_color['B']);
						$this->SetDrawColor($column_color['R'],$column_color['G'],$column_color['B']);
						$height = 6;
						$border = "TB";
					}

				}
				else{
					if($rows % 2 == 0) {
						$this->SetXY(18, 25 + $rows * 6 + 0.5);
						$this->SetFillColor(0xFF, 0xFF, 0xFF);
						$height = 5.5;
						$border = "";

					}else {
						$this->SetXY(18, 25 + $rows * 6);
						$this->SetFillColor($row_color['R'],$row_color['G'],$row_color['B']);
						$this->SetDrawColor($column_color['R'],$column_color['G'],$column_color['B']);
						$height = 6;
						$border = "TB";
					}


				}

				//改ページの場合はこれ以上現在のページに書き出さない.
				$fbreak = isset($_param[$i]['Quoteitem']['LINE_ATTRIBUTE'])
					&& intval($_param[$i]['Quoteitem']['LINE_ATTRIBUTE']) == 8;
				if ($fbreak) {
					$fwrite_param = false;
					$i++;
				}

				//No.
				$str = '';
				if ($fwrite_param) {
					$str = isset($_param[$i]['Quoteitem']['ITEM_NO'])?$_param[$i]['Quoteitem']['ITEM_NO']:'';
					$str = $this->conv($str);
				}
				$this->Cell($w_no, $height, $str, $border, 0, 'C',1);

				//商品コード
				$str = '';
				if ($fwrite_param) {
					$str = isset($_param[$i]['Quoteitem']['ITEM_CODE'])?$_param[$i]['Quoteitem']['ITEM_CODE']:'';
					$str = $this->conv($str);
				}
				$this->Cell($w_code, $height, $str, $border, 0, 'C',1);

				//品目名
				$str = '';
				if ($fwrite_param) {
					$str = isset($_param[$i]['Quoteitem']['ITEM'])?$_param[$i]['Quoteitem']['ITEM']:'';
          //軽減税率対象の商品の場合
          if($_param[$i]['Quoteitem']['TAX_CLASS'] == 91 || $_param[$i]['Quoteitem']['TAX_CLASS'] == 92){
            $str .= '(※)';
          }
          $str = $this->conv($str);
				}
				$this->Cell($w_item, $height, $str, $border, 0, 'L',1);

				//数量
				$str = '';
				if ($fwrite_param) {
					$str = isset($_param[$i]['Quoteitem']['QUANTITY']) && isset($_param[$i]['Quoteitem']['UNIT'])
					       ?$this->ht2br($_param[$i]['Quoteitem']['QUANTITY'],null,'QUANTITY').$_param[$i]['Quoteitem']['UNIT']:'';
					$str = $this->conv($str);
				}
				$this->Cell($w_quantity, $height, $str, $border, 0, 'C',1);

				//単価
				$str = '';
				if ($fwrite_param) {
					$str .= isset($_param[$i]['Quoteitem']['UNIT_PRICE']) ? $this->ht2br($_param[$i]['Quoteitem']['UNIT_PRICE'],null,'UNIT_PRICE'):'';
					$str = $this->conv($str);
				}
				$this->Cell($w_unit, $height, $str, $border, 0, 'R',1);

				//合計
				$str = '';
				if ($fwrite_param) {
          if(isset($_param[$i]['Quoteitem']['TAX_CLASS']) && $_param[$i]['Quoteitem']['TAX_CLASS']%10 == 1) $str = '(内)';
					if(isset($_param[$i]['Quoteitem']['TAX_CLASS']) && $_param[$i]['Quoteitem']['TAX_CLASS']%10 == 3) $str = '(非)';
					$str .= isset($_param[$i]['Quoteitem']['AMOUNT']) ? number_format($_param[$i]['Quoteitem']['AMOUNT']):'';
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

				// ページの最後の10ページに含まれる改ページ数をカウントする(この数だけページが多くなっている).
				$break_count = 0;
				if(($j == 0 && $rows == 20) || ($j != 0 && $rows == 30)) {
					for ($k = 0; $k < ($rows+10); $k++) {
						$fbreak = isset($_param[$i+$k]) && isset($_param[$i+$k]['Quoteitem']['LINE_ATTRIBUTE'])
						&& intval($_param[$i+$k]['Quoteitem']['LINE_ATTRIBUTE']) == 8;
						if ($fbreak) {
							$break_count++;
						}
					}
				}

				// 最後のページの１つ前(-break_count)が、項目数が残り10以下の場合は次のページに送る.
				$remain_count = $max_item_count - $num_item_count;
				if(($_pages > 1) && ($j == $_pages - 2 - $break_count)
				&& (0 < $remain_count && $remain_count <= 10)
				&& (($j == 0 && $rows == 20) || ($j != 0 && $rows == 30))) {
					if ($j == 0) {
						$max_item_per_page = 20;
					} else {
						$max_item_per_page = 30;
					}
				}
				if ($rows >= $max_item_per_page) {
					$fwrite_param = false;
				}

				if($j == 0){
					if($rows % 2 == 0) {
						$this->SetXY(18, 99 + $rows * 6 + 0.5);
						$this->SetFillColor(0xFF, 0xFF, 0xFF);
						$height = 5.5;
						$border = "";
					}else {
						$this->SetXY(18, 99 + $rows * 6);
						$this->SetFillColor($row_color['R'],$row_color['G'],$row_color['B']);
						$this->SetDrawColor($column_color['R'],$column_color['G'],$column_color['B']);
						$height = 6;
						$border = "TB";
					}
				}

				else{
					if($rows % 2 == 0) {
						$this->SetXY(18, 25 + $rows * 6 + 0.5);
						$this->SetFillColor(0xFF, 0xFF, 0xFF);
						$height = 5.5;
						$border = "";
					}else {
						$this->SetXY(18, 25 + $rows * 6);
						$this->SetFillColor($row_color['R'],$row_color['G'],$row_color['B']);
						$this->SetDrawColor($column_color['R'],$column_color['G'],$column_color['B']);
						$height = 6;
						$border = "TB";
					}
				}

				if(isset($_param[$i]['Quoteitem']['DISCOUNT']) && $_param[$i]['Quoteitem']['DISCOUNT']){

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
					//				$str = $_param[$i]['Quoteitem']['DISCOUNT'].($_param[$i]['Quoteitem']['DISCOUNT_TYPE']==1?'円':'％');
					$str = $_param[$i]['Quoteitem']['DISCOUNT_TYPE']==1?'':$_param[$i]['Quoteitem']['DISCOUNT'].'%';
					$str = $this->conv($str);
					$this->Cell($w_unit, $height, $str, $border, 0, 'R',1);

					//合計
					$str = '▲'.number_format($_param[$i]['Quoteitem']['DISCOUNT_TYPE']==0?$_param[$i]['Quoteitem']['AMOUNT']*($_param[$i]['Quoteitem']['DISCOUNT']*0.01):$_param[$i]['Quoteitem']['DISCOUNT']);
					$str = $this->conv($str);
					$this->Cell($w_total, $height, $str, $border, 0, 'R',1);
					$amount-=($_param[$i]['Quoteitem']['DISCOUNT_TYPE']==0?$_param[$i]['Quoteitem']['AMOUNT']*($_param[$i]['Quoteitem']['DISCOUNT']*0.01):$_param[$i]['Quoteitem']['DISCOUNT']);
					$item_count++;
					$rows++;
				}
				$item_count++;
			}

			if($_pages == 1 && $j == 0){
				$this->SetDrawColor($line_color['R'],$line_color['G'],$line_color['B']);
				$this->Line(18 + $w_no, 92, 18 + $w_no, 219);
				$this->Line(18 + $w_no + $w_code, 92, 18 + $w_no + $w_code, 219);
				$this->Line(18 + $w_no + $w_code + $w_item, 92, 18 + $w_no + $w_code + $w_item, 219);
				$this->Line(18 + $w_no + $w_code + $w_item + $w_quantity, 92, 18 + $w_no + $w_code + $w_item + $w_quantity, 219);
				$this->Line(18 + $w_no + $w_code + $w_item + $w_quantity + $w_unit, 92, 18 + $w_no + $w_code + $w_item + $w_quantity + $w_unit, 219);
			}else if($_pages > 1 && $j == 0){
				$this->SetDrawColor($line_color['R'],$line_color['G'],$line_color['B']);
				$this->Line(18 + $w_no, 92, 18 + $w_no, 279);
				$this->Line(18 + $w_no + $w_code, 92, 18 + $w_no + $w_code, 279);
				$this->Line(18 + $w_no + $w_code + $w_item, 92, 18 + $w_no + $w_code + $w_item, 279);
				$this->Line(18 + $w_no + $w_code + $w_item + $w_quantity, 92, 18 + $w_no + $w_code + $w_item + $w_quantity, 279);
				$this->Line(18 + $w_no + $w_code + $w_item + $w_quantity + $w_unit, 92, 18 + $w_no + $w_code + $w_item + $w_quantity + $w_unit, 279);
			}else if($_pages > 1 && $j == $_pages - 1){
				$this->SetDrawColor($line_color['R'],$line_color['G'],$line_color['B']);
				$this->Line(18 + $w_no, 17, 18 + $w_no, 205);
				$this->Line(18 + $w_no + $w_code, 17, 18 + $w_no + $w_code, 205);
				$this->Line(18 + $w_no + $w_code + $w_item, 17, 18 + $w_no + $w_code + $w_item, 205);
				$this->Line(18 + $w_no + $w_code + $w_item + $w_quantity, 17, 18 + $w_no + $w_code + $w_item + $w_quantity, 205);
				$this->Line(18 + $w_no + $w_code + $w_item + $w_quantity + $w_unit, 17, 18 + $w_no + $w_code + $w_item + $w_quantity + $w_unit, 205);
			}else {
				$this->SetDrawColor($line_color['R'],$line_color['G'],$line_color['B']);
				$this->Line(18 + $w_no, 17, 18 + $w_no, 265);
				$this->Line(18 + $w_no + $w_code, 17, 18 + $w_no + $w_code, 265);
				$this->Line(18 + $w_no + $w_code + $w_item, 17, 18 + $w_no + $w_code + $w_item, 265);
				$this->Line(18 + $w_no + $w_code + $w_item + $w_quantity, 17, 18 + $w_no + $w_code + $w_item + $w_quantity, 265);
				$this->Line(18 + $w_no + $w_code + $w_item + $w_quantity + $w_unit, 17, 18 + $w_no + $w_code + $w_item + $w_quantity + $w_unit, 265);
			}


			if($j != $_pages - 1) $this->AddPage();


		}

	}

	//フッター
	function Footer()
	{
			$this->setXY(100,290);

			$this->SetFont(MINCHO,'',9);
			$str = $this->PageNo().'/'.$this->Total_Page;
			$str = mb_convert_encoding($str, "SJIS");
			$this->Write(3,$str);
	}
}