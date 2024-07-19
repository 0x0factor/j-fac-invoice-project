//値段自動計算クラス
var FormClass = function()
{

	//メンバ変数
	this.maintype;
	this.type;
	this.subtotal;
	this.reg1;
	this.reg2;
	this.reg3;
	this.reg4;
	this.reg5;
	this.reg6;
	this.reg8;
	this.reg9;
	this.reg10;
	this.reg12;
	this.reg13;
	this.maxformline;
	this.dataformline;
	this.nowformline;
	this.focusline;
	this.IEcheck = false;
	this.textarea;
	this.bt_del;
	this.bt_set;
	this.bt_can;
	this.CHcheck = false;
	this.msg_discount = 0;

	//初期化
	this.f_init = function(){
		//正規表現の設定
		this.maintype = $('form').attr('class');
		this.type     = this.maintype + 'item';
		this.subtotal = 0;
		this.reg1     = new RegExp('^data\\[([0-9]+)\\]\\['+this.type+'\\]\\[(QUANTITY|UNIT_PRICE|DISCOUNT)\\]$');
		this.reg2     = new RegExp('^data\\['+this.maintype+'\\]\\[(FRACTION|EXCISE)\\]$');
		this.reg3     = new RegExp('^data\\[([0-9]+)\\]\\['+this.type+'\\]\\[(UNIT_PRICE)\\]$');
		this.reg4     = new RegExp('^data\\[([0-9]+)\\]\\['+this.type+'\\]\\[NOTE_CHECK\\]$');
		this.reg5     = new RegExp('^data\\[([0-9]+)\\]\\['+this.type+'\\]\\[DISCOUNT_TYPE\\]$');
		this.reg6     = new RegExp('^data\\['+this.maintype+'\\]\\[DISCOUNT_TYPE\\]$');
		this.reg7     = new RegExp('^data\\[[0-9]+\\]\\['+this.type+'\\]\\[(.+)\\]$');
		this.reg8     = new RegExp('^data\\['+this.maintype+'\\]\\[(CUTOOFF_SELECT)\\]$');
		this.reg9     = new RegExp('^data\\['+this.maintype+'\\]\\[(PAYMENT_SELECT)\\]$');
		this.reg10    = new RegExp('^data\\[([0-9]+)\\]\\['+this.type+'\\]');
		this.reg12    = new RegExp('^data\\['+this.maintype+'\\]\\[(DISCOUNT)\\]$');
		this.reg13    = new RegExp('^data\\[([0-9]+)\\]\\['+this.type+'\\]\\[(QUANTITY)\\]$');
		this.reg14    = new RegExp('^data\\[([0-9]+)\\]\\['+this.type+'\\]\\[(UNIT_PRICE|AMOUNT)\\]$');
		this.reg15    = new RegExp('^data\\['+this.maintype+'\\]\\[DECIMAL_QUANTITY\\]$');
		this.reg16    = new RegExp('^data\\['+this.maintype+'\\]\\[DECIMAL_UNITPRICE\\]$');
		this.reg17    = new RegExp('^data\\['+this.maintype+'\\]\\[DATE\\]$');

		this.textarea =0;

		//IEかそれ以外か判定
		var userAgent = window.navigator.userAgent.toLowerCase();
		var appVersion = window.navigator.appVersion.toLowerCase();
		if (userAgent.indexOf("msie") > -1) {
			if (appVersion.indexOf("msie 6.0") > -1) {
				this.IEcheck = true;
			}
			else if (appVersion.indexOf("msie 7.0") > -1) {
				this.IEcheck = true;
			}
			else if (appVersion.indexOf("msie 8.0") > -1) {
				this.IEcheck = true;
			}
			else if (appVersion.indexOf("msie 9.0") > -1) {
				this.IEcheck = true;
			}
			else {
				this.IEcheck = false;
			}
		}
		else{
			this.IEcheck = false;
		}

		//
		this.maxformline  = $('input[name="data['+this.maintype+'][maxformline]"]').val();
		this.dataformline = $('input[name="data['+this.maintype+'][dataformline]"]').val() ? $('input[name="data['+this.maintype+'][dataformline]"]').val()*1 : 1;

		//値引き表示の処理
		for(var i=0;i<this.dataformline;i++)
		{
			if($('input[name="data['+i+']['+this.type+'][DISCOUNT]"]').val()){
				$('.add_'+i).addClass('hidden');
				$('input[name="data['+i+']['+this.type+'][DISCOUNT_DISPLAY]"]').val(form.number_format($('input[name="data['+i+']['+this.type+'][DISCOUNT]"]').val())+($('input[name="data['+i+']['+this.type+'][DISCOUNT_TYPE]"]').val()==1?"円引き":"％引き"));
			}else{
				$('.del_'+i).addClass('hidden');
			}
		}
		for(var i=this.dataformline;i<this.maxformline;i++)
		{
			//消す
			$('tr.row_'+i).css({display : 'none'});
			$('tr.row_'+i).html('');
		}
		this.nowformline = this.dataformline-1;
		if($('tr.row_0 td img.delbtn') && $('tr.row_0 td img.add_0') && $('tr.row_0 td img.del_0')){

			this.bt_del = $('tr.row_0 td img.delbtn').attr('src');
			this.bt_set = $('tr.row_0 td img.add_0').attr('src');
			this.bt_can = $('tr.row_0 td img.del_0').attr('src');

		}
		$('textarea').focus(function(){
			form.textarea=1;
		});
		$('textarea').blur(function(){
			form.textarea=0;
		});


        var enterKeyGo = 0;

        var indexForm = '#'+controller_name+'IndexForm';

        if($(indexForm).length != 0){
            enterKeyGo = 1;
        }
        if($('#CustomerSelectForm').length != 0){
            enterKeyGo = 1;
        }

        document.onkeydown = KeyEvent;
		function KeyEvent(e){
			e = e || window.event;
		    pressKey=e.keyCode;
		     if(pressKey==13&&form.textarea!=1&&enterKeyGo==0){return false;}
		}



		//削除,複製ボタン処理
		$('input[name="delete"]').attr('disabled','disabled');
		$('input[name="reproduce"]').attr('disabled','disabled');
		$('input[name="status_change"]').attr('disabled','disabled');

		$('input.chk').each(function(){
			if($(this).attr('checked')){
				$('input[name="delete"]').attr('disabled','');
				$('input[name="reproduce"]').attr('disabled','');
				$('input[name="status_change"]').attr('disabled','');
			}
		});
		$('input.chk').change(function(){
			var flg = false;
			$('input.chk').each(function(){
				if($(this).attr('checked')){
					flg = true;
				}
			});
			if(flg){
				$('input[name="delete"]').attr('disabled','');
				$('input[name="reproduce"]').attr('disabled','');
				$('input[name="status_change"]').attr('disabled','');
			}else{
				$('input[name="delete"]').attr('disabled','disabled');
				$('input[name="reproduce"]').attr('disabled','disabled');
				$('input[name="status_change"]').attr('disabled','disabled');
			}
		});
		if($('div[id="billfrag"]').text()=='1'||$('div[id="edit_stat"]').text()=='0'){
			$('.hidebox_d').css('display','none');
			$('.hidebox_s').css('display','true');
		}
		if($('div[id="billfrag"]').text()=='0'||$('div[id="edit_stat"]').text()=='1'){
			$('.hidebox').html('');
			$('.hidebox_s').css('display','none');
			$('.hidebox_d').css('display','true');

		}
		if($('form').attr('class') && $('form').attr('class').match(/^(Receipt)$/))
		{
			$('input[name="data[Bill][TOTAL]"]').val(form.number_format($('input[name="data[Bill][TOTAL]"]').val()));
			$('.imgcheck').mouseover(function(){
				$('input[name="data[Bill][TOTAL]"]').val(form.unnumber_format($('input[name="data[Bill][TOTAL]"]').val()));
			});
			$('.imgcheck').mouseout(function(){
				$('input[name="data[Bill][TOTAL]"]').val(form.number_format($('input[name="data[Bill][TOTAL]"]').val()));
			});

		}
		if($('form').attr('class') && $('form').attr('class').match(/^(Quote|Bill|Delivery|Regularbill)$/))
		{
			//初期状態のチェック
			if($('input[name="data['+form.maintype+'][DISCOUNT_TYPE]"]:checked').val()!=2){
				$('input[name="data['+form.maintype+'][DISCOUNT]"]').removeAttr("readonly");
			}
			if($('input[name="data['+form.maintype+'][DISCOUNT_TYPE]"]:checked').val()==0){
				$('input[name="data['+form.maintype+'][DISCOUNT]"]').attr("maxlength",3);
			}
			if($('input[name="data['+form.maintype+'][DISCOUNT_TYPE]"]:checked').val()==1){
				$('input[name="data['+form.maintype+'][DISCOUNT]"]').attr("maxlength",15);
			}

			$('.imgcheck').mouseover(function(){

				for(i=0;i<form.dataformline+form.nowformline;i++){
					if($('input[name="data['+i+']['+form.type+'][QUANTITY]"]').val()!=''){
						$('input[name="data['+i+']['+form.type+'][QUANTITY]"]').val(form.unnumber_format($('input[name="data['+i+']['+form.type+'][QUANTITY]"]').val()));
					}
					if($('input[name="data['+i+']['+form.type+'][UNIT_PRICE]"]').val()!=''){
						$('input[name="data['+i+']['+form.type+'][UNIT_PRICE]"]').val(form.unnumber_format($('input[name="data['+i+']['+form.type+'][UNIT_PRICE]"]').val()));

					}
				}
				form.CHcheck=true;
			});
			$('.imgcheck').mouseout(function(){
				for(i=0;i<form.dataformline+form.nowformline;i++){
					if($('input[name="data['+i+']['+form.type+'][QUANTITY]"]').val()!=''){
						$('input[name="data['+i+']['+form.type+'][QUANTITY]"]').val(form.number_format($('input[name="data['+i+']['+form.type+'][QUANTITY]"]').val()));

					}
					if($('input[name="data['+i+']['+form.type+'][UNIT_PRICE]"]').val()!=''){
						$('input[name="data['+i+']['+form.type+'][UNIT_PRICE]"]').val(form.number_format($('input[name="data['+i+']['+form.type+'][UNIT_PRICE]"]').val()));
					}
				}
				form.CHcheck=false;
			});

			//数値入力時処理
			$('input').keyup(function(){
				//
				var this_name = $(this).attr('name');
				var match;

				if(match = this_name.match(form.reg1)){
					//あり
					form.f_row(match[1]);
					recalculation(form.maintype);
				}else{
					//なし
					form.f_subtotal();
				}

			});
			$('input').focus(function(){
				var line;
				if(line=$(this).attr('name').match(form.reg10)){
					form.focusline=line[1];
					focusLine(form.maintype);
				}
			});

			//企業変更時税率切り替え
			$('#'+form.maintype+'CSTID').change(function(){

				if($(this).val().match('^[0-9]+$')){
					var param = $('input[name="data['+form.maintype+']['+$(this).val()+']"]').val().split("-");
					if(param[0]){
						$('input[name="data['+form.maintype+'][EXCISE]"]').val([param[0]]);
					}
					if(param[1]){
						$('input[name="data['+form.maintype+'][FRACTION]"]').val([param[1]]);
					}
					form.f_subtotal();
				}else{
					var param = $('input[name="data['+form.maintype+'][default]"]').val().split("-");
					$('input[name="data['+form.maintype+'][EXCISE]"]').val([param[0]]);
					$('input[name="data['+form.maintype+'][FRACTION]"]').val([param[1]]);
					form.f_subtotal();
				}
			});

			//ラジオボタン選択時処理
			$('input[type="radio"]').click(function(){
				recalculation(form.maintype);
				var match;
				this.dataformline = $('input[name="data['+this.maintype+'][dataformline]"]').val() ? $('input[name="data['+this.maintype+'][dataformline]"]').val()*1 : 1;

				if(match = $(this).attr('name').match(form.reg5)){
					form.f_row(match[1]);
					form.f_subtotal();
				}else if($(this).attr('name').match(form.reg6)){

					/**
					 * 割引設定ラジオボタン押下時処理
					 */

					// 「設定しない」以外の場合に処理を行う
					if($(this).val() != 2){

						var str = -1;
						var different_tax_class_exists = false;

						for(var i = 0;i<this.dataformline+form.nowformline;i++){
                            var line_attribute = $('select[name="data['+ i +']['+form.type+'][LINE_ATTRIBUTE]').val();
                            if(line_attribute <5 ){
                                var tax_class = $('select[name="data['+ i +']['+form.type+'][TAX_CLASS]"]').val();

                                if(tax_class != 0){
                                    if(str>0 && tax_class != str){
                                        different_tax_class_exists = true;
                                        break;
                                    }
                                    str = tax_class;
                                }
                            }
						}
						if (different_tax_class_exists) {
							alert("全体割引は同一の税区分でのみ可能です。");
							$('input[name="data['+form.maintype+'][DISCOUNT_TYPE]"]').val([2]);
						}
                    }

					form.f_subtotal();
					if($(this).val()==1){
						$('input[name="data['+form.maintype+'][DISCOUNT]"]').attr("maxlength",15);
					}
					else if($(this).val()==0){
					$('input[name="data['+form.maintype+'][DISCOUNT]"]').val(form.unnumber_format($('input[name="data['+form.maintype+'][DISCOUNT]"]').val()).slice(0,3));
					$('input[name="data['+form.maintype+'][DISCOUNT]"]').attr("maxlength",3);
					}
				}else if($(this).attr('name').match(form.reg2)){
					form.f_rowall();
				}else if($(this).attr('name').match(form.reg15)){
					for(var i = 0;i<this.dataformline+form.nowformline;i++){
						if($('input[name="data['+i+']['+form.type+'][QUANTITY]"]').val().length!=0){
							var quantity = form.unnumber_format($('input[name="data['+i+']['+form.type+'][QUANTITY]"]').val());
							var stnum = quantity.indexOf('.');
							var dec = $('input[name="data['+form.maintype+'][DECIMAL_QUANTITY]"]:checked').val();
							if(stnum!=-1){
								$('input[name="data['+i+']['+form.type+'][QUANTITY]"]').val(fix_fraction(quantity,dec));
							}
						}
					}
				}else if($(this).attr('name').match(form.reg16)){
					for(var i = 0;i<this.dataformline+form.nowformline;i++){
						if($('input[name="data['+i+']['+form.type+'][UNIT_PRICE]"]').val().length!=0){
							var unitprice = form.unnumber_format($('input[name="data['+i+']['+form.type+'][UNIT_PRICE]"]').val());
							var stnum = unitprice.toString().indexOf('.');
							var dec = $('input[name="data['+form.maintype+'][DECIMAL_UNITPRICE]"]:checked').val();
							if(stnum!=-1){
								$('input[name="data['+i+']['+form.type+'][UNIT_PRICE]"]').val(fix_fraction(unitprice,dec));
							}
						}
					}
				}
                form.f_roweach();
			});

			//動的数字区切り切り替え処理
			$('input')
			.blur(function(){
				if(!form.CHcheck){
				//フォーカスが外れたタイミング
				var match;
				if(match = $(this).attr('name').match(form.reg3))
				{

					var unitprice = form.unnumber_format($('input[name="data['+match[1]+']['+form.type+'][UNIT_PRICE]"]').val());
					var stnum = unitprice.toString().indexOf('.');
					var dec = $('input[name="data['+form.maintype+'][DECIMAL_UNITPRICE]"]:checked').val();
					if(stnum!=-1){
						$('input[name="data['+match[1]+']['+form.type+'][UNIT_PRICE]"]').val(fix_fraction(unitprice,dec));
					}else{
						$('input[name="data['+match[1]+']['+form.type+'][UNIT_PRICE]"]').val(form.number_format(unitprice));
					}
					//数量、単価入力済み
					if($('input[name="data['+match[1]+']['+form.type+'][QUANTITY]"]').val().length!=0&&$('input[name="data['+match[1]+']['+form.type+'][UNIT_PRICE]"]').val().length!=0)
					{
						//再計算
						form.f_row(match[1]);
					}
                }
				if(match = $(this).attr('name').match(form.reg13))
				{

					var quantity = form.unnumber_format($('input[name="data['+match[1]+']['+form.type+'][QUANTITY]"]').val());
					var stnum = quantity.toString().indexOf('.');
					var dec = $('input[name="data['+form.maintype+'][DECIMAL_QUANTITY]"]:checked').val();

					if(stnum!=-1){
						$('input[name="data['+match[1]+']['+form.type+'][QUANTITY]"]').val(fix_fraction(quantity,dec));
					}else{
						$('input[name="data['+match[1]+']['+form.type+'][QUANTITY]"]').val(form.number_format(quantity));
					}
					//数量、単価入力済み
					if($('input[name="data['+match[1]+']['+form.type+'][QUANTITY]"]').val().length!=0&&$('input[name="data['+match[1]+']['+form.type+'][UNIT_PRICE]"]').val().length!=0)
					{
						//再計算
						form.f_row(match[1]);
					}
				}
				if(match = $(this).attr('name').match(form.reg12)){
					$('input[name="data['+form.maintype+'][DISCOUNT]"]').val(form.number_format($('input[name="data['+form.maintype+'][DISCOUNT]"]').val()));
				}
				}
			})
			.focus(function(){
				//フォーカスされたタイミング
				var match;
				if(match = $(this).attr('name').match(form.reg3)){
					if(form.IEcheck) {
						var caret = form.getCaret($('input[name="data['+match[1]+']['+form.type+'][UNIT_PRICE]"]').val().length);
						$('input[name="data['+match[1]+']['+form.type+'][UNIT_PRICE]"]').val(form.unnumber_format($('input[name="data['+match[1]+']['+form.type+'][UNIT_PRICE]"]').val()));
						form.setCaret(caret);
					}else{
						$('input[name="data['+match[1]+']['+form.type+'][UNIT_PRICE]"]').val(form.unnumber_format($('input[name="data['+match[1]+']['+form.type+'][UNIT_PRICE]"]').val()));
					}
				}
				if(match = $(this).attr('name').match(form.reg13)){
					if(form.IEcheck) {
						var caret = form.getCaret($('input[name="data['+match[1]+']['+form.type+'][QUANTITY]"]').val().length);
						$('input[name="data['+match[1]+']['+form.type+'][QUANTITY]"]').val(form.unnumber_format($('input[name="data['+match[1]+']['+form.type+'][QUANTITY]"]').val()));
						form.setCaret(caret);
					}else{
						$('input[name="data['+match[1]+']['+form.type+'][QUANTITY]"]').val(form.unnumber_format($('input[name="data['+match[1]+']['+form.type+'][QUANTITY]"]').val()));
					}
				}
				if(match = $(this).attr('name').match(form.reg12)){

					if(form.IEcheck) {
						var caret = form.getCaret($('input[name="data['+match[1]+']['+form.type+'][DISCOUNT]"]').val().length);
						$('input[name="data['+form.maintype+'][DISCOUNT]"]').val(form.unnumber_format($('input[name="data['+form.maintype+'][DISCOUNT]"]').val()));
						form.setCaret(caret);
					}else{
						$('input[name="data['+form.maintype+'][DISCOUNT]"]').val(form.unnumber_format($('input[name="data['+form.maintype+'][DISCOUNT]"]').val()));
					}
				}
			})
			.parents('form:first').submit(function(){
				var match;
				$('input').each(function(){

					if(match = $(this).attr('name').match(form.reg14))
					{
						$(this).val(form.unnumber_format($(this).val()));
					}
					else if(match = $(this).attr('name').match(form.reg13))
					{
						$(this).val(form.unnumber_format($(this).val()));
					}
					else if($(this).attr('name').match('^data\\['+form.maintype+'\\]\\[SUBTOTAL\\]$'))
					{
						$(this).val(form.unnumber_format($(this).val()));
					}
					else if($(this).attr('name').match('^data\\['+form.maintype+'\\]\\[SALES_TAX\\]$'))
					{
						$(this).val(form.unnumber_format($(this).val()));
					}
					else if($(this).attr('name').match('^data\\['+form.maintype+'\\]\\[TOTAL\\]$'))
					{
						$(this).val(form.unnumber_format($(this).val()));
					}
					else if($(this).attr('name').match('^data\\['+form.maintype+'\\]\\[DISCOUNT\\]$'))
					{
						$(this).val(form.unnumber_format($(this).val()));
					}
					else if($(this).attr('name').match('^data\\[([0-9]+)\\]\\['+form.type+'\\]\\[DISCOUNT\\]$'))
					{
						$(this).val(form.unnumber_format($(this).val()));
					}
					else if($(this).attr('name').match('^data\\['+form.maintype+'\\]\\[FIVE_RATE_TAX\\]$'))
					{
					  $(this).val(form.unnumber_format($(this).val()));
					}
					else if($(this).attr('name').match('^data\\['+form.maintype+'\\]\\[FIVE_RATE_TOTAL\\]$'))
					{
					  $(this).val(form.unnumber_format($(this).val()));
					}
					else if($(this).attr('name').match('^data\\['+form.maintype+'\\]\\[EIGHT_RATE_TAX\\]$'))
					{
					  $(this).val(form.unnumber_format($(this).val()));
					}
					else if($(this).attr('name').match('^data\\['+form.maintype+'\\]\\[EIGHT_RATE_TOTAL\\]$'))
					{
					  $(this).val(form.unnumber_format($(this).val()));
					}
					else if($(this).attr('name').match('^data\\['+form.maintype+'\\]\\[REDUCED_RATE_TAX\\]$'))
					{
					  $(this).val(form.unnumber_format($(this).val()));
					}
					else if($(this).attr('name').match('^data\\['+form.maintype+'\\]\\[REDUCED_RATE_TOTAL\\]$'))
					{
					  $(this).val(form.unnumber_format($(this).val()));
					}
					else if($(this).attr('name').match('^data\\['+form.maintype+'\\]\\[TEN_RATE_TAX\\]$'))
					{
					  $(this).val(form.unnumber_format($(this).val()));
					}
					else if($(this).attr('name').match('^data\\['+form.maintype+'\\]\\[TEN_RATE_TOTAL\\]$'))
					{
					  $(this).val(form.unnumber_format($(this).val()));
					}
				});
			})
			.end().blur();

			$('input[name="data['+form.maintype+'][SUBTOTAL]"]').val(form.number_format($('input[name="data['+form.maintype+'][SUBTOTAL]"]').val()));
			$('input[name="data['+form.maintype+'][SALES_TAX]"]').val(form.number_format($('input[name="data['+form.maintype+'][SALES_TAX]"]').val()));
			$('input[name="data['+form.maintype+'][TOTAL]"]').val(form.number_format($('input[name="data['+form.maintype+'][TOTAL]"]').val()));
			$('input[name="data['+form.maintype+'][FIVE_RATE_TAX]"]').val(form.number_format($('input[name="data['+form.maintype+'][FIVE_RATE_TAX]"]').val()));
			$('input[name="data['+form.maintype+'][FIVE_RATE_TOTAL]"]').val(form.number_format($('input[name="data['+form.maintype+'][FIVE_RATE_TOTAL]"]').val()));
			$('input[name="data['+form.maintype+'][EIGHT_RATE_TAX]"]').val(form.number_format($('input[name="data['+form.maintype+'][EIGHT_RATE_TAX]"]').val()));
			$('input[name="data['+form.maintype+'][EIGHT_RATE_TOTAL]"]').val(form.number_format($('input[name="data['+form.maintype+'][EIGHT_RATE_TOTAL]"]').val()));
			$('input[name="data['+form.maintype+'][REDUCED_RATE_TAX]"]').val(form.number_format($('input[name="data['+form.maintype+'][REDUCED_RATE_TAX]"]').val()));
			$('input[name="data['+form.maintype+'][REDUCED_RATE_TOTAL]"]').val(form.number_format($('input[name="data['+form.maintype+'][REDUCED_RATE_TOTAL]"]').val()));
			$('input[name="data['+form.maintype+'][TEN_RATE_TAX]"]').val(form.number_format($('input[name="data['+form.maintype+'][TEN_RATE_TAX]"]').val()));
			$('input[name="data['+form.maintype+'][TEN_RATE_TOTAL]"]').val(form.number_format($('input[name="data['+form.maintype+'][TEN_RATE_TOTAL]"]').val()));

		}

		if($('form').attr('class') && $('form').attr('class').match(/^(Company||Customer)$/)){

			//初期状態のチェック
			if($('input[name="data['+form.maintype+'][CUTOOFF_SELECT]"]:checked').val()==0){
				$('input[name="data['+form.maintype+'][CUTOOFF_DATE]"]').attr('readonly','readonly');
			}

			if($('input[name="data['+form.maintype+'][CUTOOFF_SELECT]"]:checked').val()==1){
				$('input[name="data['+form.maintype+'][CUTOOFF_DATE]"]').parent().removeClass("pay_hidden");
				$('input[name="data['+form.maintype+'][CUTOOFF_DATE]"]').removeAttr("readonly");
			}

			if($('input[name="data['+form.maintype+'][PAYMENT_SELECT]"]:checked').val()==0){
				$('input[name="data['+form.maintype+'][PAYMENT_DAY]"]').attr('readonly','readonly');
			}

			if($('input[name="data['+form.maintype+'][PAYMENT_SELECT]"]:checked').val()==1){
				$('input[name="data['+form.maintype+'][PAYMENT_DAY]"]').parent().removeClass("pay_hidden");
				$('input[name="data['+form.maintype+'][PAYMENT_DAY]"]').removeAttr("readonly");
			}

			//ラジオボタン選択時処理
			$('input[type="radio"]').click(function(){
				var match;
				if(match = $(this).attr('name').match(form.reg8)){
					if($('input[name="data['+form.maintype+'][CUTOOFF_SELECT]"]:checked').val()==1){
						$('input[name="data['+form.maintype+'][CUTOOFF_DATE]"]').parent().removeClass("pay_hidden");
						$('input[name="data['+form.maintype+'][CUTOOFF_DATE]"]').removeAttr("readonly");
					}
					if($('input[name="data['+form.maintype+'][CUTOOFF_SELECT]"]:checked').val()==0){
						$('input[name="data['+form.maintype+'][CUTOOFF_DATE]"]').val('');
						$('input[name="data['+form.maintype+'][CUTOOFF_DATE]"]').parent().addClass("pay_hidden");
						$('input[name="data['+form.maintype+'][CUTOOFF_DATE]"]').attr('readonly','readonly');
					}
				}
				if(match = $(this).attr('name').match(form.reg9)){
					if($('input[name="data['+form.maintype+'][PAYMENT_SELECT]"]:checked').val()==1){
						$('input[name="data['+form.maintype+'][PAYMENT_DAY]"]').parent().removeClass("pay_hidden");
						$('input[name="data['+form.maintype+'][PAYMENT_DAY]"]').removeAttr("readonly");
					}
					if($('input[name="data['+form.maintype+'][PAYMENT_SELECT]"]:checked').val()==0){
						$('input[name="data['+form.maintype+'][PAYMENT_DAY]"]').val('');
						$('input[name="data['+form.maintype+'][PAYMENT_DAY]"]').parent().addClass("pay_hidden");
						$('input[name="data['+form.maintype+'][PAYMENT_DAY]"]').attr('readonly','readonly');
					}
				}
			});
		}
		if($('form').attr('class') && $('form').attr('class').match(/^(Item)$/))
		{
			var reg11  = new RegExp('^data\\['+this.maintype+'\\]\\[(UNIT_PRICE)\\]$');

			$('.imgcheck').mouseover(function(){
				$('input[name="data['+form.maintype+'][UNIT_PRICE]"]').val(form.unnumber_format($('input[name="data['+form.maintype+'][UNIT_PRICE]"]').val()));
				form.CHcheck=true;
			});
			$('.imgcheck').mouseout(function(){
				$('input[name="data['+form.maintype+'][UNIT_PRICE]"]').val(form.number_format($('input[name="data['+form.maintype+'][UNIT_PRICE]"]').val()));
				form.CHcheck=false;
			});
			$('input')
			.blur(function(){
				if(!form.CHcheck){
					var match;
					if(match = $(this).attr('name').match(reg11)){
						var unitprice = form.unnumber_format($('input[name="data['+form.maintype+'][UNIT_PRICE]"]').val());
						var stnum = unitprice.toString().indexOf('.');
						var dec = 3;
						if(stnum!=-1){
							if(unitprice.toString().slice(stnum+1).length>dec){
								if(Number(unitprice)){
									$('input[name="data['+form.maintype+'][UNIT_PRICE]"]').val(fix_fraction(unitprice, dec));
//									$('input[name="data['+form.maintype+'][UNIT_PRICE]"]').val(form.number_format(Number(unitprice).toFixed(dec)));
								}
							}else{
								$('input[name="data['+form.maintype+'][UNIT_PRICE]"]').val(form.number_format(unitprice));
							}
						}else{
							$('input[name="data['+form.maintype+'][UNIT_PRICE]"]').val(form.number_format(unitprice));
						}
					}
				}
			})
			.focus(function(){
				var match;
				if(match = $(this).attr('name').match(reg11)){
					$('input[name="data['+form.maintype+'][UNIT_PRICE]"]').val(form.unnumber_format($('input[name="data['+form.maintype+'][UNIT_PRICE]"]').val()));
				}

			})
			.parents('form:first').submit(function(){
				var match;
				$('input').each(function(){
					if(match = $(this).attr('name').match(reg11))
					{
						$(this).val(form.unnumber_format($(this).val()));
					}
				});
			})
			.end().blur();
		}
		//メール画面の切り替え
		if($('input[name="data[smtp_frag]"]')){
			if($('input[name="data[smtp_frag]"]').val()==0){
				$('div.Smtpuse').hide();
			}
		}
		if($('form').attr('class') && $('form').attr('class').match(/^(Configuration)$/))
		{
			if($('select[name="data['+form.maintype+'][STATUS]"]').val()==0){
				$('div.Smtpuse').hide();
			}
			if($('input[name="data['+form.maintype+'][PROTOCOL]"]:checked').val()==0){
				$('input[name="data['+form.maintype+'][USER]"]').attr('readonly','readonly');
				$('input[name="data['+form.maintype+'][PASS]"]').attr('readonly','readonly');
			}

			$('select[name="data['+form.maintype+'][STATUS]"]').change(function(){
				if($('select[name="data['+form.maintype+'][STATUS]"]').val()==1){
					$('div.Smtpuse').slideDown();
					$('input[name="data['+form.maintype+'][PROTOCOL]"]').val(['0']);
					$('input[name="data['+form.maintype+'][SECURITY]"]').val(['0']);
				}
				if($('select[name="data['+form.maintype+'][STATUS]"]').val()==0){
					$('div.Smtpuse').slideUp();
					$('input[name="data['+form.maintype+'][PROTOCOL]"]').attr("checked",false);
					$('input[name="data['+form.maintype+'][SECURITY]"]').attr("checked",false);
					$('input[name="data['+form.maintype+'][HOST]"]').val(null);
					$('input[name="data['+form.maintype+'][PORT]"]').val(null);
					$('input[name="data['+form.maintype+'][USER]"]').val(null);
					$('input[name="data['+form.maintype+'][PASS]"]').val(null);
				}

			});

			$('input[name="data['+form.maintype+'][PROTOCOL]"]').change(function(){
				if($(this).val()==0){
					$('input[name="data['+form.maintype+'][USER]"]').attr('readonly','readonly');
					$('input[name="data['+form.maintype+'][PASS]"]').attr('readonly','readonly');
					$('input[name="data['+form.maintype+'][USER]"]').val(null);
					$('input[name="data['+form.maintype+'][PASS]"]').val(null);
				}
				if($(this).val()==1){
					$('input[name="data['+form.maintype+'][USER]"]').removeAttr('readonly');
					$('input[name="data['+form.maintype+'][PASS]"]').removeAttr('readonly');
				}
			});
		}
		if($('form').attr('class') && $('form').attr('class').match(/^(Totalbill)$/)){

			//初期状態チェック
			$('input[name="data[Totalbill][LASTM_BILL]"]').val(form.number_format($('input[name="data[Totalbill][LASTM_BILL]"]').val()));
			$('input[name="data[Totalbill][DEPOSIT]"]').val(form.number_format($('input[name="data[Totalbill][DEPOSIT]"]').val()));
			$('input[name="data[Totalbill][SALE]"]').val(form.number_format($('input[name="data[Totalbill][SALE]"]').val()));
			$('input[name="data[Totalbill][SALE_TAX]"]').val(form.number_format($('input[name="data[Totalbill][SALE_TAX]"]').val()));
			$('input[name="data[Totalbill][CARRY_BILL]"]').val(form.number_format($('input[name="data[Totalbill][CARRY_BILL]"]').val()));
			$('input[name="data[Totalbill][THISM_BILL]"]').val(form.number_format($('input[name="data[Totalbill][THISM_BILL]"]').val()));
			$('input[name="data[Totalbill][SUBTOTAL]"]').val(form.number_format($('input[name="data[Totalbill][SUBTOTAL]"]').val()));

			if($('input[name="data['+form.maintype+'][EDIT_STAT]"]').val()=='0'){
				$('.hidebox_d').html('');
				$('.hidebox_s').css('display','true');
			}
			if($('input[name="data['+form.maintype+'][EDIT_STAT]"]').val()=='1'){
				$('.hidebox_s').html('');
				$('.hidebox_d').css('display','true');
			}
			var lastm_bill=0;
			var deposit=0;
			var carry_bill=0;
			var sale=0;
			var sale_tax=0;
			var thism_bill=0;
			var subtotal=0;

			$('.hidebox_d input').blur(function(){
				var match;
				tb_reg1 = new RegExp('^data\\[Totalbill\\]\\[(LASTM_BILL)\\]$');
				tb_reg2 = new RegExp('^data\\[Totalbill\\]\\[(DEPOSIT)\\]$');

				lastm_bill=form.unnumber_format($('input[name="data[Totalbill][LASTM_BILL]"]').val());
				deposit=form.unnumber_format($('input[name="data[Totalbill][DEPOSIT]"]').val());
				carry_bill=form.unnumber_format($('input[name="data[Totalbill][CARRY_BILL]"]').val());
				sale=form.unnumber_format($('input[name="data[Totalbill][SALE]"]').val());
				sale_tax=form.unnumber_format($('input[name="data[Totalbill][SALE_TAX]"]').val());
				thism_bill=form.unnumber_format($('input[name="data[Totalbill][THISM_BILL]"]').val());

				if((lastm_bill).match(/^-?[0-9]+$/)!=null&&(deposit).match(/^-?[0-9]+$/)!=null
						&&(sale).match(/^-?[0-9]+$/)!=null&&(sale_tax).match(/^-?[0-9]+$/)!=null){
					carry_bill = lastm_bill-deposit;
					thism_bill = Number(sale) + Number(carry_bill);
				}
				$('input[name="data[Totalbill][LASTM_BILL]"]').val(form.number_format(lastm_bill));
				$('input[name="data[Totalbill][DEPOSIT]"]').val(form.number_format(deposit));
				$('input[name="data[Totalbill][SALE]"]').val(form.number_format(sale));
				$('input[name="data[Totalbill][SALE_TAX]"]').val(form.number_format(sale_tax));
				$('input[name="data[Totalbill][CARRY_BILL]"]').val(form.number_format(carry_bill));
				$('input[name="data[Totalbill][THISM_BILL]"]').val(form.number_format(thism_bill));
			})
			.focus(function(){
				$(this).val(form.unnumber_format($(this).val()));
			})
			.parents('form:first').submit(function(){
				var match;
					$('input[name="data[Totalbill][LASTM_BILL]"]').val(form.unnumber_format($('input[name="data[Totalbill][LASTM_BILL]"]').val()));
					$('input[name="data[Totalbill][DEPOSIT]"]').val(form.unnumber_format($('input[name="data[Totalbill][DEPOSIT]"]').val()));
					$('input[name="data[Totalbill][SALE]"]').val(form.unnumber_format($('input[name="data[Totalbill][SALE]"]').val()));
					$('input[name="data[Totalbill][SALE_TAX]"]').val(form.unnumber_format($('input[name="data[Totalbill][SALE_TAX]"]').val()));
					$('input[name="data[Totalbill][CARRY_BILL]"]').val(form.unnumber_format($('input[name="data[Totalbill][CARRY_BILL]"]').val()));
					$('input[name="data[Totalbill][THISM_BILL]"]').val(form.unnumber_format($('input[name="data[Totalbill][THISM_BILL]"]').val()));
			})
			.end().blur();
			$('.hidebox_s input').blur(function(){
				sale_tax=form.unnumber_format($('input[name="data[Totalbill][SALE_TAX]"]').val());
				thism_bill=form.unnumber_format($('input[name="data[Totalbill][THISM_BILL]"]').val());
				subtotal = form.unnumber_format($('input[name="data[Totalbill][SUBTOTAL]"]').val());

				if((subtotal).match(/^-?[0-9]+$/)!=null&&(sale_tax).match(/^-?[0-9]+$/)!=null
						&&(thism_bill).match(/^-?[0-9]+$/)!=null){
				}
				$('input[name="data[Totalbill][SALE_TAX]"]').val(form.number_format(sale_tax));
				$('input[name="data[Totalbill][THISM_BILL]"]').val(form.number_format(thism_bill));
				$('input[name="data[Totalbill][SUBTOTAL]"]').val(form.number_format(subtotal));
			})
			.focus(function(){
				$(this).val(form.unnumber_format($(this).val()));
			})
			.parents('form:first').submit(function(){
				var match;
				$('input[name="data[Totalbill][SALE_TAX]"]').val(form.unnumber_format($('input[name="data[Totalbill][SALE_TAX]"]').val()));
				$('input[name="data[Totalbill][THISM_BILL]"]').val(form.unnumber_format($('input[name="data[Totalbill][THISM_BILL]"]').val()));
				$('input[name="data[Totalbill][SUBTOTAL]"]').val(form.unnumber_format($('input[name="data[Totalbill][SUBTOTAL]"]').val()));
			})
			.end().blur();
		}



		//今日の日付の取得処理
		$(".nowtime").click(function () {
			var date = new Date();

			var year = date.getFullYear();          // 指定年
			var mon  = date.getMonth()+1;             // 指定月
			mon= "0"+mon;
			mon = mon.substr(mon.length - 2, mon.length);
			var today = +date.getDate();             // 指定日
			today= "0"+today;
			today = today.substr(today.length - 2, today.length);
			var sdate = year.toString()+"-"+mon.toString()+"-"+today.toString();
			$(this).parents().children('.date').val(sdate);
			return false;
		});
		//今日の日付の取得処理
		$(".cleartime").click(function () {
			$(this).parents().children('.date').val(null);
			return false;
		});

		//各税率の合計、消費税表示
		var tax_kind_count = 0;
		if($('input[name="data['+this.maintype+'][FIVE_RATE_TOTAL]"]').val() == 0){
			$('#five_rate_tax').css('display', 'none');
		}else{
			tax_kind_count++;
		}

		if($('input[name="data['+this.maintype+'][EIGHT_RATE_TOTAL]"]').val() == 0){
			$('#eight_rate_tax').css('display', 'none');
		}else{
			tax_kind_count++;
		}

		if($('input[name="data['+this.maintype+'][REDUCED_RATE_TOTAL]"]').val() == 0){
			$('#reduced_rate_tax').css('display', 'none');
		}else{
			tax_kind_count++;
		}

		if($('input[name="data['+this.maintype+'][TEN_RATE_TOTAL]"]').val() == 0){
			$('#ten_rate_tax').css('display', 'none');
		}else{
			tax_kind_count++;
		}
		// 複数ある場合のみ各税率箇所を表示
		if(tax_kind_count < 2){
			$('#every_tax_table').css('display', 'none');
		}

	};//init

	// 税率の取得
	this.getTaxRate = function(no) {
		var tax_class = $('select[name="data['+ no +']['+ this.maintype +'item][TAX_CLASS]"]').val();
		return this.tax_rates[tax_class];
	};

	// 発行日時から税率の設定
	// this.setRateByIssueDate = function() {
	// 	var date = $("input.cal.date").val();
	// 	for(var i = 0; i < this.tax_rates.length; i++) {
	// 		if(this.tax_rates[i]["Tax"]["START_DATE"] < date) currentRate = this.tax_rates[i]["Tax"]["TAX_ID"];
	// 	}
	// 	$("#rate").val(currentRate);
	// };

	// 発行日時から税率・税区分のデフォルト値を取得
	this.getRateByIssueDate = function () {
		var currentRate;
		var issue_date = $("input.cal.date").val();
		//IE8対応
		issue_date = issue_date.replace(/-/g, '/');

		var excise = $('input[name="data['+this.maintype+'][EXCISE]"]:checked').val();

		$.each(form.tax_operation_date, function(per, dates) {

			//IE8 対応
			dates["start"] = dates["start"].replace(/-/, '/').replace(/-/, '/');
			if(Date.parse(dates["start"]) <= Date.parse(issue_date)) {
				var prefix = "";
				if(Number(excise) != 3 && per > 5) prefix = per;
				currentRate = prefix + "" + excise;
			}
		});



		return currentRate;
	};


	this.h = function (ch){
	    ch = ch.replace(/"/g,"\\\"") ;
	    ch = ch.replace(/\//g,"\\/") ;
		return ch;
	};

	/**
	 * 全体割引きの処理
	 * 前提：全て同一の税区分
	 */
	this.discount_all = function(total, fraction){
		var all_dis_amount = Number(this.unnumber_format($('input[name="data['+this.maintype+'][DISCOUNT]"]').val()));
		if($('input[name="data['+this.maintype+'][DISCOUNT_TYPE]"]:radio:checked').val() == 0){
			total = this.f_fraction(fraction, (Number(total) * (100 - Number(all_dis_amount)) / 100));
		}else if($('input[name="data['+this.maintype+'][DISCOUNT_TYPE]"]:radio:checked').val() == 1){
			total = total - all_dis_amount;
		}
		return total;
	}

	this.f_total = function(){
		const taxTypes = ['5','8','r8','10'];
		// 端数処理・税区分
		const fraction = $('input[name="data['+this.maintype+'][FRACTION]"]:checked').val();
		const taxFraction = $('input[name="data['+this.maintype+'][TAX_FRACTION]"]:checked').val();
		const taxFractionTiming = $('input[name="data['+this.maintype+'][TAX_FRACTION_TIMING]"]:checked').val();

		let amounts = {}
		let eachTaxTotal = {}
		for(let type in taxTypes){
			amounts[taxTypes[type]] = [];
			eachTaxTotal[taxTypes[type]] = {count: 0, amount: 0, tax: 0};
		}
		let noneTaxTotal = 0;

		for(var i = 0; true; i++){
			//終了条件
			if(!$('input[name="data['+i+']['+this.type+'][AMOUNT]"]').attr('name')){
				break;
			}

			// 行属性取得
			const tmpAttribute = $('select[name="data['+ i +']['+ this.maintype +'item][LINE_ATTRIBUTE]"]').val();
			// 税区分取得
			const tmpNextAttribute = $('select[name="data['+ eval(Number(i) + 1) +']['+ this.maintype +'item][LINE_ATTRIBUTE]"]').val();
			// 税区分取得
			const tmpTaxClass = $('select[name="data['+ i +']['+ this.maintype +'item][TAX_CLASS]"]').val();
			// 税率
			const taxRate = form.getTaxRate(i);
			let taxRateInteger = taxRate * 100; // 消費税を整数化

			if(tmpTaxClass == 91 || tmpTaxClass == 92){
				taxRateInteger = 'r' + taxRateInteger;
			}

			// 次の行が割引行の場合
			let discount = 0;
			if(tmpNextAttribute == 3 || tmpNextAttribute == 4){
				discount = Number(form.unnumber_format($('input[name="data['+ eval(Number(i) + 1) +']['+ this.type +'][AMOUNT]"]').val()));
			}
			
			// 通常行の処理
			if(tmpAttribute == 0) {
				// 金額取得
				amount = Math.multiply(this.unnumber_format($('input[name="data['+i+']['+this.type+'][QUANTITY]"]').val()), this.unnumber_format($('input[name="data['+i+']['+this.type+'][UNIT_PRICE]"]').val()));

				// 端数処理
				amount = this.f_fraction(fraction, amount);
				amount = amount + discount;

				//内税
				if(tmpTaxClass.match(/1$/)) {
					amounts[taxRateInteger].push({amount : amount, inTaxFlg : true});
				//外税					
				} else if (tmpTaxClass.match(/2$/)){
					amounts[taxRateInteger].push({amount : amount, inTaxFlg : false});
				} else if (tmpTaxClass.match(/3$/)){
					noneTaxTotal += amount;
				}
			}
		}

		let total = 0;
		let taxTotal = 0;
		let beforeTax = 0;

		let taxTypeCount = 0;

		// 消費税端数計算が帳票単位の場合
		if(taxFractionTiming==0){
			for(let i in taxTypes){
				const nowTax = taxTypes[i];
				let nowTagInteger = +nowTax;
				if(nowTax == 'r8'){
					nowTagInteger=8;
				}
				let inTaxTotal = 0;
				let outTaxTotal = 0;

				if(amounts[nowTax].length > 0){
					let taxTypes = [];
					for(let k in amounts[nowTax]){
						const line = amounts[nowTax][k];
						if(line['inTaxFlg'] == true && !taxTypes.includes('intax')){
							taxTypes.push('intax');
						} else if(line['inTaxFlg'] == false && !taxTypes.includes('outtax')){
							taxTypes.push('outtax');
						}
					}

					// 消費税区分が1種類の場合
					if(taxTypes.length == 1 && taxTypes.includes('outtax')){
						// 外税の場合
						if(taxTypes.includes('outtax')){
							for(let k in amounts[nowTax]){
								const line = amounts[nowTax][k];
								outTaxTotal += line['amount'];
							}

							outTaxTotal = this.discount_all(outTaxTotal, fraction);
							const outTax = this.f_fraction(taxFraction, outTaxTotal * (nowTagInteger ) / 100);

							eachTaxTotal[nowTax]['amount'] = outTaxTotal + outTax;
							eachTaxTotal[nowTax]['tax'] = outTax;
							eachTaxTotal[nowTax]['count'] = amounts[nowTax].length;
						// 内税の場合
						} else if(taxTypes.includes('intax')){
							for(let k in amounts[nowTax]){
								const line = amounts[nowTax][k];
								inTaxTotal += line['amount'];
							}
							inTaxTotal = this.discount_all(inTaxTotal);
							eachTaxTotal[nowTax]['amount'] = inTaxTotal;
							eachTaxTotal[nowTax]['tax'] = this.f_fraction(taxFraction, eachTaxTotal[nowTax]['amount'] * nowTagInteger / (nowTagInteger + 100));
							eachTaxTotal[nowTax]['count'] = amounts[nowTax].length;
						}
					// 内税外税混在の場合（もしくは内税のみの場合）は外税を内税化する際の処理は基本端数処理で行う
					} else {
						for(let k in amounts[nowTax]){
							const line = amounts[nowTax][k];
							if(line['inTaxFlg'] == true){
								inTaxTotal += line['amount'];
							} else if(line['inTaxFlg'] == false){
								outTaxTotal += line['amount'];
							}
						}
						const outTaxTotalWithTotal = this.f_fraction(fraction, outTaxTotal * (nowTagInteger + 100) / 100);

						eachTaxTotal[nowTax]['amount'] = inTaxTotal + outTaxTotalWithTotal;
						eachTaxTotal[nowTax]['amount'] = this.discount_all(eachTaxTotal[nowTax]['amount'], fraction);
						const taxAmount = eachTaxTotal[nowTax]['amount'] * nowTagInteger / (nowTagInteger + 100)
						eachTaxTotal[nowTax]['tax'] = this.f_fraction(taxFraction, taxAmount);

						if(Math.floor(taxAmount) <= eachTaxTotal[nowTax]['tax']){
							eachTaxTotal[nowTax]['amount'] += eachTaxTotal[nowTax]['tax'] - Math.floor(taxAmount);
						}
						eachTaxTotal[nowTax]['count'] = amounts[nowTax].length;
					}
				}
			}
		} else if(taxFractionTiming==1){
			for(let i in taxTypes){
				const nowTax = taxTypes[i];
				let nowTagInteger = +nowTax;
				if(nowTax == 'r8'){
					nowTagInteger=8;
				}

				if(amounts[nowTax].length > 0){
					for(let k in amounts[nowTax]){
						const line = amounts[nowTax][k];

						if(line['inTaxFlg'] == true){
							const calcTax = this.f_fraction(taxFraction, line['amount'] * nowTagInteger / (100 + +nowTagInteger) );
							eachTaxTotal[nowTax]['amount'] += line['amount'];
							eachTaxTotal[nowTax]['tax'] += calcTax;
						} else if(line['inTaxFlg'] == false){
							const calcTax = this.f_fraction(taxFraction, (line['amount'] * nowTagInteger / 100));
							eachTaxTotal[nowTax]['amount'] += (+line['amount'] + calcTax);
							eachTaxTotal[nowTax]['tax'] += calcTax;
						}
					}
					eachTaxTotal[nowTax]['count'] = amounts[nowTax].length;
				}
			}
		}

		for(let i in taxTypes){
			const nowTax = taxTypes[i];
			if(eachTaxTotal[nowTax]['count'] > 0){
				taxTypeCount ++;
				total += eachTaxTotal[nowTax]['amount'];
				taxTotal += eachTaxTotal[nowTax]['tax'];
			}
		}
		beforeTax = total - taxTotal;

		if(noneTaxTotal > 0){
			total = total + noneTaxTotal;
			beforeTax = beforeTax + noneTaxTotal;
		}

		$('input[name="data['+this.maintype+'][TOTAL]"]').val(this.number_format(total));
		$('input[name="data['+this.maintype+'][SALES_TAX]"]').val(this.number_format(taxTotal));
		$('input[name="data['+this.maintype+'][SUBTOTAL]"]').val(this.number_format(beforeTax));

		// 5%
		$('input[name="data['+this.maintype+'][FIVE_RATE_TAX]"]').val(this.number_format(eachTaxTotal['5']['tax']));
		$('input[name="data['+this.maintype+'][FIVE_RATE_TOTAL]"]').val(this.number_format(eachTaxTotal['5']['amount']));
		// 8%
		$('input[name="data['+this.maintype+'][EIGHT_RATE_TAX]"]').val(this.number_format(eachTaxTotal['8']['tax']));
		$('input[name="data['+this.maintype+'][EIGHT_RATE_TOTAL]"]').val(this.number_format(eachTaxTotal['8']['amount']));
		// 8%軽減
		$('input[name="data['+this.maintype+'][REDUCED_RATE_TAX]"]').val(this.number_format(eachTaxTotal['r8']['tax']));
		$('input[name="data['+this.maintype+'][REDUCED_RATE_TOTAL]"]').val(this.number_format(eachTaxTotal['r8']['amount']));
		// 10%
		$('input[name="data['+this.maintype+'][TEN_RATE_TAX]"]').val(this.number_format(eachTaxTotal['10']['tax']));
		$('input[name="data['+this.maintype+'][TEN_RATE_TOTAL]"]').val(this.number_format(eachTaxTotal['10']['amount']));

		if(taxTypeCount >= 1){
			if(eachTaxTotal['5']['count'] > 0){
				$('#five_rate_tax').show();
			} else {
				$('#five_rate_tax').hide();
			}
			if(eachTaxTotal['8']['count'] > 0){
				$('#eight_rate_tax').show();
			} else {
				$('#eight_rate_tax').hide();
			}
			if(eachTaxTotal['r8']['count'] > 0){
				$('#reduced_rate_tax').show();
			} else {
				$('#reduced_rate_tax').hide();
			}
			if(eachTaxTotal['10']['count'] > 0){
				$('#ten_rate_tax').show();
			} else {
				$('#ten_rate_tax').hide();
			}
			$('#every_tax_table').show();
		} else {
			$('#every_tax_table').hide();
		}
	}

	//小計計算用メンバ関数
	this.f_subtotal = function(){
		this.f_total();
	};

	Math._getDecimalLength = function(value) {
	    var list = (value + '').split('.'), result = 0;
	    if (list[1] !== undefined && list[1].length > 0) {
	        result = list[1].length;
	    }
	    return result;
	};


	/**
	 * 乗算処理
	 *
	 * value1, value2から小数点を取り除き、整数値のみで乗算を行う。
	 * その後、小数点の桁数Nの数だけ10^Nで除算する
	 */
	Math.multiply = function(value1, value2) {
	    var intValue1 = +(value1 + '').replace('.', ''),
	        intValue2 = +(value2 + '').replace('.', ''),
	        decimalLength = Math._getDecimalLength(value1) + Math._getDecimalLength(value2),
	        result;

	    result = (intValue1 * intValue2) / Math.pow(10, decimalLength);

	    return result;
	};


	this.f_row = function(_no){
		//列の合計


		var amount = Math.multiply(this.unnumber_format($('input[name="data['+_no+']['+this.type+'][QUANTITY]"]').val()), this.unnumber_format($('input[name="data['+_no+']['+this.type+'][UNIT_PRICE]"]').val()));
		amount = amount ? amount : 0;
		var fraction = $('input[name="data['+this.maintype+'][FRACTION]"]:checked').val();

		//端数処理
		amount = this.f_fraction(fraction, amount);

		if($('input[name="data['+_no+']['+this.type+'][QUANTITY]"]').val()==''){
			amount = '';
		}
		if($('input[name="data['+_no+']['+this.type+'][UNIT_PRICE]"]').val()==''){
			amount = '';
		}

		$('input[name="data['+_no+']['+this.type+'][AMOUNT]"]').val(this.number_format(amount)).css('color', '#000');
		this.f_subtotal();
	};

    this.f_roweach = function(){
		var cnt = this.nowformline +1 ;

        for(var i=0;i<cnt;i++){
            var row = i.toString();
            			//終了条件
			if($('input[name="data['+i+']['+this.type+'][AMOUNT]"]').val()==''){
				continue;
			} else if ($('select[name="data['+ i +']['+ this.maintype +'item][LINE_ATTRIBUTE]"]').val() != 0){
                continue;
            }
            this.f_row(row);
        }
        recalculation(form.maintype);
    };

	this.f_rowall = function(){

		var cnt = this.nowformline + 1;

		recalculation(form.maintype);

		this.f_subtotal();
	};



	//数字区切り変換用メンバ関数
	this.number_format = function(_num){
		if(!_num) return _num;
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
	this.unnumber_format = function(_num){
		if(!_num) return _num;
		for(i=0;_num.toString().charAt(0)=='0';i++){
			if(_num.toString().length==1||_num.toString().charAt(1)=='.'){
				break;
			}
			_num=_num.toString().slice(1);
		}
		return _num.toString().replace( /,/g , '');
	};


	// キャレットの位置を取得するメンバ関数
	this.getCaret = function(_length){
		var range = document.selection.createRange();
		range.moveStart( "character", - _length);
		var caret_position = range.text.length;
		caret_position = caret_position - Math.floor( caret_position / 4);

		return caret_position;
	}

	// キャレットの位置を設定するメンバ関数
	this.setCaret = function(_caret){
		var range = document.selection.createRange();
		range.collapse();
		range.moveEnd(   "character", _caret );
		range.moveStart( "character", _caret );
		range.select()

	}



	//端数処理用メンバ関数
	this.f_fraction = function(_fraction, _no){
		//金額
		if(_fraction == 0){
			//切上げ
			return Math.ceil(_no);
		}else if(_fraction == 1){
			//切り捨て
			return Math.floor(_no);
		}else{
			//四捨五入
			return Math.round(_no);
		}
	};

	//内税のための端数処理逆メンバ関数
	this.f_fraction_opposite = function(_tax_fraction, _no){
		//金額
		if(_tax_fraction == 0){
			//消費税が切上げの場合は切り下げる
			return Math.floor(_no);
		}else if(_tax_fraction == 1){
			//消費税が切下げの場合は切り上げる
			return Math.ceil(_no);
		}else{
			//四捨五入はそのまま計算
			return Math.round(_no);
		}
	};



	this.f_cleateline = function(_no, _val)
	{
		this.bt_del;
		this.bt_set;
		this.bt_can;
		var bt_select   = $('#INSERT_ITEM_IMG0 a img').attr('src');
		var bt_up       = $('tr.row_0 td img.btn_up').attr('src');
		var bt_down     = $('tr.row_0 td img.btn_down').attr('src');
		var html;
		var excise = $('input[name="data['+this.maintype+'][EXCISE]"]:radio:checked').val();
		html = '<td><a href="#"><img src="'+this.bt_del+'" alt="×" onclick="return form.f_delline('+_no+');"><a></td>';
		html += '<td><input type="text" id="'+this.maintype+'Name" name="data['+_no+']['+this.type+'][ITEM_NO]" '    + 'value="'+(_val['ITEM_NO']    ? _val['ITEM_NO']    :"")+'" ' + 'class="w31" maxlength="2"></td>';
		html += '<td><input type="text" id="'+this.maintype+'Name" name="data['+_no+']['+this.type+'][ITEM_CODE]" '  + 'value="'+(_val['ITEM_CODE']  ? _val['ITEM_CODE']  :"")+'" ' + 'class="w64" maxlength="8"></td>';
		html += '<td><input type="text" id="'+this.maintype+'Name" name="data['+_no+']['+this.type+'][ITEM]" '       + 'value="'+(_val['ITEM']       ? _val['ITEM']       :"")+'" ' + 'class="w120" maxlength="25" >';
		html += ' <span id="INSERT_ITEM_IMG'+ _no +'" ><a href="#"><img src="' + bt_select + '" alt="商品選択" onclick="form.focusline = '+ _no +';focusLine();return popupclass.popupajax(\'select_item\');" style="margin: 0px 0px 2px;"/></a></span></td>';
		html += '<td><input type="text" id="'+this.maintype+'Name" name="data['+_no+']['+this.type+'][QUANTITY]" '   + 'value="'+(_val['QUANTITY']   ? _val['QUANTITY']   :"")+'" ' + 'class="w63" maxlength="7" onkeyup="recalculation(\''+this.maintype+'\');"></td>';
		html += '<td><input type="text" id="'+this.maintype+'Name" name="data['+_no+']['+this.type+'][UNIT]" '       + 'value="'+(_val['UNIT']       ? _val['UNIT']       :"")+'" ' + 'class="w45" maxlength="4"></td>';
		html += '<td><input type="text" id="'+this.maintype+'Name" name="data['+_no+']['+this.type+'][UNIT_PRICE]" ' + 'value="'+(_val['UNIT_PRICE'] ? _val['UNIT_PRICE'] :"")+'" ' + 'class="w73" maxlength="9" onkeyup="recalculation(\''+this.maintype+'\');"></td>';
		html += '<td><input type="text" id="'+this.maintype+'Name" name="data['+_no+']['+this.type+'][AMOUNT]" '     + 'value="'+(_val['AMOUNT']     ? _val['AMOUNT']     :"")+'" ' + 'class="w103" readonly="" onChange="recalculation(\''+this.maintype+'\');" onkeyup="recalculation(\''+this.maintype+'\');"></td>';
		html += '<td><select name="data['+ _no +']['+this.type+'][LINE_ATTRIBUTE]" class="w103" onChange="changeAttribute(\''+this.maintype+'\','+ _no +', value);" style="display: inline" >';
		html += '<option value="0">通常</option>';
		html += '<option value="1">小計</option>';
		html += '<option value="2">グループ小計</option>';
		html += '<option value="3">割引(円)</option>';
		html += '<option value="4">割引(％)</option>';
		html += '<option value="5">備考</option>';
		html += '<option value="8">改ページ</option>';
		html += '</select> ';

		html += '<select name="data['+ _no +']['+this.type+'][TAX_CLASS]" class="w105" onchange="changeTaxClass(\''+this.maintype+'\','+ _no +', value);" style="display: inline">';
		html += '<option value="0">------</option>';

		var currentRate = this.getRateByIssueDate();

		$.each(form.tax_rates_option, function(index, tax_class) {
			html += '<option value="' + tax_class["key"] + '"';
			if(tax_class["key"] == currentRate){
				html += 'selected = "selected"';
			}
			html += '>' + tax_class["name"] + '</option>';
		});

		html += '</select>';

		html += '<input type="hidden" name="data['+_no+']['+this.type+'][DISCOUNT]" />';
		html += '<input type="hidden" name="data['+_no+']['+this.type+'][DISCOUNT_TYPE]" /></td>';
		html += '<td><a href="javascript:void(0);"><img src="'+ bt_up +'" onclick="form.focusline='+ _no +';form.f_up();"></a><br />';
		html += '<a href="javascript:void(0);"><img src="'+ bt_down +'" onclick="form.focusline='+ _no +';form.f_down();"></a></td>';

		$(".row_" + _no).hover(
				function (){
					$(':text[name*="data[' + _no + ']"]').addClass('hoverLine');
				},
				function (){
					$(':text[name*="data[' + _no + ']"]').removeClass('hoverLine');
				}
		);


		return html;

	};

	this.f_cleateraw = function(_no)
	{
		var html;
		html  = '<tr class="row_'+ _no +'">';
		html += '</tr>';
		return html;

	};


	//１列ずらして減らす処理
	this.f_delshift = function(_no){
		for(var i=_no;i<this.nowformline;i++)
		{
			var array = new Array();

			$('tr.row_'+ (i+1) +' td input').each(function()
			{
				var match;
				match = $(this).attr('name').match(form.reg7);
				array[match[1]] = $(this).val();
			});

			$('tr.row_'+ i).html(this.f_cleateline(i, array));
			$('tr.row_'+ i +' td input').each(function()
			{
				var this_name = $(this).attr('name');
				var match;
				if(match = this_name.match(form.reg1))
				{
					//数値入力時処理
					$(this).keyup(function()
					{
						//あり
						form.f_row(match[1]);
					});
					$(this).blur(function(){
						if(!form.CHcheck){
						var match;

						//単価
						if(match = $(this).attr('name').match(form.reg3))
						{
							//数量、単価入力済み
							if($('input[name="data['+match[1]+']['+form.type+'][QUANTITY]"]').val().length!=0&&$('input[name="data['+match[1]+']['+form.type+'][UNIT_PRICE]"]').val().length!=0)
							{
								//再計算
								form.f_row(match[1]);
							}

							//小数点処理
							var unitprice = form.unnumber_format($('input[name="data['+match[1]+']['+form.type+'][UNIT_PRICE]"]').val());
							var stnum = unitprice.toString().indexOf('.');
							var dec = $('input[name="data['+form.maintype+'][DECIMAL_UNITPRICE]"]:checked').val();

							if(stnum!=-1)
							{
								var chconma = unitprice.toString().slice(stnum+1).indexOf('.');
								if(chconma==-1)
								{
									if(unitprice.toString().slice(stnum+1).length>dec)
									{
										if(Number(unitprice)){
											$('input[name="data['+match[1]+']['+form.type+'][UNIT_PRICE]"]').val(fix_fraction(unitprice, dec));
//											$('input[name="data['+match[1]+']['+form.type+'][UNIT_PRICE]"]').val(form.number_format(Number(unitprice).toFixed(dec)));
										}
									}
									else
									{
										$('input[name="data['+match[1]+']['+form.type+'][UNIT_PRICE]"]').val(form.number_format(unitprice));
									}
								}
							}
							else
							{
								$('input[name="data['+match[1]+']['+form.type+'][UNIT_PRICE]"]').val(form.number_format(unitprice));
							}


							var lineAttribute = $('select[name="data['+ match[1] +']['+ form.type +'item][LINE_ATTRIBUTE]"]').val();




						}

						//数量
						if(match = $(this).attr('name').match(form.reg13))
						{
							//数量、単価入力済み
							if($('input[name="data['+match[1]+']['+form.type+'][QUANTITY]"]').val().length!=0&&$('input[name="data['+match[1]+']['+form.type+'][UNIT_PRICE]"]').val().length!=0)
							{
								//再計算
								form.f_row(match[1]);
							}

							//小数点処理
							var quantity = form.unnumber_format($('input[name="data['+match[1]+']['+form.type+'][QUANTITY]"]').val());
							var stnum = quantity.toString().indexOf('.');
							var dec = $('input[name="data['+form.maintype+'][DECIMAL_QUANTITY]"]:checked').val();
							if(stnum!=-1){
								var chconma = quantity.toString().slice(stnum+1).indexOf('.');
								if(chconma==-1){
									if(quantity.toString().slice(stnum+1).length>dec)
									{
										if(Number(quantity)){
											$('input[name="data['+match[1]+']['+form.type+'][QUANTITY]"]').val(fix_fraction(quantity, dec));
//											$('input[name="data['+match[1]+']['+form.type+'][QUANTITY]"]').val(form.number_format(Number(quantity).toFixed(dec)));
										}
									}
									else
									{
										$('input[name="data['+match[1]+']['+form.type+'][QUANTITY]"]').val(form.number_format(quantity));
									}
								}
							}
							else
							{
								$('input[name="data['+match[1]+']['+form.type+'][QUANTITY]"]').val(form.number_format(quantity));
							}
						}
						$('input[name="data['+form.maintype+'][SUBTOTAL]"]').val(form.number_format($('input[name="data['+form.maintype+'][SUBTOTAL]"]').val()));
						$('input[name="data['+form.maintype+'][SALES_TAX]"]').val(form.number_format($('input[name="data['+form.maintype+'][SALES_TAX]"]').val()));
						$('input[name="data['+form.maintype+'][TOTAL]"]').val(form.number_format($('input[name="data['+form.maintype+'][TOTAL]"]').val()));
						}
					})
					.focus(function(){
						var match;
						if(match = $(this).attr('name').match(form.reg3))
						{
							$('input[name="data['+match[1]+']['+form.type+'][UNIT_PRICE]"]').val(form.unnumber_format($('input[name="data['+match[1]+']['+form.type+'][UNIT_PRICE]"]').val()));
						}
						if(match = $(this).attr('name').match(form.reg13))
						{
							$('input[name="data['+match[1]+']['+form.type+'][QUANTITY]"]').val(form.unnumber_format($('input[name="data['+match[1]+']['+form.type+'][QUANTITY]"]').val()));
						}
					});
				}
				$(this).focus(function(){
					var line;
					if(line=$(this).attr('name').match(form.reg10)){
						form.focusline=line[1];
						focusLine(form.maintype);
					}
				});
			});
		}
	};



	this.f_addline = function(_no){

			//行の追加処理
			if(this.IEcheck)
			{
				$('tr.row_'+(++this.nowformline)).css({display : 'block'});
			}
			else
			{
				$('tr.row_'+(++this.nowformline)).css({display : 'table-row'});
			}

			if(_no != null)
			{
				this.f_addshift(_no);
			}
			else
			{
				$('tr.row_'+(this.nowformline-1)).after(this.f_cleateraw(this.nowformline));
				$('tr.row_'+(this.nowformline)).html(this.f_cleateline(this.nowformline, new Array()));

			}
			$('tr.row_'+ this.nowformline +' td input').each(function()
			{
				var this_name = $(this).attr('name');
				var match;
				if(match = this_name.match(form.reg1))
				{
					//数値入力時処理
					$(this).keyup(function(){
						//あり
						form.f_row(match[1]);
						recalculation(form.maintype);
					});
					$(this).blur(function(){
						if(!form.CHcheck){
						var match;

						//単価
						if(match = $(this).attr('name').match(form.reg3))
						{
							//数量、単価入力済み
							if($('input[name="data['+match[1]+']['+form.type+'][QUANTITY]"]').val().length!=0&&$('input[name="data['+match[1]+']['+form.type+'][UNIT_PRICE]"]').val().length!=0)
							{
								//再計算
								form.f_row(match[1]);
							}

							//小数点処理
							var unitprice = form.unnumber_format($('input[name="data['+match[1]+']['+form.type+'][UNIT_PRICE]"]').val());
							var stnum = unitprice.toString().indexOf('.');
							var dec = $('input[name="data['+form.maintype+'][DECIMAL_UNITPRICE]"]:checked').val();

							if(stnum!=-1)
							{
								var chconma = unitprice.toString().slice(stnum+1).indexOf('.');
								if(chconma==-1)
								{
									if(unitprice.toString().slice(stnum+1).length>dec)
									{
										if(Number(unitprice)){
											$('input[name="data['+match[1]+']['+form.type+'][UNIT_PRICE]"]').val(fix_fraction(unitprice,dec));
//											$('input[name="data['+match[1]+']['+form.type+'][UNIT_PRICE]"]').val(form.number_format(Number(unitprice).toFixed(dec)));
										}
									}
									else
									{
										$('input[name="data['+match[1]+']['+form.type+'][UNIT_PRICE]"]').val(form.number_format(unitprice));
									}
								}
							}
							else
							{
								$('input[name="data['+match[1]+']['+form.type+'][UNIT_PRICE]"]').val(form.number_format(unitprice));
							}
						}

						//数量
						if(match = $(this).attr('name').match(form.reg13))
						{
							//数量、単価入力済み
							if($('input[name="data['+match[1]+']['+form.type+'][QUANTITY]"]').val().length!=0&&$('input[name="data['+match[1]+']['+form.type+'][UNIT_PRICE]"]').val().length!=0)
							{
								//再計算
								form.f_row(match[1]);
							}

							//小数点処理
							var quantity = form.unnumber_format($('input[name="data['+match[1]+']['+form.type+'][QUANTITY]"]').val());
							var stnum = quantity.toString().indexOf('.');
							var dec = $('input[name="data['+form.maintype+'][DECIMAL_QUANTITY]"]:checked').val();
							if(stnum!=-1)
							{
								var chconma = quantity.toString().slice(stnum+1).indexOf('.');
								if(chconma==-1)
								{
									if(quantity.toString().slice(stnum+1).length>dec)
									{
										if(Number(quantity)){
											$('input[name="data['+match[1]+']['+form.type+'][QUANTITY]"]').val(fix_fraction(quantity,dec));
//											$('input[name="data['+match[1]+']['+form.type+'][QUANTITY]"]').val(form.number_format(Number(quantity).toFixed(dec)));
										}
									}
									else
									{
										$('input[name="data['+match[1]+']['+form.type+'][QUANTITY]"]').val(form.number_format(quantity));
									}
								}
							}
							else
							{
								$('input[name="data['+match[1]+']['+form.type+'][QUANTITY]"]').val(form.number_format(quantity));
							}

						}

						$('input[name="data['+form.maintype+'][SUBTOTAL]"]').val(form.number_format($('input[name="data['+form.maintype+'][SUBTOTAL]"]').val()));
						$('input[name="data['+form.maintype+'][SALES_TAX]"]').val(form.number_format($('input[name="data['+form.maintype+'][SALES_TAX]"]').val()));
						$('input[name="data['+form.maintype+'][TOTAL]"]').val(form.number_format($('input[name="data['+form.maintype+'][TOTAL]"]').val()));
						}
					})
					.focus(function(){
						var match;
						if(match = $(this).attr('name').match(form.reg3))
						{
							if(form.IEcheck) {
								var caret = form.getCaret($('input[name="data['+match[1]+']['+form.type+'][UNIT_PRICE]"]').val().length);
								$('input[name="data['+match[1]+']['+form.type+'][UNIT_PRICE]"]').val(form.unnumber_format($('input[name="data['+match[1]+']['+form.type+'][UNIT_PRICE]"]').val()));
								form.setCaret(caret);
							}else{
								$('input[name="data['+match[1]+']['+form.type+'][UNIT_PRICE]"]').val(form.unnumber_format($('input[name="data['+match[1]+']['+form.type+'][UNIT_PRICE]"]').val()));
							}
						}
						if(match = $(this).attr('name').match(form.reg13))
						{
							if(form.IEcheck) {
								var caret = form.getCaret($('input[name="data['+match[1]+']['+form.type+'][QUANTITY]"]').val().length);
								$('input[name="data['+match[1]+']['+form.type+'][QUANTITY]"]').val(form.unnumber_format($('input[name="data['+match[1]+']['+form.type+'][QUANTITY]"]').val()));
								form.setCaret(caret);
							}else{
								$('input[name="data['+match[1]+']['+form.type+'][QUANTITY]"]').val(form.unnumber_format($('input[name="data['+match[1]+']['+form.type+'][QUANTITY]"]').val()));
							}
						}
					});
				}
				$(this).focus(function(){
					var line;
					if(line=$(this).attr('name').match(form.reg10)){
						form.focusline=line[1];
						focusLine(form.maintype);
					}
				});
			});

		this.f_subtotal();
		return false;
	};



	this.f_delline = function(_no){
		if(this.nowformline - 1 >= 0){
			//行の削除処理

			if(_no != null)
			{
				var tmpFocus = form.focusline;
				form.focusline = _no;
				for(var i = _no; i < form.nowformline; i++) {
					this.f_down();
				}


			}

			$('tr.row_'+this.nowformline).remove();
			setReadOnly(form.maintype);
			form.focusline = tmpFocus;
			$('tr.row_'+(this.nowformline--)).css({display : 'none'});
		}

		this.f_subtotal();
		recalculation(form.maintype);
		setReadOnly(form.maintype);
		return false;
	};

	//送付状送付書類行追加
	this.coverpage_addline = function(_no){
		if(this.nowformline + 1 < this.maxformline)
		{
			var row_num = ++this.nowformline;
			this.bt_del;
			this.bt_set;
			this.bt_can;

			var html;
			html  = '<td style="width: 170px;"></td>';
			html += '<td style="width: 42px;"><a href="#"><img src="'+this.bt_del+'" alt="×" onclick="return form.coverpage_delline('+row_num+');"></a></td>';
			html += '<td style="width: 250px;"><input class="documenttitle" type="text" style="width: 250px;" maxlength = "15" name="data['+row_num+'][Reports][DOCUMENT_TITLE]"></td>';
			html += '<td style="width: 100px;"><input class="documentnumber" type="text" style="width: 80px;" maxlength = "7" name="data['+row_num+'][Reports][DOCUMENT_NUMBER]">&nbsp;部</td>';
			html += '<td style="width: 318px;">&nbsp;</td>';

			//行の追加処理
			if(this.IEcheck)
			{
				$('tr[class="row_'+row_num+'"]').css({display : 'block'}).html(html);
			}
			else
			{
				$('tr[class="row_'+row_num+'"]').css({display : 'table-row'}).html(html);
			}
		}
		return false;
	};
	//デリート時上シフト
	this.coverpage_shiftline = function(_no){

			for(var i = _no; i < this.maxformline-1; i++){
				$('tr[class="row_'+i+'"] input[class="documenttitle"]').val($('tr[class="row_'+(i+1)+'"] input[class="documenttitle"]').val());
				$('tr[class="row_'+i+'"] input[class="documentnumber"]').val($('tr[class="row_'+(i+1)+'"] input[class="documentnumber"]').val());
			}
		return false;
	};
	//行の削除
	this.coverpage_delline = function(_no){
		if(this.nowformline > 0){
			this.coverpage_shiftline(_no);
			$('#SEND_DOCUMENT *').removeClass("error");
			$('tr[class="row_'+this.nowformline--+'"]').css({display : 'none'});

		}
			return false;
	};





	//行データのdisable処理
	this.f_reset = function(_no){
		var check = confirm("明細内容がリセットされます。よろしいですか？");
		if(check){
			if(!isNaN(_no)){
			}else {
				for(var i = 0;i<this.nowformline+1;i++)
				{
					$('tr.row_'+ i +' td input').each(function()
					{
						$(this).val('');
					});

					$('tr.row_'+ i +' td select').each(function()
					{
						$(this).val(0);
					});

					$('.add_'+i).removeClass('hidden');
					$('.del_'+i).addClass('hidden');

					$('select[name="data['+ i +']['+form.type+'][TAX_CLASS]"]').each(function() {
						var excise = $('input[name="data['+form.maintype+'][EXCISE]"]:radio:checked').val();
						$('select[name="data['+ i +']['+form.type+'][TAX_CLASS]"]').val(++excise);
					});
				}
				this.f_subtotal();
			}
			setReadOnly(form.maintype);
			form.focusline = null;
			focusLine(form.maintype);
			return false;

		}else {
			return false;
		}

	};

	this.f_additem = function(){
		var itemno=$('.popupSelectitem').val();
		if(itemno!="default"&&form.focusline>-1){
			var data = eval("(" + $('#itemlist').text() + ")");
			var dec = $('input[name="data['+form.maintype+'][DECIMAL_UNITPRICE]"]:checked').val();
			$('input[name="data['+form.focusline+']['+form.type+'][ITEM]"]').css('color', '#000').val(data[itemno].ITEM);
			$('input[name="data['+form.focusline+']['+form.type+'][ITEM_CODE]"]').css('color', '#000').val(data[itemno].ITEM_CODE);
			$('input[name="data['+form.focusline+']['+form.type+'][UNIT]"]').css('color', '#000').val(data[itemno].UNIT);
			$('select[name="data['+form.focusline+']['+form.type+'][LINE_ATTRIBUTE]"]').val(0);

			var excise = $('input[name="data['+form.maintype+'][EXCISE]"]:radio:checked').val();
			$('select[name="data['+form.focusline+']['+form.type+'][TAX_CLASS]"]').val(++excise);

			var unitprice = data[itemno].UNIT_PRICE;
			var stnum = unitprice.toString().indexOf('.');
			if(stnum!=-1){
				if(unitprice.toString().slice(stnum+1).length>dec){
					$('input[name="data['+form.focusline+']['+form.type+'][UNIT_PRICE]"]').css('color', '#000').val(fix_fraction(data[itemno].UNIT_PRICE,dec));
//					$('input[name="data['+form.focusline+']['+form.type+'][UNIT_PRICE]"]').css('color', '#000').val(this.number_format(Number(data[itemno].UNIT_PRICE).toFixed(dec)));
				}
				else{
					$('input[name="data['+form.focusline+']['+form.type+'][UNIT_PRICE]"]').css('color', '#000').val(this.number_format(data[itemno].UNIT_PRICE));
				}
				form.f_row(form.focusline);
			}
			else{
				$('input[name="data['+form.focusline+']['+form.type+'][UNIT_PRICE]"]').css('color', '#000').val(this.number_format(Number(data[itemno].UNIT_PRICE)));
				form.f_row(form.focusline);
			}
		}
		setReadOnly(form.maintype);
		focusLine(form.maintype);
		return false;
	};

	//行の移動(上)
	this.f_up = function(insert){
		var detailTable = document.getElementById("detail_table");		//table参照
		var startRow = 1;												//帳票明細入力が開始する行
		var temp;

		if(form.focusline > 0){

			//入力内容の移動
			for(var i = startRow; i < detailTable.rows[1].cells.length - 1; i++) {
				if(i == 8) {
					temp = detailTable.rows[eval(startRow) + eval(form.focusline) + 1].cells[i].getElementsByTagName("select")[0].value;
					detailTable.rows[eval(startRow) + eval(form.focusline) + 1].cells[i].getElementsByTagName("select")[0].value = detailTable.rows[eval(startRow) + eval(form.focusline)].cells[i].getElementsByTagName("select")[0].value;
					detailTable.rows[eval(startRow) + eval(form.focusline)].cells[i].getElementsByTagName("select")[0].value = temp;

					temp = detailTable.rows[eval(startRow) + eval(form.focusline) + 1].cells[i].getElementsByTagName("select")[1].value;
					detailTable.rows[eval(startRow) + eval(form.focusline) + 1].cells[i].getElementsByTagName("select")[1].value = detailTable.rows[eval(startRow) + eval(form.focusline)].cells[i].getElementsByTagName("select")[1].value;
					detailTable.rows[eval(startRow) + eval(form.focusline)].cells[i].getElementsByTagName("select")[1].value = temp;

					continue;
				}

				temp = detailTable.rows[eval(startRow) + eval(form.focusline) + 1].cells[i].getElementsByTagName("input")[0].value;
				detailTable.rows[eval(startRow) + eval(form.focusline) + 1].cells[i].getElementsByTagName("input")[0].value = detailTable.rows[eval(startRow) + eval(form.focusline)].cells[i].getElementsByTagName("input")[0].value;
				detailTable.rows[eval(startRow) + eval(form.focusline)].cells[i].getElementsByTagName("input")[0].value = temp;
			}

			form.focusline--;
			if(insert == 'insert'){

			}else {
				focusLine(form.maintype);
			}
		}
	};

	//行の移動(下)
	this.f_down = function(){
		var detailTable = document.getElementById("detail_table");		//table参照
		var startRow = 1;												//帳票明細入力が開始する行
		var temp;


		if(form.focusline < form.nowformline) {

			//入力内容の移動
			for(var i = startRow; i < detailTable.rows[1].cells.length - 1; i++) {
				if(i == 8) {
					temp = detailTable.rows[eval(startRow) + eval(form.focusline) + 2].cells[i].getElementsByTagName("select")[0].value;
					detailTable.rows[eval(startRow) + eval(form.focusline) + 2].cells[i].getElementsByTagName("select")[0].value = detailTable.rows[eval(startRow) + eval(form.focusline) + 1].cells[i].getElementsByTagName("select")[0].value;
					detailTable.rows[eval(startRow) + eval(form.focusline) + 1].cells[i].getElementsByTagName("select")[0].value = temp;

					temp = detailTable.rows[eval(startRow) + eval(form.focusline) + 2].cells[i].getElementsByTagName("select")[1].value;
					detailTable.rows[eval(startRow) + eval(form.focusline) + 2].cells[i].getElementsByTagName("select")[1].value = detailTable.rows[eval(startRow) + eval(form.focusline) + 1].cells[i].getElementsByTagName("select")[1].value;
					detailTable.rows[eval(startRow) + eval(form.focusline) + 1].cells[i].getElementsByTagName("select")[1].value = temp;

					continue;
				}

				temp = detailTable.rows[eval(startRow) + eval(form.focusline) + 2].cells[i].getElementsByTagName("input")[0].value;
				detailTable.rows[eval(startRow) + eval(form.focusline) + 2].cells[i].getElementsByTagName("input")[0].value = detailTable.rows[eval(startRow) + eval(form.focusline) + 1].cells[i].getElementsByTagName("input")[0].value;
				detailTable.rows[eval(startRow) + eval(form.focusline) + 1].cells[i].getElementsByTagName("input")[0].value = temp;
			}

			form.focusline++;
			focusLine(form.maintype);
		}
	};

	//行の挿入
	this.f_insert = function(type){
		if(!type) {
			type = 0; // 通常
		}

		form.f_addline(null);
		var tmpFocus = form.focusline;
		form.focusline = form.nowformline;

		// 未フォーカス時は tmpFocus が undefined なので補正する.
		switch (Number(type)) {
		case 8:
			// 改ページ
			if (!tmpFocus) {
				tmpFocus = form.focusline - 1;
			}
			break;
		}

		for(var i = tmpFocus; i < form.nowformline; i++) {
			this.f_up('insert');
		}

		// 挿入した行に対して、行属性変更.
		switch (Number(type)) {
		case 8:
			// 改ページ
			$('select[name="data['+ tmpFocus +']['+ form.maintype +'item][LINE_ATTRIBUTE]"]').val(8);
			changeAttribute(form.maintype, tmpFocus, 8);
			break;
		}

		form.focusline = eval(tmpFocus) + 1;
		focusLine(form.maintype);
		return false;
	};





};

$(function(){
    $('input[name="submit"]').click(function(){

        setTimeout(function(){
            $('input[name="submit"]').attr('disabled','disabled');
        },1);

    });
});



//削除の確認
function del() {

	cnum=new Array(20);
	for(i=0;i<$(".chk:checked").length;i++){
		cnum[i]=$(".chk:checked:eq("+i+")").attr('name').match('([0-9]+)');
		if($('.auth'+cnum[i][0]).text()){
			if($('.auth'+cnum[i][0]).text()==0){
				alert("削除できない項目が含まれています");
				return false;
			}
		}
	}
	if (confirm("本当に削除をしてもよろしいですか？")){

		//削除
		return true;
	} else {

		//キャンセル
		return false;
	}
}

//削除の確認
function status_change() {

	cnum=new Array(20);
	for(i=0;i<$(".chk:checked").length;i++){
		cnum[i]=$(".chk:checked:eq("+i+")").attr('name').match('([0-9]+)');
		if($('.auth'+cnum[i][0]).text()){
			if($('.auth'+cnum[i][0]).text()==0){
				alert("ステータス変更できない項目が含まれています");
				return false;
			}
		}
	}
	if (confirm("本当にステータス変更をしてもよろしいですか？")){

		//ステータス変更
		return true;
	} else {

		//キャンセル
		return false;
	}
}

//送信の確認
function sendmail() {
	if (confirm("本当に送信をしてもよろしいですか？"))
	{
		//削除
		return true;
	}
	else
	{
		//キャンセル
		return false;
	}
}

//残り文字数のカウント
function count_str(id, str, max) {
	if(max - str.length >= 0) {
		$('#'+id).html('(あと'+(max - str.length) +'文字)').removeClass('str_rest');
	}else {
		$('#'+id).addClass('str_rest').html(max + '文字を超えています');
	}
}

// 残り文字数カウント（全角：1文字、半角：0.5文字）
function count_strw(id, str, max) {
	// 文字数をカウント
	var len = 0;
	for(i = 0; i < str.length; i++){
		var c = str.charCodeAt(i);
		if ( (c >= 0x0 && c < 0x81) || (c == 0xf8f0)
				|| (c >= 0xff61 && c < 0xffa0)
				|| (c >= 0xf8f1 && c < 0xf8f4)) {
			len += 0.5;
		} else {
			len += 1;
		}
	}

	// 判定
	if(max - len >= 0) {
		$('#'+id).html('(あと'+(max - len) +'文字)').removeClass('str_rest');
	}else {
		$('#'+id).addClass('str_rest').html(max + '文字を超えています');
	}
}

//すべてのチェックボックスを選択
function select_all() {
	$(".chk").attr('checked', 'checked');
}

//すべてのチェックボックスを選択
function release_all() {
	$(".chk").attr('checked', '');
}



//フォーカスされている行の背景を色付け
function focusLine(type) {
	var ie = (function(){
	    var undef, v = 3, div = document.createElement('div');
	    while (
	        div.innerHTML = '<!--[if gt IE '+(++v)+']><i></i><![endif]-->',
	        div.getElementsByTagName('i')[0]
	    );
	    return v> 4 ? v : undef;
	}());


	$(':text').removeClass('focusLine focusReadOnly');
	$(':text[name*="data['+ form.focusline +']"]').addClass('focusLine');

	if(ie == 8) {
		$(':text[name*="data['+ form.focusline +']"][readonly="readonly"]').addClass('focusReadOnly');
	}else {
		$(':text[name*="data['+ form.focusline +']"][readonly=""]').addClass('focusReadOnly');
		$(':text[name*="data['+ form.focusline +']"][readonly]').addClass('focusReadOnly');
	}


	recalculation(form.maintype);

}



////////////////////////////////
///////ここから行属性関連///////
////////////////////////////////


//行を空にする
function resetLine(type, no) {
	$(':text[name*="data['+ no +']"]').val('');

}

//readonly属性を加える
function addReadOnly(type, no) {
	$(':text[name*="data['+ no +']"]').attr("readonly","readonly");
}

//readonly属性を取り除く
function removeReadOnly(type, no) {
	$(':text[name*="data['+ no +']"]').removeAttr("readonly");
}

//各行のreadonly属性を再設定
function setReadOnly(type) {

	var tempAttribute = 0;

	for(var i  = 0; i <= form.nowformline; i++) {
		tempAttribute = $('select[name="data['+ i +']['+ type +'item][LINE_ATTRIBUTE]"]').val();

		switch(Number(tempAttribute)) {
		case 0:		//通常
			removeReadOnly(type, i);
			$('input[name="data['+ i +']['+ type +'item][AMOUNT]"]').attr("readonly", "readonly");
			break;

		case 1:		//小計
			addReadOnly(type, i);
			break;

		case 2:		//グループ小計
			addReadOnly(type, i);
			break;

		case 3:		//割引(円)
			addReadOnly(type, i);
			$('input[name="data['+ i +']['+ type +'item][ITEM]"]').removeAttr("readonly");
			$('input[name="data['+ i +']['+ type +'item][AMOUNT]"]').removeAttr("readonly");

			break;

		case 4:		//割引(％)
			addReadOnly(type, i);
			$('input[name="data['+ i +']['+ type +'item][ITEM]"]').removeAttr("readonly");
			$('input[name="data['+ i +']['+ type +'item][QUANTITY]"]').removeAttr("readonly");
			break;

		case 5:		//備考
			addReadOnly(type, i);
			$('input[name="data['+ i +']['+ type +'item][ITEM]"]').removeAttr("readonly");
			break;

		case 8:		//改ページ
			addReadOnly(type, i);
			break;
		}
	}
}

//税区分設定
function setTaxClass(type, no, value) {
	var excise = $('input[name="data['+type+'][EXCISE]"]:radio:checked').val();

	switch(Number(value)) {
	case 0:		//通常時処理
		$('select[name="data['+ no +']['+ type +'item][TAX_CLASS]"]').val(eval(Number(excise) + 1));
		break;

	case 3:		//割引(円)
	case 4:		//割引(％)
		if(no > 0 && $('select[name="data['+ (no - 1) +']['+ type +'item][LINE_ATTRIBUTE]"]').val() == 0) {
			$('select[name="data['+ no +']['+ type +'item][TAX_CLASS]"]').val(
				$('select[name="data['+ (no - 1) +']['+ type +'item][TAX_CLASS]"]').val()
			);
		}else {
			$('select[name="data['+ no +']['+ type +'item][TAX_CLASS]"]').val(eval(Number(excise) + 1));
		}
		break;

	case 1:		//小計処理
	case 2:		//グループ小計
	case 5:		//備考
	case 8:		//改ページ
		$('select[name="data['+ no +']['+ type +'item][TAX_CLASS]"]').val(0);
		break;
	}

}

//税区分変更時
function changeTaxClass(type, no, value) {
	var tmpAttr = $('select[name="data['+ no +']['+ type +'item][LINE_ATTRIBUTE]"]').val();
	var excise = $('input[name="data['+type+'][EXCISE]"]:radio:checked').val();

	switch(Number(value)) {
	case 0:		//なし
        if($('input[name="data['+type+'][DISCOUNT_TYPE]"]:radio:checked').val() != 2){
			alert('全体割引設定後は税区分の変更はできません。');
            $('select[name="data['+ no +']['+ type +'item][TAX_CLASS]"]').val(0);
        } else if(tmpAttr == 0 || tmpAttr == 3 || tmpAttr == 4 ){
			alert('税区分を選択してください。');
			 $('select[name="data['+ no +']['+ type +'item][TAX_CLASS]"]').val(++excise);
		}
		break;

	case 1:		//内税
	case 2:		//外税
	case 3:		//非課税
	case 81:	//内税(8%)
	case 82:	//外税(8%)
	case 91:	//軽減税率内税(8%)
	case 92:	//軽減税率外税(8%)
	case 101:	//内税(10%)
	case 102:	//外税(10%)

		if(tmpAttr == 1 || tmpAttr == 2 || tmpAttr == 5 || tmpAttr == 8 ){
			alert('税区分は通常行・割引行でのみ設定できます。');
            $('select[name="data['+ no +']['+ type +'item][TAX_CLASS]"]').val(0);
		} else if($('input[name="data['+type+'][DISCOUNT_TYPE]"]:radio:checked').val() != 2){
			alert('全体割引設定後は税区分の変更はできません。');
            $('select[name="data['+ no +']['+ type +'item][TAX_CLASS]"]').val(0);
        }
		break;

	}
	form.focusline = no;
	focusLine(type);
}

//行の属性変更時
function changeAttribute(type, no, value) {

	switch(Number(value)) {
	case 0:		//通常時処理
		resetLine(type, no);
		setReadOnly(type);
		setTaxClass(type, no, value);

		break;

	case 1:		//小計処理
		resetLine(type, no);
		setReadOnly(type);
		setTaxClass(type, no, value);
		calcSubtotal(type, no);
		break;

	case 2:		//グループ小計
		resetLine(type, no);
		setReadOnly(type);
		setTaxClass(type, no, value);
		calcGroupSubtotal(type, no);
		break;

	case 3:		//割引(円)
		resetLine(type, no);
		setReadOnly(type);
		setTaxClass(type, no, value);
		$('input[name="data['+ no +']['+ type +'item][ITEM]"]').val('　(割引)');
		$('input[name="data['+ no +']['+ type +'item][AMOUNT]"]').val('-0');

		break;
	case 4:		//割引(％)
		resetLine(type, no);
		setReadOnly(type);
		setTaxClass(type, no, value);
		$('input[name="data['+ no +']['+ type +'item][ITEM]"]').val('　(割引)');
		$('input[name="data['+ no +']['+ type +'item][UNIT]"]').val('％');
		break;

	case 5:		//備考
		resetLine(type, no);
		setReadOnly(type);
		setTaxClass(type, no, value);
		$('input[name="data['+ no +']['+ type +'item][ITEM]"]').val('　(備考)');
		break;

	case 8:		//改ページ
		resetLine(type, no);
		setReadOnly(type);
		setTaxClass(type, no, value);
		$('input[name="data['+ no +']['+ type +'item][ITEM]"]').val('　(改ページ)');
		break;
	}
	toggleInsertImg(no, value);
	form.focusline = no;
	focusLine(type);
}

//アイテム挿入画像表示切替
function toggleInsertImg(no, value) {

	switch(Number(value)) {
	case 0:		//通常時処理
		$("#INSERT_ITEM_IMG"+ no +"").show();
		break;

	case 1:		//小計処理
		$("#INSERT_ITEM_IMG"+ no +"").hide();
		break;

	case 2:		//グループ小計
		$("#INSERT_ITEM_IMG"+ no +"").hide();
		break;

	case 3:		//割引(円)
		$("#INSERT_ITEM_IMG"+ no +"").hide();
		break;

	case 4:		//割引(％)
		$("#INSERT_ITEM_IMG"+ no +"").hide();
		break;

	case 5:		//備考
		$("#INSERT_ITEM_IMG"+ no +"").hide();
		break;

	case 8:		//改ページ
		$("#INSERT_ITEM_IMG"+ no +"").hide();
		break;
	}

}

//小計計算
function calcSubtotal(type, no) {
	var subtotal     = 0;
	var tmpAmount    = 0;
	var tmpAttribute = 0;

	//商品名を【小計】に
	$('input[name="data['+ no +']['+ type +'item][ITEM]"]').val('　(小計)');

	//小計を計算
	for(var i = 0; i < no; i++) {
		tmpAttribute = $('select[name="data['+ i +']['+ type +'item][LINE_ATTRIBUTE]"]').val()
		tmpAmount    = Number(form.unnumber_format($('input[name="data['+ i +']['+ type +'item][AMOUNT]"]').val()));

		//金額がないもの、行属性が小計・グループ小計のものは無視
		if(tmpAmount != 0 && tmpAttribute != 1 && tmpAttribute != 2) {
			subtotal += tmpAmount;
		}
	}

	//小計表示
	$('input[name="data['+ no +']['+ type +'item][AMOUNT]"]').val(form.number_format(subtotal));
}

//グループ小計計算
function calcGroupSubtotal(type, no) {
	var subtotal     = 0;
	var tmpAmount    = 0;
	var tmpAttribute = 0;

	//商品名を【グループ小計】に
	$('input[name="data['+ no +']['+ type +'item][ITEM]"]').val('　(グループ小計)');

	//小計を計算
	for(var i = 0; i < no; i++) {
		tmpAttribute = $('select[name="data['+ i +']['+ type +'item][LINE_ATTRIBUTE]"]').val()
		tmpAmount    = Number(form.unnumber_format($('input[name="data['+ i +']['+ type +'item][AMOUNT]"]').val()));

		//以前の行にグループ小計があれば金額を0に戻す
		if(tmpAttribute == 2) {
			subtotal = 0;
		}
		//金額がないもの、行属性が小計のものは無視
		else if(tmpAmount != 0 && tmpAttribute != 1) {
			subtotal += tmpAmount;
		}
	}

	//小計表示
	$('input[name="data['+ no +']['+ type +'item][AMOUNT]"]').val(form.number_format(subtotal));
}


//税抜価格計算
function calcBeforeTax(type, no, attr, taxClass, discountFlg) {
	var amount = Number(form.unnumber_format($('input[name="data['+ no +']['+ type +'item][AMOUNT]"]').val()));
	var discount = 0;
	var tax_rate = form.getTaxRate(no);



	//次の行が割引の場合
	if(discountFlg) {
		discount = Number(form.unnumber_format($('input[name="data['+ eval(Number(no) + 1) +']['+ type +'item][AMOUNT]"]').val()));

		//内税
		if(taxClass.match(/1$/)) {
			return (amount + discount) * 100 / (tax_rate*100 + 100);
		//外税・非課税
		}else {
			return (amount + discount);
		}
	}

	//次の行が割引でない場合
	else {
		//内税
		if(taxClass.match(/1$/)) {
			return (amount) * 100 / (tax_rate*100 + 100);
		//外税・非課税
		}else {
			return (amount);
		}
	}
}

//消費税額計算
function calcTax(type, no, attr, taxClass, discountFlg) {
	var amount = Number(form.unnumber_format($('input[name="data['+ no +']['+ type +'item][AMOUNT]"]').val()));
	var discount = 0;
	var tax_rate = form.getTaxRate(no);

	//次の行が割引の場合
	if(discountFlg) {
		discount = Number(form.unnumber_format($('input[name="data['+ eval(Number(no) + 1) +']['+ type +'item][AMOUNT]"]').val()));

		//内税
		if(taxClass.match(/1$/)) {
			return (amount + discount) *  tax_rate*100 / (tax_rate*100 + 100);
		//外税
		}else if(taxClass.match(/2$/)){
			return (amount + discount) * tax_rate*100 / 100;
		//非課税
		}else {
			return 0;
		}
	}

	//次の行が割引でない場合
	else {
		//内税
		if(taxClass.match(/1$/)) {
			return (amount) * tax_rate*100 / (tax_rate*100 + 100);
		//外税
		}else if(taxClass.match(/2$/)){
			return (amount) * tax_rate*100 / 100;
		//非課税
		}else {
			return 0;
		}
	}
}

//税込金額計算
function calcIncludingTax(type, no, attr, taxClass, discountFlg) {
	var amount = Number(form.unnumber_format($('input[name="data['+ no +']['+ type +'item][AMOUNT]"]').val()));
	var discount = 0;
	var tax_rate = form.getTaxRate(no);

	//次の行が割引の場合
	if(discountFlg) {
		discount = Number(form.unnumber_format($('input[name="data['+ eval(Number(no) + 1) +']['+ type +'item][AMOUNT]"]').val()));

		//内税
		if(taxClass.match(/1$/)) {
			return (amount + discount);
		//外税
		}else if(taxClass.match(/2$/)){
			return (amount + discount) * (tax_rate*100 + 100) / 100;
		//非課税
		}else {
			return (amount + discount);
		}
	}

	//次の行が割引でない場合
	else {
		//内税
		if(taxClass.match(/1$/)) {
			return (amount);
		//外税
		}else if(taxClass.match(/2$/)){
			return (amount) * (tax_rate*100 + 100) / 100;
		//非課税
		}else {
			return (amount);
		}
	}
}

//金額変更時再計算
function recalculation(type) {

	var tmpAttribute = 0;

	for(var i = 0; i <= form.nowformline; i++) {
		tmpAttribute = $('select[name="data['+ i +']['+ type +'item][LINE_ATTRIBUTE]"]').val();
		toggleInsertImg(i, tmpAttribute);

		//小計
		if(tmpAttribute == 1) {
			calcSubtotal(type, i);


		//グループ小計
		}else if(tmpAttribute == 2) {
			calcGroupSubtotal(type, i);


		//割引（円）
		}else if(tmpAttribute == 3) {

			setTaxClass(type, i, tmpAttribute);
			if($('select[name="data['+ (i - 1) +']['+ type +'item][LINE_ATTRIBUTE]"]').val() != 0) {
				$('input[name="data['+ i +']['+ type +'item][AMOUNT]"]').val(0);
			}

			if($('input[name="data['+ i +']['+ type +'item][AMOUNT]"]').val() == '-') {
				continue;
			}else if($('input[name="data['+ i +']['+ type +'item][AMOUNT]"]').val() == 0) {
				continue;
			}

			var amount    = Number(form.unnumber_format($('input[name="data['+ i +']['+ type +'item][AMOUNT]"]').val()));
			if(!isNaN(amount)) {
				if(amount > 0) {
					amount  = -amount;
				}
				$('input[name="data['+ i +']['+ type +'item][AMOUNT]"]').val(form.number_format(amount));

			}else{
				$('input[name="data['+ i +']['+ type +'item][AMOUNT]"]').val('-');
			}


		//割引（％）
		}else if(tmpAttribute == 4) {

			setTaxClass(type, i, tmpAttribute);
			if(i > 0){
				var prevAttr  = $('select[name="data['+ (i - 1) +']['+ type +'item][LINE_ATTRIBUTE]"]').val();
				var dec = $('input[name="data['+ type +'][DECIMAL_UNITPRICE]"]:checked').val();

				if(Number(prevAttr) == 0) {
					var prevAmount    = Number(form.unnumber_format($('input[name="data['+ (i - 1) +']['+ type +'item][AMOUNT]"]').val()));
					var per = Number(form.unnumber_format($('input[name="data['+ i +']['+ type +'item][QUANTITY]"]').val()));
					$('input[name="data['+ i +']['+ type +'item][AMOUNT]"]').val('-' + form.number_format(form.f_fraction($('input[name="data['+ type +'][FRACTION]"]:checked').val(), prevAmount * per / 100)));
				}else{
					$('input[name="data['+ i +']['+ type +'item][AMOUNT]"]').val(0);
				}
			}else {
				$('input[name="data['+ i +']['+ type +'item][AMOUNT]"]').val(0);
			}
		}
	}



	setReadOnly(type);
	form.f_subtotal();

}

function fix_fraction(num, dec){
	return Math.floor(Math.multiply(num,Math.pow(10, dec)))/Math.pow(10, dec)
}


//IE10用
$("#detail_table td select").live("mousedown", function() {
	var line_index = $(this).parents("tr").index() - 2;
	form.focusline = line_index;
	focusLine(form.maintype);
	$(this).focus();
});

function move_to_index(id){
    var checkform = '#'+controller_name+'MovebackForm';
    $(checkform).submit();
}

function toggle_quote_extend_open(){
    $('#calid').hide();
    $('#quote_open_btn').hide();
    $('#quote_close_btn').show();
    $('.quote_extend_area').slideDown();
}
function toggle_quote_extend_close(){
    $('#calid').hide();
    $('#quote_open_btn').show();
    $('#quote_close_btn').hide();
    $('.quote_extend_area').slideUp();
}

function check_if_searched(){
    var open_flg = 0;

    if($('#'+controller_name+'ITEMNAME').val() != undefined){
        if( $('#'+controller_name+'ITEMNAME').val() != '' && $('#'+controller_name+'ITEMNAME').val() != undefined){ open_flg = 1;}
        if( $('#'+controller_name+'ITEMCODE').val() != '' && $('#'+controller_name+'ITEMCODE').val() != undefined){ open_flg = 1;}
        if( $('#'+controller_name+'TOTALFROM').val() != '' && $('#'+controller_name+'TOTALFROM').val() != undefined){ open_flg = 1;}
        if( $('#'+controller_name+'TOTALTO').val() != '' && $('#'+controller_name+'TOTALTO').val() != undefined){ open_flg = 1;}
        if( $('#'+controller_name+'NOTE').val() != '' && $('#'+controller_name+'NOTE').val() != undefined){ open_flg = 1;}
        if( $('#'+controller_name+'MEMO').val() != '' && $('#'+controller_name+'MEMO').val() != undefined){ open_flg = 1;}
        if( $('#'+controller_name+'ACTIONDATEFROM').val() != '' && $('#'+controller_name+'ACTIONDATEFROM').val() != undefined){ open_flg = 1;}
        if( $('#'+controller_name+'ACTIONDATETO').val() != '' && $('#'+controller_name+'ACTIONDATETO').val() != undefined){ open_flg = 1;}
        if( $('input[name="data['+controller_name+'][ISSUE]"]:checked').val() != 3 && $('input[name="data['+controller_name+'][ISSUE]"]:checked').val() != undefined){ open_flg = 1;}
    }

    if(open_flg == 1){
        $('#quote_open_btn').hide();
        $('#quote_close_btn').show();
        $('.quote_extend_area').show();
    }

}

function reset_forms(){
    $('#'+controller_name+'MQTID').val('');
    $('#'+controller_name+'MBLID').val('');
    $('#'+controller_name+'MDVID').val('');
    $('#'+controller_name+'RBLID').val('');
    $('#'+controller_name+'NO').val('');
    $('#'+controller_name+'SUBJECT').val('');
    $('#'+controller_name+'NAME').val('');
    $('#'+controller_name+'CHRUSRNAME').val('');
    $('#'+controller_name+'USRNAME').val('');
    $('#'+controller_name+'UPDUSRNAME').val('');
    for(i=0; i<18; i++){
        $('#'+controller_name+'ACTION'+i).removeAttr('checked');
    }
    for(i=0; i<6; i++){
        $('#'+controller_name+'STATUS'+i).removeAttr('checked');
    }
    $('#'+controller_name+'ITEMCODE').val('');
    $('#'+controller_name+'ITEMNAME').val('');
    $('#'+controller_name+'TOTALFROM').val('');
    $('#'+controller_name+'TOTALTO').val('');
    $('#'+controller_name+'NOTE').val('');
    $('#'+controller_name+'MEMO').val('');
    $('#'+controller_name+'ACTIONDATEFROM').val('');
    $('#'+controller_name+'ACTIONDATETO').val('');
    $('#'+controller_name+'ADDRESS').val('');
    $('#'+controller_name+'LOGINID').val('');
    $('#'+controller_name+'CHARGENAME').val('');
    $('#'+controller_name+'COMPANYNAME').val('');
    $('#'+controller_name+'STATUS').val('');
    $('#'+controller_name+'ITEM').val('');
    $('#'+controller_name+'UNIT').val('');
}

function setBeforeSubmit(formId){
	var form = $('#'+formId);
    form.submit(function() {
    	var data = form.serializeArray();
    	jQuery.each(data, function() {
    		if(this.value==""){
    			$('[name='+this.name+']').removeAttr('name');
    		}
   		});
    	var fixed_data = form.serializeArray();
    	if(fixed_data == ""){
    		form.attr('action', form.attr('action')+'/movetoindex')
    	}
    });
}

