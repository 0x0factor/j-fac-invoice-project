<?php
/**
 * @copyright ICZ Corporation (http://www.icz.co.jp/)
 * @license See the LICENCE file
 * @author <matcha@icz.co.jp>
 * @version $Id$
 *
 *
 */

namespace App\Http\Controllers;

class AppController extends Controller {

	public $autoLayout = false;
	var $uses =array('History','Company','ViewOption', 'Charge', 'Bill', 'Delivery', 'Quote', 'Totalbill');
	var $helpers = array('Session','Html', 'Form','CustomAjax','CustomHtml');
	var $components = array('Session','Common','Auth','Cookie');

	function beforeFilter(){
		if(configure('constants.onlyFullGroupByDisable')){
			$this->disable_only_full_group_by();
		}

		$this->header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
		$this->header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		$this->header('Pragma: no-cache');

		$user=$this->Auth->user();
		$this->set('usernavi', configure('constants.UserNavigate'));
		$this->set('user',$user['User']);
		$value = $this->Cookie->read('userid');
		$log=$this->History->h_getlastlog($user['User']['USR_ID']);
		$nbrowser = $this->History->browser_hash();

		if($log['History']['ACTION']==0&& $nbrowser!=$log['History']['BROWSER']){
			if(!$this->Common->matchCheck($this->action, array('pdf','contents'))
			&&!($this->Common->matchCheck($this->params['controller'], array('mails')) && $this->Common->matchCheck($this->action, array('login','customer', 'logout'))))
			{
				$this->Session->setFlash('同じユーザーIDで他PCでログインされたかセッションが切れた為、ログアウトしました','',array('auto_logout'));
				$this->redirect("/users/logout");
			}
		}

		$session_params = $this->Session->read('session_params');
		$read_session_params = $this->Session->read('read_session_params');
		$dataArray = $this->params['url'];
		unset($dataArray['url']);

		if(empty($dataArray) && $this->action=='index' && empty($this->passedArgs)){
			if(!empty($session_params[$this->params['controller']])){
				$url = '/'.$this->params['controller'].'?';
				foreach($session_params[$this->params['controller']] as $key => $val){
					if ($val !== reset($session_params[$this->params['controller']])) {
						$url .='&';
					}
					$url .= $key.'='.$val;
				}
				$this->redirect($url);
			}
		} else if (SearchBoxSessionMode == SessionDeleteAlways && $this->action != 'edit' && $this->action != 'check' && $this->action != 'moveback' ){
			$this->Session->delete('session_params');
		}
		if(SearchBoxSessionMode == SessionDeleteAlways && $this->action!='index'){
			$this->Session->delete('read_session_params');
		}
		if(SearchBoxSessionMode == SessionDeleteAlways && empty($session_params[$this->params['controller']])){
			$this->Session->delete('session_params');
		}

		if(!empty($dataArray) && $this->action=='index'){
			$insArray = $session_params;
			$insArray[$this->params['controller']] = $dataArray;
			$this->Session->write('session_params', $insArray);
		}

		$this->Common->Authority_Check($user,$this);

		$this->Set_View_Option();

		if($this->action == 'pdf' && $this->name != 'Totalbill' ||
				$this->action == 'index' && $this->name == 'Coverpage') {
			$this->createTmpImage();
		}

		if (file_exists(APP.'plugins'. DS .'regularbill')){
			$this->set('rb_flag',true);
		}

	}


	function Get_User_Data(){
		return $this->Auth->user();
	}

	function Set_View_Option(){
		$options = $this->ViewOption->
			find('all',array('fields' => array('ViewOption.OPTION_NAME','ViewOption.OPTION_NAME_JP','ViewOption.OPTION_VALUE')));

		for($i = 0; $i < count($options); $i++) {
			if($options[$i]['ViewOption']['OPTION_NAME'] =='logo') {
				$this->set($options[$i]['ViewOption']['OPTION_NAME'],'cms/'.$options[$i]['ViewOption']['OPTION_VALUE']);
			}else{
				$this->set($options[$i]['ViewOption']['OPTION_NAME'],$options[$i]['ViewOption']['OPTION_VALUE']);
			}
		}
	}

	function Get_Check_Authority($_id){
		$user=$this->Auth->user();
		if($user['User']['AUTHORITY']==1){
			if($_id!=$user['User']['USR_ID']){
				return false;
			}
		}
		return true;
	}

	function Get_Edit_Authority($_id){
		$user=$this->Auth->user();
		if($user['User']['AUTHORITY']==2||$user['User']['AUTHORITY']==1){
			if($_id!=$user['User']['USR_ID']){
				return false;
			}
		}
		return true;
	}
	function Get_User_ID(){
		$user=$this->Auth->user();
		return $user['User']['USR_ID'];
	}
	function Get_User_AUTHORITY(){
		$user=$this->Auth->user();
		return $user['User']['AUTHORITY'];
	}

	function item_validation($_param,$_field){

		$_error = array(
			'ITEM'=>array(
				'NO'=>array()
				,'FLAG'=>0
			),
			'ITEM_NO'=>array(
				'NO'=>array()
				,'FLAG'=>0
			),
			'QUANTITY'=>array(
				'NO'=>array()
				,'FLAG'=>0
			),
			'UNIT'=>array(
				'NO'=>array()
				,'FLAG'=>0
			),
			'UNIT_PRICE'=>array(
				'NO'=>array()
				,'FLAG'=>0
			),
		);
			for($i=0;$i<count($_param)-2;$i++){
				$item_value=ceil(mb_strwidth($_param[$i][$_field]['ITEM']) / 2);
				if($item_value > 40){
					$_error['ITEM']['NO'][$i]=$i;
				}
				$no_value=mb_strlen($_param[$i][$_field]['ITEM_NO']);
				if($no_value > 2){
					$_error['ITEM_NO']['NO'][$i]=$i;
				}
				if(preg_match( "/^[0-9]+$/",$_param[$i][$_field]['ITEM_NO'])==0
							&& $_param[$i][$_field]['ITEM_NO']!=NULL){
					$_error['ITEM_NO']['NO'][$i]=$i;
				}
				$quantity_value=0;
				$quantityf_value=0;
				$j = strpos($_param[$i][$_field]['QUANTITY'],'.');
				if($j){
					$str = substr($_param[$i][$_field]['QUANTITY'], 0, $j);
					$astr = substr($_param[$i][$_field]['QUANTITY'], $j+1);
					$quantityf_value=mb_strlen($str)+mb_strlen($astr);
				}else{
					$quantity_value=mb_strlen($_param[$i][$_field]['QUANTITY']);
				}
				if($quantity_value > 6){
					$_error['QUANTITY']['NO'][$i]=$i;
				}
				if($quantityf_value > 6){
					$_error['QUANTITY']['NO'][$i]=$i;
				}
				if(preg_match( "/^(\\|\$)?(0|-?[1-9]\d*|-?(0|[1-9]\d*)\.\d+)$/",$_param[$i][$_field]['QUANTITY'])==0
							&& $_param[$i][$_field]['QUANTITY']!=NULL){
					$_error['QUANTITY']['NO'][$i]=$i;
				}
				$unit_value=ceil(mb_strwidth($_param[$i][$_field]['UNIT']) / 2);
				if($unit_value > 4){
					$_error['UNIT']['NO'][$i]=$i;
				}
				$unitprice_value=0;
				$unitpricef_value=0;
				$j = strpos($_param[$i][$_field]['UNIT_PRICE'],'.');
				if($j){
					$str = substr($_param[$i][$_field]['UNIT_PRICE'], 0, $j);
					$astr = substr($_param[$i][$_field]['UNIT_PRICE'], $j+1);
					$unitpricef_value=mb_strlen($str)+mb_strlen($astr);
				}else{
					$unitprice_value=mb_strlen($_param[$i][$_field]['UNIT_PRICE']);
				}
				if($unitprice_value > 9){
					$_error['UNIT_PRICE']['NO'][$i]=$i;
				}
				if($unitpricef_value > 9){
					$_error['UNIT_PRICE']['NO'][$i]=$i;
				}
				if(preg_match( "/^(\\|\$)?(0|-?[1-9]\d*|-?(0|[1-9]\d*)\.\d+)$/",$_param[$i][$_field]['UNIT_PRICE'])==0
							&& $_param[$i][$_field]['UNIT_PRICE']!=NULL){
					$_error['UNIT_PRICE']['NO'][$i]=$i;
				}
			}
		$_error = $this->Common->array_is_empty($_error);
		return $_error;
	}
	function phone_validation($_param){
		$error = NULL;
		$error = $this->Common->array_is_empty($_param);
		return $error;
	}
	function fax_validation($_param){
		$error = NULL;
		$error = $this->Common->array_is_empty($_param);
		return $error;
	}
	function serial_validation($_param,$_field){
		$error = array(
			'FLG'=> 0,
			'NO'=> array(),
		);
		for($i=0;$i<count($_param);$i++){
			$serial_value=mb_strlen($_param[$i][$_field]);
			if($serial_value > 5){
				$error['NO'][$i]=$i;
			}
		}
		$error = $this->Common->array_is_empty($error);
		return $error;
	}

	function isCorrectToken() {
		if ($this->params['controller'] == 'data'){
			if($this->params['action']=='moveback' || $this->params['action']=='check' || $this->params['action']=='edit'){
				$token = $this->Session->read('edit_token');
				if (!empty($token) && $token != $this->data['Data']['token']) {
					return false;
				} else if(empty($token)){
					return false;
				}
				$this->Session->delete('edit_token');
				return true;
			}
		}else if ($this->params['controller'] == 'totalbills'){
			$token = $this->Session->read('edit_token');
			if (!empty($token) && $token != $this->data['Totalbill']['token']) {
				return false;
			}
			$this->Session->delete('edit_token');
			return true;
		}else{
			if(!empty($this->data['Delivery'])){
				$token = $this->Session->read('edit_token');
				if (!empty($token) && $token != $this->data['Delivery']['token']) {
					return false;
				}
			}else{
				if(!empty($this->data['Bill'])){
					$token = $this->Session->read('edit_token');
					if (!empty($token) && $token != $this->data['Bill']['token']) {
						return false;
					}
				}
			}
			$this->Session->delete('edit_token');
			return true;
		}
	}

	function disable_only_full_group_by() {
		$db = ConnectionManager::getDataSource('default');
		$db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
	}

	function createTmpImage() {
		$user=$this->Auth->user();
		$dir = TMP.'images'.DS.$user['User']['USR_ID'];
		$flag = true;
		if (is_dir($dir)) {
			$dh = opendir($dir);
			while (($file = readdir($dh)) !== false) {
				if (!in_array($file, array('.', '..'))) {
					unlink($dir.DS.$file);
				}
			}
			closedir($dh);
		} else {
			$flag = mkdir($dir);
		}

		if ($flag === true) {
			$cmd = "phantomjs ".dirname(__FILE__).DS."..".DS."vendors".DS."scripts".DS."rasterize.js ".
					Router::url(array('controller' => 'Coverpage', 'action' => 'index', 'ext' => 'pdf'), true)." ".$dir.DS."coverpage.png 960px*540px";

			exec($cmd);
		}
	}

	function getCompatibleItems($items) {
		$oldfields = array(
			"MEMO1",
			"MEMO2",
			"MEMO3",
			"MEMO4",
			"MEMO5",
			"MEMO6",
			"MEMO7",
			"MEMO8",
			"MEMO9",
			"MEMO10",
		);
		$newfields = array(
			"ITEM_REMARK_1",
			"ITEM_REMARK_2",
			"ITEM_REMARK_3",
			"ITEM_REMARK_4",
			"ITEM_REMARK_5",
			"ITEM_REMARK_6",
			"ITEM_REMARK_7",
			"ITEM_REMARK_8",
			"ITEM_REMARK_9",
			"ITEM_REMARK_10",
		);

		for ($i = 0; $i < count($items); $i++) {
			for ($j = 0; $j < count($oldfields); $j++) {
				if (empty($items[$i]["Item"][$newfields[$j]])) {
					$items[$i]["Item"][$newfields[$j]] = $items[$i]["Item"][$oldfields[$j]];
				}
			}
		}

		return $items;
	}
}
