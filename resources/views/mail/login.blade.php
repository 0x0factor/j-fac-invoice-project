<?php echo $session->flash(); ?>
<div id="contents">
	<div class="contents_box">
		<?php echo $html->image('bg_contents_top.jpg',array('class'=>'block')); ?>
			<?php echo $form->create("Mail", array("type" => "post", "controller" => "mails", "action" => "customer")); ?>
		<div class="contents_area">
			<h3 class="mail_h3">パスワードの入力</h3>

			<p class="p_message"><?php echo $customer_charge; ?> 様宛てに <?php echo $charge; ?> 様より下記データが届いています。<br />パスワードを入力し次へを押してください。</p>

			<p class="pb20">※パスワードについてはメールには記載されておりません。<br />&emsp;もしもパスワードが届いていない場合には送信者様にご連絡ください。</p>

			<div class="mail_table2">

			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td class="bgcl w140">パスワード</td>
					<td><?php echo $form->text("PASSWORD", array('class' => 'w200')); ?></td>
				</tr>
			</table>
			</div>
		</div>
		<?php echo $html->image('/img/bg_contents_bottom.jpg',array('class'=>'block')); ?>
	</div>

	<div class="edit_btn">
			<?php echo $form->hidden("TOKEN", array('value' => $token)); ?>
			<?php echo $form->submit('/img/bt_next.jpg', array('name' => 'login', 'alt' => '次へ')); ?>
			<?php echo $form->end(); ?>
	</div>

</div>
