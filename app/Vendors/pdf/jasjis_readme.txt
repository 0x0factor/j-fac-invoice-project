==============================================================================
    Multi-Byte FPDF                                             version: 1.0b
==============================================================================
                                                         airwhite@airwhite.net

《はじめに》
mbfpdf.php は、Oliver氏が作成された chinese.php を元に修正したものです。
日本語対応に当たっては、PHP-Users ML 過去ログと津波荘のminatsuさんの日記が
とても役に立ちました。この場を借りて、お礼を申し上げます。

《不具合に対する対応》
1.0b には、以下の修正が含まれています。
UNO Shintaroさん、Patrick Bennyさん、使ってくれている皆さん
どうもありがとうございます。

(1) UNO Shintaroさんのご好意により
   ・半角カナの幅の件
   ・MultiCellが条件によって無限ループに陥る件
   ・MultiCellの折り返し処理
   が対応済みになっています。

(2) Patrick Bennyさんのご好意により
   ・グローバル変数の使用についての問題
   が対応済みになっています。

《今後について》
自分のシステム開発用にPHPの勉強がてらに作ったものですが
実は仕事では使いそうもないことがわかりショックを受けています。
そんな事情も手伝い、現在は恐縮ですがほぼメンテできません。
ですので、著作権は放棄、名称もバージョンもご自由に変更して
使っていただいても結構です。本家の日本語バージョンに
マージしていただくのが一番良いとは思いますが・・・
英語力無し＆パワー不足なのであきらめています。

《概要》
・書体情報の管理と使用を実際のFontNameで行います。
・書体情報は文字幅(cw)と下線の厚さ(UnderlineThickness=ut)と
  下線位置(UnderlinePosition=up)を保持します。
・エンコード情報は対応するCMap名とOrderingとSupplementを保持します。
・AddBIG5Font、AddGBFont、AddSJISFontをまとめて、AddMBFontに統一しました。
・Win32 Onlyですが mkttfdef.bat を使って楽に TTF 書体の登録ができます。
（mkttfdef.batは、Yann Sugere氏のaddfont.zipにあるttf2pt1.exeが必要です。）

《インストール》
・FPDF 1.51 をインストールしておいて下さい。
・mbfpdf.zip を解凍してfpdfフォルダを上書きします。
・EUC-JP -> SJIS 変換を自動的で行う場合は、25行目の$EUC2SJISの値をtrueに変えるか
  実行時に $GLOBALS['EUC2SJIS'] を true に設定する。

《動作確認》
・クライアントがWindows環境なら付属のexja.phpをサーバに乗せて実行できるでしょう。

《TrueType フォントの追加方法(Win32 Only)》
・http://www.fpdf.org/en/script/index.php からYann Sugere氏のaddfont.zipを
  ダウンロードして、FPDFのfont/makefontフォルダへ解凍。
・MS-DOSプロンプトを開き、FPDFのfont/makefontフォルダへ移動。
・mkttfdef TTFファイル名 を実行するとfontフォルダのmbttfdef.phpが更新されます。
