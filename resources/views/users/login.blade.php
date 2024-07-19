<div id="login">
<?php echo $form->create('User', array('type' => 'post', 'action' => 'login', 'name' => 'UserLoginForm'));?>
	<div class="login"><?php echo $html->image('/img/login/tl_login.jpg',array('alt' => 'ログイン画面'));?></div>
	<div id="login_box">
		<div id="login_logo"><?php echo $html->image('/img/login/i_logo_login.jpg'); ?></div>
		<div id="login_id"><?php echo $html->image('/img/login/tm_id.gif', array('alt' => 'ID', 'class' => 'mr10')); ?>
			<?php echo $form->text("LOGIN_ID", array('class' => "w320", 'value' => '')); ?>
		</div>

		<div id="login_pw"><?php echo $html->image('/img/login/tm_pw.gif', array('alt' => 'パスワード', 'class' => 'mr10')); ?>
			<?php echo $form->password("PASSWORD", array('class' => "w320")); ?>
		</div>
		<div id="login_btn">
		<?php echo $form->submit('/img/login/bt_login.jpg', array('div' => false , 'name' => 'submit', 'alt' => 'ログイン', 'class' => 'imgover')); ?>
		<?php echo $html->link('パスワードお忘れの方',array('action' => 'reset'))?>
		</div>
		<?php if ($session->check('Message.flash')){ echo "<span class=\"must\">{$session->flash()}</span>";} ?>
	</div>
<?php echo $form->end();?>
</div>
<!-- contents_End -->