<?php
defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
/*
 * Name : Collection
 * Date : 20120107
 * Author : Qesy
 * QQ : 762264
 * Mail : 762264@qq.com
 *
 * (̅_̅_̅(̲̅(̅_̅_̅_̅_̅_̅_̅_̅()ڪے
 *
 */
abstract class Db {
	public static $s_db_obj;
	protected $p_dbConfig;
	public $sqlSetArr = array (
			'Cond' => array (),
			'Insert' => array (),
			'Update' => array (),
			'Field' => '*',
			'TbName' => 0,
			'Index' => '',
			'Limit' => '',
			'Sort' => array (),
			'IsDebug' => 0 
	);
	
	/*
	 * Name : 构造函数
	 */
	public function __construct() {
	    $this->p_dbConfig = Config::DbConfig();	    
		self::_get_db_config ();
	}
	/*
	 * Name : 析构函数
	 */
	public function __destruct() {
		self::$s_db_obj = null;
	}
	/*
	 * Name : 获取配置
	 */
	private function _get_db_config() {
		if (isset ( self::$s_db_obj )) {
			return self::$s_db_obj;
		}
		try {
		    self::$s_db_obj  = new PDO ('mysql:dbname=' . $this->p_dbConfig ['Name'] . ';host=' . $this->p_dbConfig ['Host'] . '', $this->p_dbConfig ['Accounts'], $this->p_dbConfig ['Password'], array (
		        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		    ));
		    self::$s_db_obj ->exec ( "SET NAMES utf8" );
		} catch ( PDOException $e ) {
			echo 'Connection failed: ' . $e->getMessage ();
			exit ();
		}
		
	}
	
	/*
	 * Name : 查询
	 */
	public function query($sql, $getArr, $fetch_mode = 0) {
		self::p_clean ();
		$sth = self::$s_db_obj->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$sth->execute($getArr);		
		if (empty ( $fetch_mode )) {
		    $rs = $sth->fetchAll ( PDO::FETCH_ASSOC );
		} else {
		    $rs = $sth->fetch ( PDO::FETCH_ASSOC );
		}
		return $rs;
	}
	/*
	 * Name : 获取插入ID
	 */
	public function last_insert_id() {
		return self::$s_db_obj->lastInsertId ();
	}
	/*
	 * Name : 执行
	 */
	public function exec($sql, $getArr) {
		self::p_clean ();
		$sth = self::$s_db_obj->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		return $sth->execute($getArr);
		//return self::$s_db_obj ->exec ( $sql );
	}
	/*
	 * Name : 插入帮助
	 */
	public function get_sql_insert($insert_arr = array()) {
		$insert_arr_t = array ();
		$value_arr_t = array ();
		if (is_array ( $insert_arr )) {
			foreach ( $insert_arr as $key => $val ) {
				$insert_arr_t [] = $key;
				$value_arr_t [] = ':' . $key ;
			}
			return " (" . implode ( ',', $insert_arr_t ) . ") values (" . implode ( ',', $value_arr_t ) . ")";
		}
	}
	/*
	 * Name : 条件帮助
	 */
	public function get_sql_cond($cond_arr = '') {
		if (! is_array ( $cond_arr )) {
			return $cond_arr;
		}
		$cond_arr_t = array ();
		foreach ( $cond_arr as $key => $val ) {			
			if (is_array ( $val )) {
				$cond_arr_t [] = $key . " in (" . self::get_sql_cond_by_in ( $key , $val) . ")";
			} else {
			    //var_dump($key, strpos($key, '>'));
			    $keyStr = $key ;
			    if(strpos($key, '<') !== false) $keyStr = str_replace(array(' ', '<'), array('', ''), $key) .'_1';
			    if(strpos($key, '>') !== false) $keyStr = str_replace(array(' ', '>'), array('', ''), $key).'_2';
			    if(strpos($key, 'LIKE') !== false) {
			        $keyStr = str_replace(array(' ', 'LIKE'), array('', ''), $key).'_3';
			        $cond_arr_t [] = $key . " :" . $keyStr.'' ;
			    }else{
			        $cond_arr_t [] = $key . "=:" . $keyStr ;
			    }
				
			}
		}
		return empty ( $cond_arr_t ) ? '' : ' WHERE ' . implode ( ' AND ', $cond_arr_t );
	}
	/*
	 * Name : IN辅助
	 */
	public function get_sql_cond_by_in($Key, $InArr) {
	    $Arr = array();
	    for($i=0;$i<count($InArr);$i++){
	        $Arr[] = ':'.$Key.'_'.$i;
	    }
		return implode ( ',', $Arr );
	}
	/*
	 * Name : 修改帮助
	 */
	public function get_sql_update($update_arr = array()) {
		$update_arr_t = array();
		if (! is_array ( $update_arr )) {
			return $update_arr;
		}
		foreach ( $update_arr as $key => $val ) {
		    $update_arr_t [] = $key . " = :" . $key;
		}
		return implode ( ',', $update_arr_t );
	}
	
	public function get_execute_arr($update_arr){
	    $Arr = array();
	    foreach($update_arr as $k => $v){
	        if(is_array($v)){
	            for($i=0;$i<count($v);$i++) $Arr[':'.$k.'_'.$i] = $v[$i];
	        }else{
	            $keyStr = $k;
	            if(strpos($k, '<') !== false) $keyStr = str_replace(array(' ', '<'), array('', ''), $k).'_1';
	            if(strpos($k, '>') !== false) $keyStr = str_replace(array(' ',  '>'), array('', ''), $k).'_2';
	            if(strpos($k, 'LIKE') !== false){
	                $keyStr = str_replace(array(' ',  'LIKE'), array('', ''), $k).'_3';
	                $Arr[':'.$keyStr] = '%'.$v.'%';
	            }else{
	                $Arr[':'.$keyStr] = $v;
	            }
	            
	        }	        
	    }
	    return $Arr;
	}
	/*
	 * Name : 设置主键
	 */
	public static function set_index($arr, $key) {
		if (empty ( $arr ))
			return $arr;
		$temp = array ();
		foreach ( $arr as $val ) {
			if (! isset ( $val [$key] )) {
				return $arr;
			}
			$temp [$val [$key]] = $val;
		}
		return $temp;
	}
	/*
	 * Name : 排序帮助
	 */
	public static function sort($sort) {
		$sort_arr = array();
		if (empty ( $sort ))
			return '';
		if (is_array ( $sort )) {
			foreach ( $sort as $key => $val ) {
				$sort_arr [] = $key . ' ' . $val;
			}
			return ' ORDER BY ' . implode ( ',', $sort_arr );
		} else {
			return $sort;
		}
	}
	protected  function p_clean() {
		$this->sqlSetArr = array (
				'Cond' => array (),
				'Insert' => array (),
				'Update' => array (),
				'Field' => '*',
				'TbName' => 0,
				'Index' => '',
				'Limit' => '',
				'Sort' => array (),
				'IsDebug' => 0 
		);
	}
}
?>