<div class="user_reset_box">
<?php echo $form->create('User', array('type' => 'post', 'action' => 'pass_edit'));?>
		<div class="user_reset_area">
			<table cellspacing="0" cellpadding="0" border="0" width="600">
				<tr><th>パスワード</th><td><?php echo $form->password('EDIT_PASSWORD',array('class' => 'w200')); ?><br /><span class="must"><?php echo $form->error('EDIT_PASSWORD') ?></span></td></tr>
				<tr><th>パスワード確認</th><td><?php echo $form->password('EDIT_PASSWORD1',array('class' => 'w200')); ?><br /><span class="must"><?php echo $form->error('EDIT_PASSWORD') ?></span></td></tr>
			</table>
			<div class="user_reset_btn">
				<div class="submit"><?php echo $form->submit('保存');?><br />
			</div>
			</div>
		</div>
		<?php echo $html->image('/img/document/bg_search_bottom.jpg' ,array('class' => 'block'));?>
		<?php echo $form->hidden('key',array('value' => $key)); ?>
</div>
<?php echo $form->end();?>
<!-- contents_End -->