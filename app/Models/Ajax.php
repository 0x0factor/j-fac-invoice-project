<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ajax extends Model
{
    protected $table = null; // Disable usage of a specific table

    /**
     * ページング
     */
    public function paging(&$paging, &$nowpage, &$number, $count, $page)
    {
        if ($count > $number) {
            // 全ページ数
            $total = floor(($count / $number) - 0.05) + 1;

            if ($page == 0) {
                $paging .= "<< 前へ | ";
            } else {
                $paging .= '<a href="javascript:void(0)" onclick="return paging(' . ($page - 1) . ');return false;"><< 前へ</a> | ';
            }

            // 10ページ以下の場合
            if ($total < 11) {
                for ($i = 0; $i < $total; $i++) {
                    if ($page == $i) {
                        $paging .= ($i + 1) . " | ";
                    } else {
                        $paging .= '<a href="javascript:void(0)" onclick="return paging(' . $i . ');return false;">' . ($i + 1) . '</a> | ';
                    }
                }
            } else {
                if ($page < 5) {
                    for ($i = 0; $i < 10; $i++) {
                        if ($page == $i) {
                            $paging .= ($i + 1) . " | ";
                        } else {
                            $paging .= '<a href="javascript:void(0)" onclick="return paging(' . $i . ');return false;">' . ($i + 1) . '</a> | ';
                        }
                    }
                    $paging .= " ・・・ ";
                } elseif ($total <= $page + 5) {
                    $paging .= " ・・・ ";
                    for ($i = $total - 10; $i < $total; $i++) {
                        if ($page == $i) {
                            $paging .= ($i + 1) . " | ";
                        } else {
                            $paging .= '<a href="javascript:void(0)" onclick="return paging(' . $i . ');return false;">' . ($i + 1) . '</a> | ';
                        }
                    }
                } else {
                    $paging .= " ・・・ ";
                    for ($i = $page - 4; $i <= $page + 5; $i++) {
                        if ($page == $i) {
                            $paging .= ($i + 1) . " | ";
                        } else {
                            $paging .= '<a href="javascript:void(0)" onclick="return paging(' . $i . ');return false;">' . ($i + 1) . '</a> | ';
                        }
                    }
                    $paging .= " ・・・ ";
                }
            }

            if ($page >= floor(($count / $number) - 0.05)) {
                $paging .= "次へ >>";
            } else {
                $paging .= '<a href="javascript:void(0)" onclick="return paging(' . ($page + 1) . ');return false;">次へ >></a>';
            }
        }

        $nowpage = "$count 件中 " . ($count ? $page * $number + 1 : 0) . " - " . ($count > ($page * $number + $number) ? ($page * $number + $number) : $count) . " 件を表示<br>";
    }
}

