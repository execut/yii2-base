<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 8/8/14
 * Time: 1:42 PM
 */

namespace execut\yii;


use yii\helpers\Json;

define('CACHE_DEFAULT_VALUE', md5('asdasdasdasdas das das das das'));
trait CacheTrait {
    protected $_cache = null;
    protected static $_staticCache = null;
    protected function _initCache() {
        if ($this->_cache === null) {
            $this->_cache = [];
        }
    }

    protected function _setCache() {
        $this->_initCache();
        $arguments = func_get_args();
        $value = array_pop($arguments);

        if (count($arguments) === 0) {
            $mainKey = get_class($this);
        } else {
            $mainKey = array_shift($arguments);
        }

        if (count($arguments) === 0) {
            $resultCache = $value;
        } else {
            $resultCache = $this->_getCache($mainKey);
            if (empty($resultCache)) {
                $resultCache = [];
            }

            $cache = &$resultCache;
            foreach ($arguments as $index => $key) {
                if (!isset($cache[$key])) {
                    $cache[$key] = [];
                }

                if ($index === count($arguments) - 1) {
                    $cache[$key] = $value;
                }

                $cache = &$cache[$key];
            }
        }

        $this->_setCacheByKey($resultCache, $mainKey);

        return $value;
    }

    protected function _deleteCache() {
        $this->_initCache();
        $arguments = func_get_args();

        if (count($arguments) === 0) {
            $mainKey = get_class($this);
        } else {
            $mainKey = array_shift($arguments);
        }

        if (count($arguments) === 0) {
            unset($this->_cache[$mainKey]);
        } else {
            $resultCache = $this->_getCache($mainKey);
            if (empty($resultCache)) {
                $resultCache = [];
            }

            $cache = &$resultCache;
            $isChanged = false;
            foreach ($arguments as $index => $key) {
                if (isset($cache[$key])) {
                    if ($index === count($arguments) - 1) {
                        unset($cache[$key]);
                        $isChanged = true;
                    } else {
                        $cache = &$cache[$key];
                    }
                }
            }

            if ($isChanged) {
                $this->_setCacheByKey($resultCache, $mainKey);
            }
        }
    }

    protected function _getCache() {
        $this->_initCache();

        $keys = func_get_args();
        if (count($keys) === 0) {
            $keys = [get_class($this)];
        }

        $cache = $this->_cache;
        foreach ($keys as $key) {
            if (!isset($cache[$key])) {
                return null;
            }

            $cache = $cache[$key];
        }

        return $cache;
    }

    /**
     * @param $keys
     * @return string
     */
    protected function _collectKeys($keys)
    {
        if (count($keys) === 0) {
            $keys = [get_class($this)];
        }

        return implode('-', $keys) . '-' . implode('-', array_keys($keys));
    }

    /**
     * @param $value
     * @param $key
     */
    protected function _setCacheByKey($value, $key)
    {
        $this->_cache[$key] = $value;
    }

    protected function _cache(callable $callback, $key = null) {
        if ($key === null) {
            $key = spl_object_hash($callback);
        }

        $this->_initCache();
        if (isset($this->_cache[$key])) {
            return $this->_cache[$key];
        }

        $result = $callback();
        $this->_cache[$key] = $result;

        return $result;
    }

    protected static function _cacheStatic(callable $callback, $key = null) {
        if ($key === null) {
            $key = var_export($callback, true);
        }

//        if (!\yii::$app->session->getId())
//        $session = \yii::$app->session;
//        $session->open();
//        $key .= $session->getId();

        if (isset(self::$_staticCache[$key])) {
            return self::$_staticCache[$key];
        }

        $result = $callback();
        self::$_staticCache[$key] = $result;

        return $result;
    }
} 