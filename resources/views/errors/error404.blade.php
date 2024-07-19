<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta http-equiv="imagetoolbar" content="no" />
<meta name="description" content="" />
<meta name="keywords" content="" />
<meta name="copyright" content="" />
<meta name="robots" content="index,follow" />
<meta http-equiv="imagetoolbar" content="no" />
<link rel="icon" href="<?php echo $this->webroot;?>favicon.ico" type="image/x-icon" />
<title>ERROR｜抹茶請求書</title>
<?php echo $html->css("import")."\n"; ?>


</head>
<body>
<div id="wrapper">

<!-- header_Start -->
<div id="header" class="clearfix">
	<h1><?php echo $html->image('i_logo.jpg'); ?></h1>
</div>
<div id="submenu">
</div>
<!-- header_End -->

<div id="main">
	<!-- contents_Start -->
			<div class="function_box" style="margin:auto">
				<?php echo $html->image('/img/index/bg_function_top.jpg'); ?>
				<div class="function_area">
					<p>ページが見つかりません<br /><?php echo $html->link('トップへ戻る', array('controller' => '/', 'action' => 'index')); ?></p>
				</div>
				<?php echo $html->image('/img/index/bg_function_bottom.jpg', array('class' => 'block')); ?>
			</div>
</div>

<!-- footer_Start -->
<div id="footer">

	<address>Copyright &copy; 2011 ICZ corporation. All rights reserved.</address>
</div>
<!-- footer_End -->

</body>
</html>