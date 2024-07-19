<div class="user_reset_box">
<?php echo $form->create('User', array('type' => 'post', 'action' => 'reset'));?>
		<div class="user_reset_area">
			<table cellspacing="0" cellpadding="0" border="0" width="600">
				<tr><td colspan="2">インストール時、またはユーザ登録時に設定したメールアドレスを入力してください</td></tr>
				<tr><th>メールアドレス</th><td><?php echo $form->text('MAIL',array('class' => 'w200')); ?><br /><span class="must"><?php echo $session->flash() ?></span></td></tr>
			</table>
			<div class="user_reset_btn">
				<div class="submit"><?php echo $form->submit('メール送信');?></div>
			</div>
		</div>
		<?php echo $html->image('/img/document/bg_search_bottom.jpg' ,array('class' => 'block'));?>
</div>
<?php echo $form->end();?>
<!-- contents_End -->