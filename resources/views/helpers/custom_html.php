<?php
/**
 * @copyright ICZ Corporation (http://www.icz.co.jp/)
 * @license See the LICENCE file
 * @author <matcha@icz.co.jp>
 * @version $Id$
 */

class CustomHtmlHelper extends HtmlHelper {
	var $helpers = array('Form', 'Paginator');

	function plink($title, $url = null, $options = array(), $confirmMessage = false) {
		$escapeTitle = true;
		if ($url !== null) {
			$url = $this->url($url);
		} else {
			$url = $this->url($title);
			$title = $url;
			$escapeTitle = false;
		}

		if (isset($options['escape'])) {
			$escapeTitle = $options['escape'];
		}

		if ($escapeTitle === true) {
			$title = h($title);
		} elseif (is_string($escapeTitle)) {
			$title = htmlentities($title, ENT_QUOTES, $escapeTitle);
		}

		if (!empty($options['confirm'])) {
			$confirmMessage = $options['confirm'];
			unset($options['confirm']);
		}
		if ($confirmMessage) {
			$confirmMessage = str_replace("'", "\'", $confirmMessage);
			$confirmMessage = str_replace('"', '\"', $confirmMessage);
			$options['onclick'] = "return confirm('{$confirmMessage}');";
		} elseif (isset($options['default']) && $options['default'] == false) {
			if (isset($options['onclick'])) {
				$options['onclick'] .= ' event.returnValue = false; return false;';
			} else {
				$options['onclick'] = 'event.returnValue = false; return false;';
			}
			unset($options['default']);
		}
		return sprintf($this->tags['link'], $url, $this->_parseAttributes($options), '<p>'.$title.'</p>');
	}

	function dateFormat($date,$format = "Y年m月d日") {
		return date($format,strtotime($date));
	}
	function df($date,$format = "Y年m月d日") {
		return $this->dateFormat($date,$format);
	}
	function dateTimeFormat($datetime,$format = "Y年m月d日 H:i:s") {
		return date($format,strtotime($datetime));
	}
	function dtf($datetime,$format = "Y年m月d日 H:i:s") {
		return $this->dateTimeFormat($datetime,$format);
	}

	//
	public function float_format($_data){
		$i = strpos($_data,'.');
	}
	function ht2br($_data, $_cont = null, $_attr = null, $_decimal = null) {

		$data = h($_data);

		if($_attr === 'NOTE'){
			$data = nl2br($data);
		}

		if( $_attr === 'AMOUNT' || $_attr === 'SUBTOTAL' || $_attr === 'SALES_TAX' || $_attr === 'TOTAL'|| $_attr ==='TOTALBILL' || $_attr === 'THISM_BILL'){
			$data = number_format($data);
		}
		if($_attr === 'QUANTITY' || $_attr === 'UNIT_PRICE' ){
			$i = strpos($data,'.');
			if($i){
				$str = substr($data, 0, $i);
				$astr = number_format($str);
				$data = str_replace($str, $astr, $data);
			}
			else{
				$data = number_format($data);
			}
		}
		return $data;
	}

	//CSRF対策
	function hiddenToken() {
		return $this->Form->hidden('Security.token', array('value' => session_id() ));
	}


	function sortLink($_name, $_field) {
		if(isset($this->params['named']['sort']) && $this->params['named']['sort'] == $_field) {
			$_direction = (strtolower($this->params['named']['direction']) == 'asc') ? '↑': '↓';
		}else {
			$_direction = '';
		}

		$_name = $_name.$_direction;

		return $this->Paginator->sort($_name, $_field);
	}
}
