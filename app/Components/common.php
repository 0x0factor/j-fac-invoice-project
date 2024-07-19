<?php

/**
 * @copyright ICZ Corporation (http://www.icz.co.jp/)
 * @license See the LICENCE file
 * @author <matcha@icz.co.jp>
 * @version $Id$
 */
class CommonComponent extends Object
{

    var $_controller;
    
    function startup(& $controller)
    {
        //$this->_controller = $controller;
    }

    /**
     * ページング
     * 
     * @param $searchColumnAry 検索条件を指定する            
     * @param $joins 結合するテーブルを指定する            
     * @param $order 昇順降順を指定する(desc,ascの文字列)            
     * @param $add_condition 検索条件以外のwhere句を指定する            
     *
     */
    public function paginate($searchColumnAry = null, $joins = null, $order = null, $add_condition = null, $controller = null, $groupBy = null)
    {
        if ($controller != null) {
            $this->_controller = $controller;
        }
        // Model 名
        $modelClass = $this->_controller->modelClass;
        
        // パラメータを Array に設定
        $conditionAry = array();
        $conditions = array();
        $pager_options = array();
        
        // returnパラム用
        $param = array();
        
        if($this->params['method'] == 'post'){
        	$this->redirect($this->here);
        }
        
        
        if ($searchColumnAry) {
            foreach ($searchColumnAry as $searchColumnName => $searchColumnDetail) {
                $value = ''; // 初期化
                $param[$searchColumnName] = '';

                // ページングのパラメータを優先
                if (isset($this->_controller->passedArgs[$searchColumnName])) {
                    $value = $this->_controller->passedArgs[$searchColumnName];
                    
                    $value = str_replace('$qm','?',$value);
                    $value = str_replace('$ds','/',$value);
                    $value = str_replace('$and','&',$value);
                    
                    $param[$searchColumnName] = $value;
                } else {
                	if(isset($this->_controller->params['url'][$searchColumnName])){
                		$value = $this->_controller->params['url'][$searchColumnName];
                		$param[$searchColumnName] = $value;
                	}
                }
                
                // 名前・名前カナなど、OR条件でセットにする場合
                if (is_array($searchColumnDetail)) {
                    $tempOrCondition = array();
                    foreach ($searchColumnDetail as $orSearchColumnName) {
                        $tempOrCondition['OR'][] = array(
                            "$orSearchColumnName LIKE" => "%$value%"
                        );
                    }
                    $conditions[] = $tempOrCondition;
                } else {
                    // ステータスの場合、like条件から外す
                    if ($searchColumnName == 'STATUS' || $searchColumnName == 'TYPE' || $searchColumnName == 'ACTION') {
                        
                        if ($value !== "") {
                            if (preg_match('/,/', "$value")) { // チェックボックのページングの際のパラメータ変換
                                $status = explode(',', $value);
                                $conditions[] = array(
                                    "$searchColumnDetail" => $status
                                );
                            } else { // selectの場合
                                $conditions[] = array(
                                    "$searchColumnDetail" => $value
                                );
                            }
                        } elseif (is_array($value)) { // チェックボックスの場合
                            $conditions[] = array(
                                "$searchColumnDetail" => $value
                            );
                        }
                    } else {
                        if ($searchColumnDetail == 'History.ACTION_DATE_FROM') {
                            $conditions[] = array(
                                "History.ACTION_DATE >=" => $value
                            );
                        } elseif ($searchColumnDetail == 'History.ACTION_DATE_TO') {
                            if ($value != '') {
                                $conditions[] = array(
                                    "History.ACTION_DATE <=" => $value . ' 23:59:59'
                                );
                            }
                        } elseif ($searchColumnDetail == 'Bill.ACTION_DATE_FROM') {
                            $conditions[] = array(
                                "Bill.ISSUE_DATE >=" => $value
                            );
                        } elseif ($searchColumnDetail == 'Bill.ACTION_DATE_TO') {
                            if ($value != '') {
                                $conditions[] = array(
                                    "Bill.ISSUE_DATE <=" => $value . ' 23:59:59'
                                );
                            }
                        } elseif ($searchColumnDetail == 'Delivery.ACTION_DATE_FROM') {
                            $conditions[] = array(
                                "Delivery.ISSUE_DATE >=" => $value
                            );
                        } elseif ($searchColumnDetail == 'Delivery.ACTION_DATE_TO') {
                            if ($value != '') {
                                $conditions[] = array(
                                    "Delivery.ISSUE_DATE <=" => $value . ' 23:59:59'
                                );
                            }
                        } elseif ($searchColumnDetail == 'Quote.ACTION_DATE_FROM') {
                            $conditions[] = array(
                                "Quote.ISSUE_DATE >=" => $value
                            );
                        } elseif ($searchColumnDetail == 'Quote.ACTION_DATE_TO') {
                            if ($value != '') {
                                $conditions[] = array(
                                    "Quote.ISSUE_DATE <=" => $value . ' 23:59:59'
                                );
                            }
                        } elseif ($searchColumnDetail == 'Totalbill.ACTION_DATE_FROM') {
                            $conditions[] = array(
                                "Totalbill.ISSUE_DATE >=" => $value
                            );
                        } elseif ($searchColumnDetail == 'Totalbill.ACTION_DATE_TO') {
                            if ($value != '') {
                                $conditions[] = array(
                                    "Totalbill.ISSUE_DATE <=" => $value . ' 23:59:59'
                                );
                            }
                        } elseif ($searchColumnDetail == 'TOTAL_FROM') {
                            if ($value != '') {
	                        	$conditions[] = array(
	                                "CAST_TOTAL >=" => intval($value)
	                            );
                            }
                        } elseif ($searchColumnDetail == 'TOTAL_TO') {
                            if ($value != '') {
                                $conditions[] = array(
                                    "CAST_TOTAL <=" => intval($value)
                                );
                            }
                        } elseif ($searchColumnDetail == 'Regularbillconfig.ISSUE') {
                            if ($value != '' && $value != 3) {
                                $conditions[] = array(
                                    "Regularbillconfig.ISSUE" => intval($value), 
                                    "Regularbillconfig.ISSUE_DETAIL" =>$this->_controller->params['data'][$modelClass]['ISSUE_DETAIL']
                                );
                            }
                        } else {
                            if($value != null){
                                $conditions[] = array(
                                    "$searchColumnDetail LIKE" => "%$value%"
                                );
                            }
                        }
                    }
                }
                // チェックボックのパラメータ分割
                if (is_array($value))
                    $value = implode(',', $value);
                    
                    // ページングのパラメータ
                $option[$searchColumnName] = urlencode($value);
            }
        }
        
        // 顧客一覧から絞り込み
        if (isset($this->_controller->params['named']['customer'])) {
            $conditions[] = array(
                "$modelClass.CST_ID" => $this->_controller->params['named']['customer']
            );
        }
        
                // ソートに関するパラメータを引き継ぎ
        foreach ($this->_controller->params['named'] as $key => $val) {
            if ($key != 'page'){
                $option[$key] = $val;
            }
        }
        foreach ($option as $key => $val) {
            $converted_data = str_replace('%3F','$qm',$val);
            $converted_data = str_replace('%26','$and',$converted_data);
            $converted_data = str_replace('%2F','$ds',$converted_data);
            $option[$key] = $converted_data;
        }

        
        $pager_options['url'] = isset($option) ? $option : '';
        if (is_array($add_condition)) {
            $conditions[] = $add_condition;
        }
        
        // 最大件数の取得
        $this->_controller->paginate = array(
            $modelClass => array(
                'fields' => '*',
                'joins' => $joins,
                'conditions' => $conditions,
                'order' => $order,
                'group' => $groupBy,
                'limit' => 2147483647
            )
        );
        
        $maxcount = count($this->_controller->paginate());
        
        // 実行
        $this->_controller->paginate = array(
            $modelClass => array(
                'fields' => '*',
                'joins' => $joins,
                'conditions' => $conditions,
                'order' => $order,
                'group' => $groupBy,
                'limit' => Configure::read('Paginate.LinesPerPage')
            )
        );
        $this->_controller->set('options', $pager_options);
        $this->_controller->pageOptions = $pager_options;
        
        $this->_controller->set('list', $this->_controller->paginate(null, array(), array(), $maxcount));
        
        $limit = Configure::read('Paginate.LinesPerPage');
        $count = count($this->_controller->paginate(null, array(), array(), $maxcount));
        $page = (isset($this->_controller->params['named']['page']) && ($this->_controller->params['named']['page'] > 0)) ? $this->_controller->params['named']['page'] - 1 : 0;
        
        $param['count'] = $maxcount . " 件中 " . ($maxcount > $page * $limit + 1 ? $page * $limit + 1 : $maxcount) . " - " . ($maxcount > (($page + 1) * $limit) ? ($page + 1) * $limit : $maxcount) . " 件を表示";
        
        $param['data'] = $this->_controller->paginate(null, array(), array(), $maxcount);
        
        return $param;
    }

    /**
     * 権限のチェック
     * 
     * @param $_model_name 使用しているモデルの名前            
     * @param $_action 現在のアクションの名前            
     * @param $_user 現在ログインしているユーザー            
     * @param $controller 今のコントローラーobj            
     *
     */
    function Authority_Check($_user, $controller = null)
    {
        $del_check = array();
        if ($controller != null) {
            $this->_controller = $controller;
            $_action = $this->_controller->action;
            $_model_name = $this->_controller->modelClass;
        }
        
        if ($_model_name == 'Bill' || $_model_name == 'Quote' || $_model_name == 'Delivery' || $_model_name == 'Totalbill'  || $_model_name == 'Regularbill') {
            
            // pdfの場合のみトークンチェック
            if ($_action === "pdf" && isset($this->_controller->params['pass'][2])) {
                $time_limit = date('Y-m-d H:i:s', mktime(date("H"), date("i"), date("s"), date("n"), date("j") - Configure::read('MailLoginTerm'), date("Y")));
                if($_model_name == 'Quote') {$type = 1;}
                if($_model_name == 'Delivery') {$type = 3;}
                if($_model_name == 'Totalbill') {$type = 4;}
                if($_model_name == 'Bill') {$type = 2;}
                
                $_model_name = 'Mail';
                
                if ($result = $this->_controller->$_model_name->find('first', array(
                    'conditions' => array(
                        'TYPE' => $type,
                        'FRM_ID' => $this->_controller->params['pass'][0],
                        'TOKEN' => $this->_controller->params['pass'][2],
                        'SND_DATE >=' => $time_limit
                    )
                ))) {
                    $this->_controller->Auth->allow('pdf');
                }
            }
            if ($_action == 'index' || ($controller == 'Customer' && $_action = 'select')) {
                $scarray = $this->_controller->$_model_name->searchColumnAry;
                
                // ソート条件の取得
                if (isset($this->_controller->params['named']['field']) && isset($this->_controller->params['named']['orders'])) {
                    $order = $this->_controller->params['named']['field'] . ' ' . $this->_controller->params['named']['orders'];
                } else {
                    $order = null;
                }
                
                if(isset($this->_controller->$_model_name->groupBy)){
                    $groupBy = $this->_controller->$_model_name->groupBy;
                } else {
                    $groupBy = null;
                }
                
                // ページング
                if ($_user['User']['AUTHORITY'] != 1) {
                    $this->_controller->data[$_model_name] = $this->paginate($scarray, null, $order, null, $this->_controller, $groupBy);
                } else {
                    $condition = array(
                        $_model_name . '.USR_ID' => $_user['User']['USR_ID']
                    );
                    $this->_controller->data[$_model_name] = $this->paginate($scarray, null, $order, $condition, $this->_controller, $groupBy);
                }
                for ($i = 0; $i < count($this->_controller->data[$_model_name]['data']); $i ++) {
                    $del_check[$i] = $this->_controller->Get_Edit_Authority($this->_controller->data[$_model_name]['data'][$i][$_model_name]['USR_ID']);
                    if ($del_check[$i]) {
                        $del_check[$i] = 1;
                    } else {
                        $del_check[$i] = 0;
                    }
                }
                $this->_controller->set("authcheck", $del_check);
            }
        }
        
        if ($_model_name == 'Mail' || $_model_name == 'BillCheck' || $_model_name == 'QuoteCheck' || $_model_name == 'DeliveryCheck') {
            if ($_action == 'index') {
                $scarray = $this->_controller->$_model_name->searchColumnAry;
                // ページング
                if ($_user['User']['AUTHORITY'] == 3 || $_user['User']['AUTHORITY'] == 0) {
                    $this->_controller->data[$_model_name] = $this->paginate($scarray, null, null, null, $this->_controller);
                } else {
                    $condition = array(
                        $_model_name . '.USR_ID' => $_user['User']['USR_ID']
                    );
                    $this->_controller->data[$_model_name] = $this->paginate($scarray, null, null, $condition, $this->_controller);
                }
                $this->_controller->set("authcheck", $del_check);
            }
        }
        
        if ($_model_name == 'Customer' || $_model_name == 'CustomerCharge' || $_model_name == 'Charge' || $_model_name == 'Item') {
            if ($_action == 'index' || $_action == 'select') {
                if (! isset($this->_controller->params['form']['delete_x'])) {
                    $scarray = $this->_controller->$_model_name->searchColumnAry;
                    // ページング
                    if ($_user['User']['AUTHORITY'] != 1) {
                        $this->_controller->data[$_model_name] = $this->paginate($scarray, null, null, null, $this->_controller);
                    } else {
                        $condition = array(
                            $_model_name . '.USR_ID' => $_user['User']['USR_ID']
                        );
                        $this->_controller->data[$_model_name] = $this->paginate($scarray, null, null, $condition, $this->_controller);
                    }
                    
                    if ($_model_name == 'Customer') {
                        $primalyID = 'CST_ID';
                        $delcheck = $this->_controller->$_model_name->check_pegging($this->_controller->data[$_model_name]['data']);
                    }
                    
                    if ($_model_name == 'CustomerCharge') {
                        $primalyID = 'CHRC_ID';
                        $delcheck = $this->_controller->$_model_name->check_pegging($this->_controller->data[$_model_name]['data']);
                    }
                    
                    if ($_model_name == 'Charge') {
                        $primalyID = 'CHR_ID';
                        $delcheck = $this->_controller->$_model_name->check_pegging($this->_controller->data[$_model_name]['data']);
                    }
                    
                    if ($_model_name == 'Item') {
                        $primalyID = 'ITM_ID';
                    }
                    
                    $_check = array();
                    for ($i = 0; $i < count($this->_controller->data[$_model_name]['data']); $i ++) {
                        if ($_user['User']['AUTHORITY'] == 1) {
                            $auth_check[$i] = $this->_controller->Get_Check_Authority($this->_controller->data[$_model_name]['data'][$i][$_model_name]['USR_ID']);
                        } else {
                            $auth_check[$i] = $this->_controller->Get_Edit_Authority($this->_controller->data[$_model_name]['data'][$i][$_model_name]['USR_ID']);
                        }
                        
                        $_id = $this->_controller->data[$_model_name]['data'][$i][$_model_name][$primalyID];
                        if ($auth_check[$i]) {
                            $_check[$_id] = 1;
                        } else {
                            $_check[$_id] = 0;
                        }
                        
                        if (isset($delcheck[$_id])) {
                            if ($delcheck[$_id] == 1 || $_check[$_id] == 0) {
                                $delcheck[$_id] = 1;
                            }
                        } else {
                            if ($_check[$_id]) {
                                $delcheck[$_id] = 1;
                            }
                        }
                    }
                    if ($_user['User']['AUTHORITY'] == 2) {
                        foreach ($_check as $key => $val) {
                            $_check[$key] = 1;
                        }
                    }
                    
                    if (! empty($delcheck)) {
                        $this->_controller->set("delcheck", $delcheck);
                    }
                    $this->_controller->set("authcheck", $_check);
                }
            }
        }
        if ($_model_name == 'Company') {
            if ($_action == 'edit') {
                if ($_user['User']['AUTHORITY'] != 0) {
                    $this->_controller->Session->setFlash('自社情報は変更できません');
                    $this->_controller->redirect("index");
                }
            }
        }
        if ($_model_name == 'History' || $_model_name == 'Administer') {
            if ($_user['User']['AUTHORITY'] != 0) {
                $this->_controller->redirect("/homes");
            }
            if ($_action == 'index') {
                $scarray = $this->_controller->$_model_name->searchColumnAry;
                
                // ページング
                if ($_user['User']['AUTHORITY'] != 1) {
                    $this->_controller->data[$_model_name] = $this->paginate($scarray, null, null, null, $this->_controller);
                } else {
                    $condition = array(
                        $_model_name . '.USR_ID' => $_user['User']['USR_ID']
                    );
                    $this->_controller->data[$_model_name] = $this->paginate($scarray, null, null, $condition, $this->_controller);
                }
                
                for ($i = 0; $i < count($this->_controller->data[$_model_name]['data']); $i ++) {
                    $del_check[$i] = $this->_controller->Get_Edit_Authority($this->_controller->data[$_model_name]['data'][$i][$_model_name]['USR_ID']);
                    if ($del_check[$i]) {
                        $del_check[$i] = 1;
                    } else {
                        $del_check[$i] = 0;
                    }
                }
                $this->_controller->set("authcheck", $del_check);
            }
        }
        if ($_model_name == 'History' || $_model_name == 'Administer') {
            if ($_user['User']['AUTHORITY'] != 0) {
                $this->_controller->redirect("/homes");
            }
            if ($_action == 'index') {
                $scarray = $this->_controller->$_model_name->searchColumnAry;
                
                // ページング
                if ($_user['User']['AUTHORITY'] != 1) {
                    $this->_controller->data[$_model_name] = $this->paginate($scarray, null, null, null, $this->_controller);
                } else {
                    $condition = array(
                        $_model_name . '.USR_ID' => $_user['User']['USR_ID']
                    );
                    $this->_controller->data[$_model_name] = $this->paginate($scarray, null, null, $condition, $this->_controller);
                }
                
                for ($i = 0; $i < count($this->_controller->data[$_model_name]['data']); $i ++) {
                    $del_check[$i] = $this->_controller->Get_Edit_Authority($this->_controller->data[$_model_name]['data'][$i][$_model_name]['USR_ID']);
                    if ($del_check[$i]) {
                        $del_check[$i] = 1;
                    } else {
                        $del_check[$i] = 0;
                    }
                }
                $this->_controller->set("authcheck", $del_check);
            }
        }
        
        if ($_model_name == 'Configuration' || $_model_name == 'ViewOption') {
            if ($_user['User']['AUTHORITY'] != 0) {
                $this->_controller->redirect("/homes");
            }
        }
        
        if ($this->matchCheck($_model_name, array(
            'Company',
            'Charge'
        ))) {
            $this->_controller->Auth->allow('contents');
        }
    }

    /**
     * 汎用データマッチング if文の簡素化用
     *
     * @param $_target マッチング対象の文字列            
     * @param $_data 調査文字列（配列も可）            
     *
     * @return bool マッチした場合のみtrueを返す
     */
    function matchCheck($_target, $_data)
    {
        if (is_array($_data)) {
            foreach ($_data as $value) {
                if ($_target === $value) {
                    return true;
                }
            }
        } else {
            if ($_target === $_data) {
                return true;
            }
        }
        return false;
    }

    /**
     * トークン関連の処理
     * 一連の処理を１限化する際に使用する
     *
     * 例 確認メール送信の場合
     *
     * 宛先作成(発行) → 本文作成(確認) → 確認画面(確認) → 完了画面(削除)
     *
     * 上記の様にトークン処理を挟むことで一連の処理完了後に処理の途中に戻り再度処理を完了させる様なユーザーの操作を防ぐ
     */
    
    // トークン発行
    function setOneTimeToken($_name)
    {
        $seed = "4J9F5I39";
        $tkn = $this->_controller->Auth->password($seed . microtime());
        $this->_controller->Session->delete($_name);
        $this->_controller->Session->write($_name, $tkn);
        return $tkn;
    }
    
    // トークン確認
    function checkOneTimeToken($_name, $_tkn)
    {
        if ($this->_controller->Session->read($_name) == $_tkn) {
            return true;
        }
        return false;
    }
    
    // トークン削除
    function deleteOneTimeToken($_name, $_tkn)
    {
        if ($this->_controller->Session->read($_name) == $_tkn) {
            $this->_controller->Session->delete($_name);
            return true;
        }
        return false;
    }

    /**
     * メール送信関連の処理
     *
     * @param $_target マッチング対象の文字列            
     * @param $_data 調査文字列（配列も可）            
     *
     * @return bool マッチした場合のみtrueを返す
     */
    function send_mail($to, $subject, $body)
    {
        App::import('Model', 'Configuration');
        
        $configuration = new Configuration();
        
        $result = $configuration->find('first', array(
            'conditions' => array(
                'CON_ID' => 1
            )
        ));
        
        App::import('Component', 'Qdmail');
        $mail = new QdmailComponent();
        
        if ($result['Configuration']['STATUS'] == 1) {
            
            $mail->smtp(true);
            
            $protocol = Configure::read('MailProtocolCode');
            
            if ($result['Configuration']['SECURITY'] == 1) {
                $result['Configuration']['HOST'] = "ssl://" . $result['Configuration']['HOST'];
            } elseif ($result['Configuration']['SECURITY'] == 2) {
                $result['Configuration']['HOST'] = "tls://" . $result['Configuration']['HOST'];
            }
            if ($result['Configuration']['PROTOCOL'] == 0) {
                $param = array(
                    'host' => $result['Configuration']['HOST'],
                    'port' => $result['Configuration']['PORT'],
                    'from' => $result['Configuration']['FROM'],
                    'protocol' => $protocol[$result['Configuration']['PROTOCOL']]
                );
            } else 
                if ($result['Configuration']['PROTOCOL'] == 1) {
                    $param = array(
                        'host' => $result['Configuration']['HOST'],
                        'port' => $result['Configuration']['PORT'],
                        'from' => $result['Configuration']['FROM'],
                        'protocol' => $protocol[$result['Configuration']['PROTOCOL']],
                        'user' => $result['Configuration']['USER'],
                        'pass' => $result['Configuration']['PASS']
                    );
                }
            $mail->smtpServer($param);
        }
        // メールを送信するユーザーリストの取得
        $mail->subject($subject);
        $mail->from(array(
            $result['Configuration']['FROM'],
            $result['Configuration']['FROM_NAME']
        ));
        $mail->kana(true);
        $mail->text($body);
        $mail->to($to);
        $mail->errorDisplay(false);
        
        return $mail->send();
    }
}