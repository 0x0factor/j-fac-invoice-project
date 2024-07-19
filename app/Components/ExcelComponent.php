<?php

/**
 * @copyright ICZ Corporation (http://www.icz.co.jp/)
 * @license See the LICENCE file
 * @author <matcha@icz.co.jp>
 * @version $Id$
 */
namespace App\Components;

class ExcelComponent extends Object
{

    var $errors = array();

    /**
     * 出力用メソッド
     *
     * @param object $_controller
     * @param array $_field
     * @param array $_data
     * @param string $_file_name
     */
    function outputXls($_controller, $_field = array(), $_data = array(), $_file_name = "data")
    {
        // 出力初期設定
        $_controller->layout = false;
        $_controller->autoRender = false;
        Configure::write('debug', 0);

        // 読み込み
        App::import('vendor', 'phpexcel/phpexcel');

        $excel = new PHPExcel();
        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();

        // 文字設定
        $sheet->getDefaultStyle()
            ->getFont()
            ->setName(mb_convert_encoding('ＭＳ ゴシック', 'UTF-8'));

        // 列の幅指定
        $sheet->getColumnDimension('A')->setWidth(3);
        $sheet->getColumnDimension('B')->setWidth(16); // 日付
        $sheet->getColumnDimension('C')->setWidth(8); // 番号
        $sheet->getColumnDimension('D')->setWidth(24); // 取引先
        $sheet->getColumnDimension('E')->setWidth(24); // 件名
        $sheet->getColumnDimension('F')->setWidth(16); // 自社担当者
        $sheet->getColumnDimension('G')->setWidth(22); // 小計
        $sheet->getColumnDimension('H')->setWidth(22); // 消費税
        $sheet->getColumnDimension('I')->setWidth(22); // 合計
        $sheet->getColumnDimension('J')->setWidth(20); // 納入場所
        $sheet->getColumnDimension('K')->setWidth(20); // 有効期限

        // タイトル
        if ($_controller->name === 'Quote') :
            $sheet->setTitle('見積書一覧');
            $sheet->setCellValue('B' . '2', '見積書一覧');
         elseif ($_controller->name === 'Bill') :
            $sheet->setTitle('請求書一覧');
            $sheet->setCellValue('B' . '2', '請求書一覧');
         elseif ($_controller->name === 'Delivery') :
            $sheet->setTitle('納品書一覧');
            $sheet->setCellValue('B' . '2', '納品書一覧');
        endif;

        $sheet->getStyle('B' . '2')
            ->getFont()
            ->setBold(true);

        $start = $_controller->params['data'][$_controller->name]['DATE1']['year'] . "-" . $_controller->params['data'][$_controller->name]['DATE1']['month'] . "-" . $_controller->params['data'][$_controller->name]['DATE1']['day'];
        $end = $_controller->params['data'][$_controller->name]['DATE2']['year'] . "-" . $_controller->params['data'][$_controller->name]['DATE2']['month'] . "-" . $_controller->params['data'][$_controller->name]['DATE2']['day'];
        $n_str = $_controller->params['data'][$_controller->name]['DATE1']['year'] . $_controller->params['data'][$_controller->name]['DATE1']['month'] . $_controller->params['data'][$_controller->name]['DATE1']['day'];
        $n_end = $_controller->params['data'][$_controller->name]['DATE2']['year'] . $_controller->params['data'][$_controller->name]['DATE2']['month'] . $_controller->params['data'][$_controller->name]['DATE2']['day'];

        // 期間の設定
        $sheet->setCellValue('D' . '3', '期間　' . date('Y年m月d日', strtotime($start)) . '　～　' . date('Y年m月d日', strtotime($end)));

        // ウィンドウ枠の固定
        $sheet->freezePane('C6');

        // 横初期位置
        $col = 'B';

        // フィールドの設定
        if (is_array($_field)) :
            foreach ($_field as $key => $value) :

                // 値の挿入
                $sheet->setCellValue($col . '5', $value);

                // スタイルの取得
                $style = $sheet->getStyle($col ++ . '5');

                // 中央配置
                $style->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                // 線の設定
                $style->getBorders()
                    ->getRight()
                    ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $style->getBorders()
                    ->getLeft()
                    ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $style->getBorders()
                    ->getTop()
                    ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $style->getBorders()
                    ->getBottom()
                    ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);

                // 背景色の設定
                $style->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFCCFFCC');
            endforeach
            ;

		endif;

        // 初期位置
        $row = 6;

        $subtotal = 0;
        $sales_tax = 0;
        $total = 0;

        // データの設定
        if (is_array($_data)) :
            foreach ($_data as $key1 => $value1) :
                $col = 'B';
                if (is_array($value1)) :
                    foreach ($value1 as $key2 => $value2) :

                        // １番左
                        if (reset(array_keys($value1)) === $key2) :
                            $sheet->getStyle($col . $row)
                                ->getBorders()
                                ->getLeft()
                                ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

						endif;

                        // １番右
                        if (end(array_keys($value1)) === $key2) :
                            $sheet->getStyle($col . $row)
                                ->getBorders()
                                ->getRight()
                                ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

						endif;

                        // １番下
                        if (end(array_keys($_data)) === $key1) :
                            $sheet->getStyle($col . $row)
                                ->getBorders()
                                ->getBottom()
                                ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);

						endif;

                        // 値の挿入
                        if ($key2 == 6) :
                            $subtotal += $value2;
                            $sheet->getStyle($col . $row)
                                ->getAlignment()
                                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                            $sheet->setCellValue($col ++ . $row, number_format($value2));
                         elseif ($key2 == 7) :
                            $sales_tax += $value2;
                            $sheet->getStyle($col . $row)
                                ->getAlignment()
                                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                            $sheet->setCellValue($col ++ . $row, number_format($value2));
                         elseif ($key2 == 8) :
                            $total += $value2;
                            $sheet->getStyle($col . $row)
                                ->getAlignment()
                                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                            $sheet->setCellValue($col ++ . $row, number_format($value2));
                         else :
                            $sheet->setCellValue($col ++ . $row, $value2);
                        endif;
                    endforeach
                    ;

				endif;
                $row ++;
            endforeach
            ;

		endif;

        // 横初期位置
        $col = 'B';

        // 合計金額の設定
        if (is_array($_field)) :
            foreach ($_field as $key => $value) :

                // １番左
                if (reset(array_keys($_field)) === $key) :
                    $sheet->setCellValue($col . $row, '合計金額');
                    $sheet->getStyle($col . $row)
                        ->getAlignment()
                        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle($col . $row)
                        ->getBorders()
                        ->getLeft()
                        ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $sheet->getStyle($col . $row)
                        ->getFill()
                        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('FFFFFF99');

				endif;

                if ($key == 6) :
                    $sheet->getStyle($col . $row)
                        ->getAlignment()
                        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $sheet->setCellValue($col . $row, number_format($subtotal));
                 elseif ($key == 7) :
                    $sheet->getStyle($col . $row)
                        ->getAlignment()
                        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $sheet->setCellValue($col . $row, number_format($sales_tax));
                 elseif ($key == 8) :
                    $sheet->getStyle($col . $row)
                        ->getAlignment()
                        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $sheet->setCellValue($col . $row, number_format($total));
                endif;

                // １番右
                if (end(array_keys($_field)) === $key) :
                    $sheet->getStyle($col . $row)
                        ->getBorders()
                        ->getRight()
                        ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

				endif;

                // スタイルの取得
                $style = $sheet->getStyle($col ++ . $row);

                // 線の設定
                $style->getBorders()
                    ->getBottom()
                    ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            endforeach
            ;

		endif;

        $savefile = $_file_name . $n_str . '-' . $n_end . '.xlsx';
        $savepath = TMP;

        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $writer->save($savepath . '/' . $savefile);

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachement; filename=${savefile}");
        header("Content-Length: " . filesize($savepath . '/' . $savefile));

        readfile($savepath . '/' . $savefile);
    }
}
