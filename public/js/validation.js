//validationのプログラム

$(document).ready(function($){

	if($('form').attr('class') && $('form').attr('class').match(/^(validation)$/)){

		reg1 = new RegExp('max_([0-9]*)');
		$('form').submit(function(){
			$("p.error").remove();
			$("form *").removeClass("error");
			//必須項目のチェック 'class'=>'required'
			$(":text,textarea").filter(".validate").each(function(){
				$(this).filter(".required").each(function(){
					if($(this).val()==""){
						$(this).parent().prepend("<p class='error')>必須項目です</p>");
						$(this).addClass("error");
					}
				});
				//文字数チェック 'class'=>'maxlength max_(数字)'
				$(this).filter(".maxlength").each(function(){
					result=$(this).attr("class").match(reg1);
					if($(this).val().length>result['1']){
						$(this).parent().prepend("<p class='error')>文字数は"+result['1']+"文字までです</p>");
						$(this).addClass("error");
					}
				});
				//数字のみ許可
				$(this).filter(".numberonly").each(function(){
					num = new RegExp('^[0-9]*$');
					if(!$(this).val().match(num)){
						$(this).parent().prepend("<p class='error')>半角数字のみ入力可能です</p>");
						$(this).addClass("error");
					}
				});
				//半角英数字、/、-、_のみ許可
				$(this).filter(".manage").each(function(){
					manage = new RegExp('^[a-zA-Z0-9\/_\.-]*$');
					if(!$(this).val().match(manage)){
						$(this).parent().prepend("<p class='error')>半角英数字、/、-、_のみ入力可能です</p>");
						$(this).addClass("error");
					}
				});
			});
			//エラーがあったときの処理
			if($("p.error").size() > 0){
				return false;
			}
		});
		$(":text,textarea").filter(".validate").each(function(){
			$(this).filter(".required").blur(function(){
				if($(this).val()==""){
					$(this).parent().prepend("<p class='error')>必須項目です</p>");
					$(this).addClass("error");
//					console.log($(this).parents('form').find(".submit"));
				}
				else{
					$(this).removeClass("error");
					$(this).parent().children("p").remove();
				}
			});
			$(this).filter(".required").focus(function(){
				$(this).removeClass("error");
				$(this).parent().children("p").remove();
			});
			$(this).filter(".maxlength").blur(function(){
				result=$(this).attr("class").match(reg1);
				if($(this).val().length>result['1']){
					$(this).parent().prepend("<p class='error')>文字数は"+result['1']+"文字までです</p>");
					$(this).addClass("error");
				}
			});
			$(this).filter(".maxlength").focus(function(){
				$(this).removeClass("error");
				$(this).parent().children("p").remove();
			});
			//数字のみ許可
			$(this).filter(".numberonly").blur(function(){
				num = new RegExp('^[0-9]*$');
				if(!$(this).val().match(num)){
					$(this).parent().prepend("<p class='error')>半角数字のみ入力可能です</p>");
					$(this).addClass("error");
				}
			});
			$(this).filter(".numberonly").focus(function(){
				$(this).removeClass("error");
				$(this).parent().children("p").remove();
			});
			//半角英数字、/、-、_のみ許可
			$(this).filter(".manage").each(function(){
				manage = new RegExp('^[a-zA-Z0-9\/_\.-]*$');
				if(!$(this).val().match(manage)){
					$(this).parent().prepend("<p class='error')>半角英数字、/、-、_のみ入力可能です</p>");
					$(this).addClass("error");
				}
			});
			$(this).filter(".manage").focus(function(){
				$(this).removeClass("error");
				$(this).parent().children("p").remove();
			});
		});
	}
});