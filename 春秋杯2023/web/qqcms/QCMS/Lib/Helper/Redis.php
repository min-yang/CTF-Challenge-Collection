<?php

namespace Helper;

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
class Redis {
	private static $_redis = null;
	public static $s_IsOpen = 2;
	const CACHETIME = 1209600; // 缓存2周
	private static function __getRedis() {
	    if (!isset( self::$_redis )) {
			try {
			    $RedisConfig = \Config::DbConfig('RedisConfig');			    
			    self::$s_IsOpen = ($RedisConfig['IsOpen'] == 1) ? 1 : 2;
			    if(self::$s_IsOpen != 1) return;
			    self::$_redis = new \Redis();
			    self::$_redis->connect ($RedisConfig['Host'], $RedisConfig['Port']);
			    self::$_redis->auth ($RedisConfig['Password']);
			} catch ( \Exception $e ) {
				throw new \Exception( '连接Redis失败！' . $e->getMessage () );
			}
		}
		return self::$_redis;
	}
	
	/**
	 * Keys
	 */
	public static function del($key) {
		return self::__getRedis ()->del ( $key );
	}
	public static function exists($key) {
		return self::__getRedis ()->exists ( $key );
	}
	public static function expire($key, $expire) {
		return self::__getRedis ()->expire ( $key, $expire );
	}
	public static function keys($key) {
		return self::__getRedis ()->keys ( $key );
	}
	public static function ttl($key) {
		return self::__getRedis ()->ttl ( $key );
	}
	public static function type($key) {
	    return self::__getRedis ()->type($key);
	}
	
	/**
	 * String
	 */
	public static function get($key) {
		return self::__getRedis ()->get ( $key );
	}
	public static function set($key, $value) {
		return self::__getRedis ()->set ( $key, $value );
	}
	public static function setex($key, $expire, $value) {
		return self::__getRedis ()->setex ( $key, $expire, $value );
	}
	public static function setnx($key, $value) {
		return self::__getRedis ()->setnx ( $key, $value );
	}
	public static function incr($key) {
		return self::__getRedis ()->incr ( $key );
	}
	public static function decr($key) {
		return self::__getRedis ()->decr ( $key );
	}
	public static function mGet($aKeys) {
		return self::__getRedis ()->mGet ( $aKeys );
	}
	
	/**
	 * Lists
	 */
	public static function lPush($key, $value) {
		return self::__getRedis ()->lPush ( $key, $value );
	}
	public static function rPush($key, $value) {
		return self::__getRedis ()->rPush ( $key, $value );
	}
	public static function lPushx($key, $value) {
		return self::__getRedis ()->lPushx ( $key, $value );
	}
	public static function rPushx($key, $value) {
		return self::__getRedis ()->rPushx ( $key, $value );
	}
	public static function lPop($key) {
		return self::__getRedis ()->lPop ( $key );
	}
	public static function rPop($key) {
		return self::__getRedis ()->rPop ( $key );
	}
	
	/**
	 * Sets
	 */
	public static function sAdd($key, $value) {
		return self::__getRedis ()->sAdd ( $key, $value );
	}
	public static function sRem($key, $value) {
		return self::__getRedis ()->sRem ( $key, $value );
	}
	public static function sIsMember($key, $value) {
		return self::__getRedis ()->sIsMember ( $key, $value );
	}
	public static function sMembers($key) {
		return self::__getRedis ()->sMembers ( $key );
	}
	public static function sRandMember($key, $count) {
		return self::__getRedis ()->sRandMember ( $key, $count );
	}
	
	/**
	 * Sorted Sets
	 */
	public static function zAdd($key, $dScore, $sValue) {
		return self::__getRedis ()->zAdd ( $key, $dScore, $sValue );
	}
	public static function zRange($key, $iStart, $iEnd, $bWithScore = false) {
		return self::__getRedis ()->zRange ( $key, $iStart, $iEnd, $bWithScore );
	}
	public static function zRevRange($key, $iStart, $iEnd, $bWithScore = false) {
		return self::__getRedis ()->zRevRange ( $key, $iStart, $iEnd, $bWithScore );
	}
	public static function zRemRangeByScore($key, $iStart, $iEnd) {
		return self::__getRedis ()->zRemRangeByScore ( $key, $iStart, $iEnd );
	}
	public static function zRem($key, $value) {
		return self::__getRedis ()->zRem ( $key, $value );
	}
	public static function zScore($key, $value) {
		return self::__getRedis ()->zScore ( $key, $value );
	}
	public static function zRevRank($key, $value) {
		return self::__getRedis ()->zRevRank ( $key, $value );
	}
	public static function zRank($key, $value) {
		return self::__getRedis ()->zRank ( $key, $value );
	}
	public static function zSize($key) {
		return self::__getRedis ()->zSize ( $key );
	}
	
	/**
	 * Hashs
	 */
	public static function hSet($key, $sIndex, $value) {
		return self::__getRedis ()->hSet ( $key, $sIndex, $value );
	}
	public static function hMset($key, $values) {
		return self::__getRedis ()->hMset ( $key, $values );
	}
	public static function hSetNx($key, $sIndex, $value) {
		return self::__getRedis ()->hSetNx ( $key, $sIndex, $value );
	}
	public static function hExists($key, $sIndex) {
		return self::__getRedis ()->hExists ( $key, $sIndex );
	}
	public static function hGet($key, $sIndex) {
		return self::__getRedis ()->hGet ( $key, $sIndex );
	}
	public static function hDel($key, $sIndex) {
		return self::__getRedis ()->hDel ( $key, $sIndex );
	}
	public static function hGetAll($key) {
		return self::__getRedis ()->hGetAll ( $key );
	}
	public static function hIncrBy($key, $filed, $value) {
		return self::__getRedis ()->hIncrBy ( $key, $filed, $value );
	}
	
	/**
	 * 设置多个hash字段
	 */
	public static function setHashMulti($key, array $values, $cache = true, $expire = false) {
		$ret = self::hMset ( $key, $values );
		if ($ret) {
			if (true === $cache) {
				if (false === $expire) {
					$expire = self::CACHETIME;
				}
				self::expire ( $key, $expire );
			}
		}
		return $ret;
	}
	public static function setString($key, $value, $cache = true, $expire = false) {
		if (true === $cache) {
			if (false === $expire) {
				$expire = self::CACHETIME;
			}
			return self::setex ( $key, $expire, $value );
		} else {
			return self::set ( $key, $value );
		}
	}
	public static function flushDB() {
		return self::__getRedis ()->flushDB ();
	}
	public static function flushAll() {
		return self::__getRedis ()->flushAll ();
	}
}