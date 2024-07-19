<?php
/**
 * @copyright ICZ Corporation (http://www.icz.co.jp/)
 * @license See the LICENCE file
 * @author <matcha@icz.co.jp>
 * @version $Id$
 */

/*
 * Ajax・PostのURLを環境依存で生成する目的
 */
class CustomAjaxHelper extends AppHelper {
	var $helpers = array('Html');


	/*
	 * 帳票の顧客登録、商品登録のポップアップjs
	 */
	function popup(){
		$popup_url = $this->url('/ajax/popup');
		$popup_insert_url = $this->url('/ajax/popupinsert');
		echo <<<EOF
		<script type="text/javascript">
		//画面情報取得用クラス
		var WindowClass = function(){

			//現在のスクロール位置
			this.getScrollPosition = function () {
				var obj = new Object();
				obj.x = document.documentElement.scrollLeft || document.body.scrollLeft;
				obj.y = document.documentElement.scrollTop || document.body.scrollTop;
				return obj;
			};

			//横幅
			this.getBrowserWidth = function () {
				return document.documentElement.scrollWidth || document.body.scrollWidth;
			};

			//縦幅
			this.getBrowserHeight = function () {
				return document.documentElement.scrollHeight || document.body.scrollHeight;
			};

			//現在の画面サイズ
			this.getScreenSize = function () {

				var isWin9X = (navigator.appVersion.toLowerCase().indexOf('windows 98')+1);
				var isIE = (navigator.appName.toLowerCase().indexOf('internet explorer')+1?1:0);
				var isOpera = (navigator.userAgent.toLowerCase().indexOf('opera')+1?1:0);
				if (isOpera) isIE = false;
				var isSafari = (navigator.appVersion.toLowerCase().indexOf('safari')+1?1:0);
				var obj = new Object();

				if (!isSafari && !isOpera) {
					obj.x = document.documentElement.clientWidth || document.body.clientWidth || document.body.scrollWidth;
					obj.y = document.documentElement.clientHeight || document.body.clientHeight || document.body.scrollHeight;
				} else {
					obj.x = window.innerWidth;
					obj.y = window.innerHeight;
				}
				obj.mx = parseInt((obj.x)/2);
				obj.my = parseInt((obj.y)/2);
				return obj;
			};

		};

		//ポップアップウインドウ用クラス
		var PopupClass = function(win){

			//メンバ変数
			this.win = win;
			this.form;

			// 初期化処理
			this.init = function (form){
				this.form = form;

				//
				$('select').change(function(){

					//アイテム追加の場合
					if($(this).val() == 'item'){

						popupclass.popupajax($(this).val());

						//選択に戻す
						$(this).val('default');
					}

				});

				// ポップアップウィンドウの背景デザイン
				$('#popup-bg').css({
					background : '#333333',
					display    : 'none',
					position   : 'absolute',
					width      : windowclass.getBrowserWidth() + 'px',
					height     : windowclass.getBrowserHeight() + 'px',
					top        : '0px',
					left       : '0px',
					filter     : 'Alpha(opacity=80)',
					opacity    : 0.8
				});

				// ポップアップウィンドウの本体デザイン
				$('#popup').css({
					background : '#ffffff',
					padding    : '20px',
					display    : 'none',
					position   : 'absolute',
					width      : '580px',
					'border-radius': '20px',
					'-moz-border-radius': '20px',
					'-webkit-border-radius': '20px'
				});

				// 背景部分がクリックされたらポップアップを消す
				$('#popup-bg').click(function(){
					popupclass.popup_close();
				});

				//画面サイズ時の処理
				$(window).resize(function(){

					if($('#popup-bg').is(':visible') && $('#popup').is(':visible')){

						$('#popup-bg').css({
							width  : win.getBrowserWidth() + 'px',
							height : document.body.clientHeight + 'px'
						});

						$('#popup').animate({"top" : win.getScrollPosition().y + 100 + 'px'},{duration:300, queue: false});
						$('#popup').animate({"left": win.getScrollPosition().x + win.getScreenSize().mx - $('#popup').width()/2 + 'px'},{duration:300, queue: false});

					}
				});

				//画面スクロール時の処理
				$(window).scroll(function(){

					if($('#popup-bg').is(':visible') && $('#popup').is(':visible')){

						var heightpoint = win.getBrowserHeight() - 100 - $('#popup').height();
						if(heightpoint > win.getScrollPosition().y){
							heightpoint = win.getScrollPosition().y + 100;
						}

						$('#popup').animate({"top" : heightpoint + 'px'},{duration:300, queue: false});
						$('#popup').animate({"left": win.getScrollPosition().x + win.getScreenSize().mx - $('#popup').width()/2 + 'px'},{duration:300, queue: false});
					}
				});

			};

			// 割引設定用ポップアップ
			this.popup_discount = function (no, type){

				//IE6対策
				$('select').each(function(){
					$(this).hide();
				});

				// ポップアップ背景の表示
				$('#popup-bg').css({
					'width' : this.win.getBrowserWidth() + 'px',
					'height': document.body.clientHeight + 'px'
				}).fadeIn("slow");

				// ポップアップの表示
				$('#popup').css({
					'top'   : this.win.getScrollPosition().y + 100 + 'px',
					'left'  : win.getScrollPosition().x + win.getScreenSize().mx - $('#popup').width()/2 + 'px'
				}).fadeIn("slow");

				var value  = $('input[name="data['+no+']['+type+'][DISCOUNT]"]').val();
				var d_type = $('input[name="data['+no+']['+type+'][DISCOUNT_TYPE]"]').val();
				var code =
							'<form>'+
							'<div id="popup_contents">'+
								'<div>'+
									'<div>'+
									'{$this->Html->image('/img/popup/tl_sale.jpg',array('style'=>'padding-bottom:10px;'))}'+
									'<table width="440" cellpadding="0" cellspacing="0" border="0">'+
									'<tr class="p_discount_tr"><th style="padding:5px;text-align:left;">割引</th>'+
										'<td style="padding:5px;text-align:left;">'+
										'<input type="text" name="p_discount" class="mr10" maxlength="3" value="'+value+'">'+
										'<input type="text" style="display:none">'+
										'<input type="radio" name="p_discount_type" class="mr5" value="0" '+ (d_type==0 ? 'checked="checked" ' :' ') +'/>％'+
										'<input type="radio" name="p_discount_type" class="ml10 mr5" value="1" '+ (d_type==1 ? 'checked="checked" ' :' ') +'/>円</td></tr>'+
										'<tr><td colspan="2" class="line">'+'{$this->Html->image('/img/popup/i_line_solid.gif')}'+'</td></tr>'+
									'</table>'+
							'</div></div>'+
							'<div>'+
								'{$this->Html->link($this->Html->image('bt_save2.jpg'),'#',array('escape' => false,'onclick'=>"return popupclass.set_discount('+ no +', \''+ type +'\')",null,false))}'+
								'{$this->Html->link($this->Html->image('bt_cancel_s.jpg'),'#',array('alt'=>'キャンセル','escape' => false,'onclick'=>"return popupclass.popup_close();",null,false))}'+
							'</div>'+
							'</form>';

				$('#popup').html(code);
				$('#popup').find('input').focus(function(){
					if($(this).attr('name')=='p_discount'){
						$('input[name="p_discount"]').val(unnumber_format($('input[name="p_discount"]').val()));
					}
				});
				$('#popup').find(':radio').click(function(){
					if($(this).attr('name')=='p_discount_type'){
						if($(this).val()==1){
							$('input[name="p_discount"]').attr("maxlength",14);
						}
						else if($(this).val()==0){
						$('input[name="p_discount"]').val(unnumber_format($('input[name="p_discount"]').val()).slice(0,3));
						$('input[name="p_discount"]').attr("maxlength",3);
						}
					}
				});
				$('#popup').find('input').blur(function(){
					if(match = $(this).attr('name')=='p_discount'){
						$('input[name="p_discount"]').val(number_format($('input[name="p_discount"]').val()));
					}
				});
				return false;
			};

			// 割引の削除
			this.del_discount = function(no,type){
				if(!$('input[name="data['+no+']['+type+'][DISCOUNT_DISPLAY]"]').val()==""){
					$('input[name="data['+no+']['+type+'][DISCOUNT_DISPLAY]"]').val("");
					$('input[name="data['+no+']['+type+'][DISCOUNT]"]').val("");
					$('input[name="data['+no+']['+type+'][DISCOUNT_TYPE]"]').val(0);
					this.form.f_row(no);
					$('.add_'+no).removeClass('hidden');
					$('.del_'+no).addClass('hidden');
				}
				return false;
			};



			//割引のセット
			this.set_discount = function(no, type){
				//バリデーション
				$('#popup').find('.p_discount_error').remove();
				$('#popup').find('input:first-child').removeClass("error");
				$('input[name="p_discount"]').val(unnumber_format($('input[name="p_discount"]').val()));
				if($('input[name="p_discount_type"]:radio:checked').val()==0){
					num = new RegExp('^[0-9]*$');
					if($('input[name="p_discount"]').val()==""){
						var error = '<tr class="p_discount_error"><th></th><td style="padding:5px;text-align:left;"><span class="must">割引率は必須です</span></td></tr>';
						$('#popup').find('.p_discount_tr').after(error);
						$('#popup').find('input[name=p_discount]').addClass("error");
						$('input[name="p_discount"]').val(number_format($('input[name="p_discount"]').val()));
						return false;
					}
					else if(!$('input[name="p_discount"]').val().match(num)){
						var error = '<tr class="p_discount_error"><th></th><td style="padding:5px;text-align:left;"><span class="must">割引率は数字のみです</span></td></tr>';
						$('#popup').find('.p_discount_tr').after(error);
						$('#popup').find('input[name=p_discount]').addClass("error");
						$('input[name="p_discount"]').val(number_format($('input[name="p_discount"]').val()));
						return false;
					}
					else if($('input[name="p_discount"]').val()>100){
						var error = '<tr class="p_discount_error"><th></th><td style="padding:5px;text-align:left;"><span class="must">正しい割引率ではありません</span></td></tr>';
						$('#popup').find('.p_discount_tr').after(error);
						$('#popup').find('input[name=p_discount]').addClass("error");
						$('input[name="p_discount"]').val(number_format($('input[name="p_discount"]').val()));
						return false;
					}
					else{
						$('input[name="data['+no+']['+type+'][DISCOUNT]"]').val($('input[name="p_discount"]').val());
						$('input[name="data['+no+']['+type+'][DISCOUNT_TYPE]"]').val($('input[name="p_discount_type"]:radio:checked').val());

						var str = '【割引】 ';
						str += $('input[name="p_discount"]').val();
						str += '%';

						$('input[name="data['+no+']['+type+'][ITEM]"]').val(str);


						this.form.f_row(no);
						this.popup_close();
						return false;
					}
				}
				if($('input[name="p_discount_type"]:radio:checked').val()==1){
					num = new RegExp('^[0-9]*$');
					if($('input[name="p_discount"]').val()==""){
						var error = '<tr class="p_discount_error"><th></th><td style="padding:5px;text-align:left;"><span class="must">割引は必須です</span></td></tr>';
						$('#popup').find('.p_discount_tr').after(error);
						$('#popup').find('input[name=p_discount]').addClass("error");
						$('input[name="p_discount"]').val(number_format($('input[name="p_discount"]').val()));
						return false;
					}
					else if(!$('input[name="p_discount"]').val().match(num)){
						var error = '<tr class="p_discount_error"><th></th><td style="padding:5px;text-align:left;"><span class="must">割引は数字のみです</span></td></tr>';
						$('#popup').find('.p_discount_tr').after(error);
						$('#popup').find('input[name=p_discount]').addClass("error");
						$('input[name="p_discount"]').val(number_format($('input[name="p_discount"]').val()));
						return false;
					}
					else if($('input[name="p_discount"]').val().length>14){
						var error = '<tr class="p_discount_error"><th></th><td style="padding:5px;text-align:left;"><span class="must">割引が長すぎます</span></td></tr>';
						$('#popup').find('.p_discount_tr').after(error);
						$('#popup').find('input[name=p_discount]').addClass("error");
						$('input[name="p_discount"]').val(number_format($('input[name="p_discount"]').val()));
						return false;
					}
					else{
						$('input[name="data['+no+']['+type+'][DISCOUNT]"]').val(unnumber_format($('input[name="p_discount"]').val()));
						$('input[name="data['+no+']['+type+'][DISCOUNT_TYPE]"]').val($('input[name="p_discount_type"]:radio:checked').val());


						var str = '【割引】 ';
						str += $('input[name="p_discount"]').val();
						str += '円';

						$('input[name="data['+no+']['+type+'][ITEM]"]').val(str);

						this.form.f_row(no);
						this.popup_close();
						return false;
					}
				}

			};



			//ポップアップ画面の終了
			this.popup_close = function(){

				$('#popup-bg').fadeOut("slow");
				$('#popup').fadeOut("slow", function(){
					//IE6対策
					$('select').each(function(){
						$(this).show();
					});
				});

				return false;
			};


			//数字区切り変換用メンバ関数
			number_format = function(_num){
				if(_num&&!(_num.toString().match(/^(\\|\$)?(0|-?[1-9]\d*|-?(0|[1-9]\d*)\.\d+)$/))){
					return _num;
				}
				for(i=0;_num.toString().charAt(0)=='0';i++){
					if(_num.toString().length==1||_num.toString().charAt(1)=='.'){
						break;
					}
					_num=_num.toString().slice(1);
				}
				var str =_num.toString().indexOf('.');
				if(str!=-1){
					var str = _num.toString().slice(0,str);
					var astr = str.replace( /([0-9]+?)(?=(?:[0-9]{3})+$)/g , '$1,');
					return _num.toString().replace(str,astr);
				}
				return _num.toString().replace( /([0-9]+?)(?=(?:[0-9]{3})+$)/g , '$1,');
			};


			//数字区切り逆変換用メンバ関数
			unnumber_format = function(_num){
				for(i=0;_num.toString().charAt(0)=='0';i++){
					if(_num.toString().length==1){
						break;
					}
					_num=_num.toString().slice(1);
				}
				return _num.toString().replace( /,/g , '');
			};

			//汎用ポップアップAjaxメソッド
			this.popupajax = function (type, no){
				var param = eval({
									"type" : type,
									"no"   : no
								});

				if(type == "customer_charge") {
					var id = 	$('#SETCUSTOMER').children('input[type=hidden]').val();
					var keyword = $('#CHRC_KEYWORD').val();
					var param = eval({
									"type" : type,
									"no"   : no,
									"id"   : id,
									"keyword"   : keyword
								});
				}

				if(type == "select_item") {
					var keyword = $('#ITEM_KEYWORD').val();
					var param = eval({
									"type"      : type,
									"no"        : no,
									"keyword"   : keyword
								});
				}

				if(type == "select_customer") {
					var keyword = $('#CST_KEYWORD').val();
					var param = eval({
									"type"      : type,
									"no"        : no,
									"keyword"   : keyword
								});
				}

				if(type == "charge") {
					var keyword = $('#CHR_KEYWORD').val();
					var param = eval({
									"type"      : type,
									"no"        : no,
									"keyword"   : keyword
								});
				}

				var win = this.win;

				$.post("$popup_url", {params:param}, function(d){
					//IE6対策
					$('select').each(function(){
						$(this).hide();
					});

					$('#popup-bg').css({
						width  : win.getBrowserWidth() + 'px',
						height : document.body.clientHeight + 'px'
					}).fadeIn("slow");

					$('#popup').css({
						'top'      : win.getScrollPosition().y + 100 + 'px',
						'left'     : win.getScreenSize().mx - $('#popup').width()/2 + 'px'
					}).fadeIn("slow");
					$('#popup').html(d);

					if(type=='item'){
						$('#popup').find('input').focus(function(){
							if($(this).attr('name')=='data[UNIT_PRICE]'){
								$('input[name="data[UNIT_PRICE]"]').val(unnumber_format($('input[name="data[UNIT_PRICE]"]').val()));
							}
						});
						$('#popup').find('input').blur(function(){
							if(match = $(this).attr('name')=='data[UNIT_PRICE]'){
								var unitprice = form.unnumber_format($('input[name="data[UNIT_PRICE]"]').val());
								var stnum = unitprice.toString().indexOf('.');
								var dec = 3;
								if(stnum!=-1){
									if(unitprice.toString().slice(stnum+1).length>dec){
										if(Number(unitprice)){
											$('input[name="data[UNIT_PRICE]"]').val(number_format(Number(unitprice).toFixed(dec)));
										}
									}else{
										$('input[name="data[UNIT_PRICE]"]').val(form.number_format(unitprice));
									}
								}else{
									$('input[name="data[UNIT_PRICE]"]').val(number_format(unitprice));
								}
							}
						});
					}
				});


				return false;
			};



			this.popupinsert = function (type){

				$('#popup').find('tr.popup_error').remove();
				$('#popup').find('input').removeClass("error");
				if(type=='item'){
					$('input[name="data[UNIT_PRICE]"]').val(unnumber_format($('input[name="data[UNIT_PRICE]"]').val()));
				}
				var param = $('#popupForm').serialize();

				var obj = this;

				$.post("$popup_insert_url", {params:param}, function(d){
					var data = eval("(" + d + ")");
					var maintype = $('form').attr('class');

					if(!data.error){
						//成功時
						obj.popup_close();
						if(type == 'customer'){
							$('#SETCUSTOMER').children('input[type=text]').val(data.Customer.NAME);
							$('#SETCUSTOMER').children('input[type=hidden]').val(data.Customer.CST_ID);
						}else if(type == 'item'){
							$('.popupSelect'+type).append($('<option>').attr({ value: data.Item.ITM_ID }).text(data.Item.ITEM));
							$('.popupSelect'+type).width();
							var text=$('#itemlist').text();
							var length=0;
							if(!text){
								text='{';
								$('#itemlist').text(text+'"'+data.Item.ITM_ID+'":'+'{"ITEM":'+'"'+form.h(data.Item.ITEM)+'"'+',"UNIT":'+'"'+data.Item.UNIT+'"'+',"UNIT_PRICE":'+'"'+data.Item.UNIT_PRICE+'"'+"}}")
							}
							else{
								length=text.length;
								text=text.slice(0,length-1);
								$('#itemlist').text(text+',"'+data.Item.ITM_ID+'":'+'{"ITEM":'+'"'+form.h(data.Item.ITEM)+'"'+',"UNIT":'+'"'+data.Item.UNIT+'"'+',"UNIT_PRICE":'+'"'+data.Item.UNIT_PRICE+'"'+"}}")
							}
							$('.popupSelect'+type).val(data.Item.ITM_ID);
						}else if(type == 'customer_charge'){
							$('#SETCUSTOMERCHARGE').children('input[type=text]').val(data.CustomerCharge.CHARGE_NAME);
							$('#SETCUSTOMERCHARGE').children('input[type=hidden]').val(data.CustomerCharge.CHRC_ID);
							$('#SETCCUNIT').children('input[type=text]').val(data.CustomerCharge.UNIT);
						}
					}else{
						//失敗時
						if(data.error.NAME){
							$('#popup').find('tr.popup_cname').after('<tr class="popup_error"><th></th><td><span class="must">'+data.error.NAME+'</span></td></tr>');
							$('#popup').find('#NAME').addClass("error");
						}
						if(data.error.NAME_KANA){
							$('#popup').find('tr.popup_ckname').after('<tr class="popup_error"><th></th><td><span class="must"><span class="must">'+data.error.NAME_KANA+'</span></td></tr>');
							$('#popup').find('#NAME_KANA').addClass("error");
						}
						if(data.error.CHARGE_NAME){
							$('#popup').find('tr.popup_cname').after('<tr class="popup_error"><th></th><td><span class="must">'+data.error.CHARGE_NAME+'</span></td></tr>');
							$('#popup').find('#CHARGE_NAME').addClass("error");
						}
						if(data.error.CHARGE_NAME_KANA){
							$('#popup').find('tr.popup_ckname').after('<tr class="popup_error"><th></th><td><span class="must"><span class="must">'+data.error.CHARGE_NAME_KANA+'</span></td></tr>');
							$('#popup').find('#CHARGE_NAME_KANA').addClass("error");
						}
						if(data.error.UNIT){
							$('#popup').find('tr.popup_cunit').after('<tr class="popup_error"><th></th><td><span class="must"><span class="must">'+data.error.UNIT+'</span></td></tr>');
							$('#popup').find('#UNIT').addClass("error");
						}

						if(data.error.POSTCODE1||data.error.POSTCODE2){
							if(data.error.POSTCODE1){
							$('#popup').find('tr.popup_postcode').after('<tr class="popup_error"><th></th><td><span class="must">'+data.error.POSTCODE1+'</span></td></tr>');
							}
							else if(data.error.POSTCODE2){
							$('#popup').find('tr.popup_postcode').after('<tr class="popup_error"><th></th><td><span class="must">'+data.error.POSTCODE2+'</span></td></tr>');
							}
							$('#popup').find('#POSTCODE1').addClass("error");
							$('#popup').find('#POSTCODE2').addClass("error");
						}
						if(data.error.ADDRESS){
							$('#popup').find('tr.popup_address').after('<tr class="popup_error"><th></th><td><span class="must">'+data.error.ADDRESS+'</span></td></tr>');
							$('#popup').find('#ADDRESS').addClass("error");
						}
						if(data.error.PHONE){
							$('#popup').find('tr.popup_phone').after('<tr class="popup_error"><th></th><td><span class="must">'+data.error.PHONE+'</span></td></tr>');
							$('#popup').find('#PHONE_NO1').addClass("error");
							$('#popup').find('#PHONE_NO2').addClass("error");
							$('#popup').find('#PHONE_NO3').addClass("error");
						}
						if(data.error.ITEM){
							$('#popup').find('tr.popup_item').after('<tr class="popup_error"><th></th><td><span class="must">'+data.error.ITEM+'</span></td></tr>');
							$('#popup').find('#ITEM').addClass("error");
						}
						if(data.error.ITEM_KANA){
							$('#popup').find('tr.popup_item_kana').after('<tr class="popup_error"><th></th><td><span class="must">'+data.error.ITEM_KANA+'</span></td></tr>');
							$('#popup').find('#ITEM_KANA').addClass("error");
						}
						if(data.error.UNIT){
							$('#popup').find('tr.popup_unit').after('<tr class="popup_error"><th></th><td><span class="must">'+data.error.UNIT+'</span></td></tr>');
							$('#popup').find('#UNIT').addClass("error");
						}
						if(data.error.UNIT_PRICE){
							$('#popup').find('tr.popup_unitprice').after('<tr class="popup_error"><th></th><td><span class="must">'+data.error.UNIT_PRICE+'</span></td></tr>');
							$('#popup').find('#UNIT_PRICE').addClass("error");
						}

						if(data.error.ITEM_CODE){
							$('#popup').find('tr.popup_item_code').after('<tr class="popup_error"><th></th><td><span class="must">'+data.error.ITEM_CODE+'</span></td></tr>');
							$('#popup').find('#ITEM_CODE').addClass("error");
						}

						if(type == 'item'){
							$('input[name="data[UNIT_PRICE]"]').val(number_format($('input[name="data[UNIT_PRICE]"]').val()));
						}
					}
				});

				return false;
			};

		};



</script>
EOF;
	}

	/*
	 * 郵便番号から住所検索時のjs
	 */
	function postcode(){
		$candidacy_url = $this->url('/ajax/candidacy');
		$search_url = $this->url('/ajax/search');
		echo <<<EOF
		<script type="text/javascript">
			$(document).ready(function($){

				if($('form').attr('class')){
					var pagetype = jQuery('form').attr('class');
					$('input#'+pagetype+'POSTCODE1').keyup(function(){
						getaddress();
					});

					$('input#'+pagetype+'POSTCODE2').keyup(function(){
						getaddress();
					});

					function getaddress(){
						var params = $('#'+pagetype+'AddForm').serialize();
						if(!params){
							params = $('#'+pagetype+'EditForm').serialize();
						}
						if(!params){
							params = $('#'+pagetype+'IndexForm').serialize();
						}
						$.post("$candidacy_url", {params:params}, function(d){
							$('div#target').html(d);
						});
					}
				}
			});

		function setaddress(postcode){
			if($('form').attr('class')){
				var pagetype = $('form').attr('class');
				$.post("$search_url", {postcode:postcode}, function(d){
					var data = eval("(" + d + ")");
					if(data){
						$('input#'+pagetype+'POSTCODE1').val(data.POSTCODE1);
						$('input#'+pagetype+'POSTCODE1').css('color', '#000');
						$('input#'+pagetype+'POSTCODE2').val(data.POSTCODE2);
						$('input#'+pagetype+'POSTCODE2').css('color', '#000');
						$('select#'+pagetype+'CNTID').val(data.CNT_ID);
						$('input#'+pagetype+'ADDRESS').val(data.CITY + data.AREA);
						$('input#'+pagetype+'ADDRESS').css('color', '#000');
					}
				});
				$('div#target').empty();
			}
			return false;
		}
	</script>
EOF;
	}
	/*
	 * ログインID重複の確認js
	 */
	function usercode(){
		$candidacy_url = $this->url('/ajax/candidacy');
		$search_url = $this->url('/ajax/searchid');
		echo <<<EOF
		<script type="text/javascript">
		$(document).ready(function($){
			if($('form').attr('class') && $('form').attr('class').match(/^(Administer)$/))
			{
				var pagetype = jQuery('form').attr('class');
				//数値入力時処理
				$('input').keyup(function(){
					var this_name = $(this).attr('name');
					var match;
					var usrid = $('input[name="data[Administer][USR_ID]"]').val();
					reg1  = new RegExp(/^data\\[Administer\\]\\[(LOGIN_ID)\\]$/);
					if(match = this_name.match(reg1)){
					var loginid = $('input[name="data[Administer][LOGIN_ID]"]').val();
							$.post("$search_url", {logincode:loginid,usercode:usrid}, function(d){
							$('div#target').html(d);
						});
					//あり
					}else{
						//なし
					}

				});
			}
		});
	</script>
EOF;
	}
}