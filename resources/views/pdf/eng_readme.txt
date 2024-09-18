=============================================================================
    Multi-Byte FPDF                                             version: 1.0b
=============================================================================
                                                        airwhite@airwhite.net

  << Introduction >>

   'mbfpdf.php' was corrected based on 'chinese.php' by which Mr. Oliver was
  created. As for the treatment of Japanese, the past log of mailing list
  PHP-Users of Japan and the diary of minatsu of tsunami-sou was very helpful.
  I want to express gratitude here.

  << Outline >>

  1.The information on a font is managed by "FontName".
  2.The information on a font manages Character Width(cw), Underline Thickness
    (ut) and Underline Position(up).
  3.The information on a encode manages CMap name, Ordering and Supplement.
  4.Function AddBIG5Font, AddGBFont, and AddSJISFont were summarized in
    Function AddMBFont.
  5.In a Win32 environment, the 'TrueType' font can be registered using
    'mkttfdef.bat'.

  << Install >>

  require FPDF 1.51
  Downloaded "mbfpdf.zip" is uncompress. the "fpdf" holder is overwritten.

    ( For Japanese user only )
     If you want convert encoding to SJIS from EUC-JP, you must change
    $EUC2SJIS to true.(mbfpdf.php 25st line)

  << TEST >>

  You can execute 'exja.php' for Japanese and 'excn.php' for Chinese.

  << Add TrueType Font >>

  1.Open MS-DOS Prompt and move current directory to FPDF's makefont floder.
  2.execute 'mkttfdef TTF-File-Name' for update 'mbttfdef.php'.
  3.You can use new TTF Font.

  Thank you.
