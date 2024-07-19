<?php
/**
 * @copyright ICZ Corporation (http://www.icz.co.jp/)
 * @license See the LICENCE file
 * @author <matcha@icz.co.jp>
 * @version $Id$
 */

namespace App\Vendors\model;

class OtherModel
{
	//帳票以外の共通の処理はこちらで行う

	/*
	 *
	 */

	public function Image_Create(&$_param,$_model_name,&$imageerror){

		//印鑑の最大サイズ
		$limit = Configure::read('ImageSize');
		//ファイルがアップロードされたかどうかの確認
		if($_param[$_model_name]['image']['size']>0){
			//画像サイズの確認
			if($_param[$_model_name]['image']['size']<$limit){
				//アップロードされたファイルかの確認
				if( is_uploaded_file($_param[$_model_name]['image']['tmp_name']) ){
				//IE用拡張子判断
				$extension = strtolower(pathinfo($_param[$_model_name]['image']['name'], PATHINFO_EXTENSION));
				if($extension==='png'||$extension==='jpeg'||$extension==='jpg' || $extension === 'gif'){
				//画像typeであるか確認
					if ($_param[$_model_name]['image']['type']==='image/jpeg'	||
					$_param[$_model_name]['image']['type']==='image/pjpeg'		||
					$_param[$_model_name]['image']['type']=== 'image/png'		||
					$_param[$_model_name]['image']['type']=== 'image/x-png'		||
					$_param[$_model_name]['image']['type']=== 'image/gif'){
						if($_param[$_model_name]['image']['type']==='image/pjpeg'){
							$_param[$_model_name]['image']['type']='image/jpeg';
						}
						if($_param[$_model_name]['image']['type']==='image/x-png'){
							$_param[$_model_name]['image']['type']='image/png';
						}
						$info=getimagesize($_param[$_model_name]['image']['tmp_name']);
						//正しい画像ファイルであるかを確認
			 			if($info['mime']==$_param[$_model_name]['image']['type']){

							//画像のサイズを取得
							list($width, $height) = getimagesize($_param[$_model_name]['image']['tmp_name']);
							if($_param[$_model_name]['image']['type']==='image/jpeg' ){
								$image = imagecreatefromjpeg($_param[$_model_name]['image']['tmp_name']);
							}
							else if($_param[$_model_name]['image']['type']==='image/png' ){
								$image = imagecreatefrompng($_param[$_model_name]['image']['tmp_name']);
							}else if($_param[$_model_name]['image']['type']==='image/gif'){
								$image = imagecreatefromgif($_param[$_model_name]['image']['tmp_name']);
							}

							//画像の作成
							$img = imagecreate($width, $height);

							//色の作成（背景色）
							$backcol = imagecolorallocate($img, 200, 200, 200);

							//背景色を塗る
							imagefill($img, 0, 0, $backcol);

							//キャンバスを透過する
							imagecolortransparent($img, $backcol);
							$color = imagecolorallocate($img, 255, 50, 50);

							for($x = 0 ; $x < $width ; $x++){

								for($y = 0 ; $y < $height ; $y++){

									//インデックスの取得
									$index = imagecolorat($image , $x  , $y);

									//色情報の取得
									$image_data = imagecolorsforindex($image, $index);

									//色情報の取得
									$red   = $image_data["red"];
									$green = $image_data["green"];
									$blue  = $image_data["blue"];
									$alpha = $image_data['alpha'];

									//色情報の取得
									if(!($red > 220 && $green > 220 && $blue > 220) & $alpha == 0)
									{
										//ピクセルの描画
										imagesetpixel($img, $x, $y, $color);
									}
								}
							}

							//画像出力
								imagepng($img,$_param[$_model_name]['image']["tmp_name"]);
								$_param[$_model_name]['SEAL'] = file_get_contents($_param[$_model_name]['image']['tmp_name']);

							//画像の消去（メモリの解放）
							imagedestroy($img);
						 }else{$imageerror=3;}
					}else{$imageerror=1;}
				}else{$imageerror=1;}
				}else{$imageerror=1;}
			}
		else{$imageerror=2;}
		}
	}
}


