<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 8/15/14
 * Time: 9:36 AM
 */

namespace execut\yii;

trait AppCacheTrait {
    use CacheTrait;
    protected function _initCache() {
        if ($this->_cache === null) {
            $this->_cache = \yii::$app->cache;
            $this->_cache->gc();
        }
    }

    /**
     * @param $value
     * @param $key
     */
    protected function _setCacheByKey($value, $key)
    {
        $this->_cache->set($key, $value, $this->_getCacheDuration());
    }

    protected function _getCacheDuration() {
        return 0;
    }
} 