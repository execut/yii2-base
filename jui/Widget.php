<?php
namespace execut\yii\jui;
use yii\helpers\Html;
use yii\helpers\Inflector;

class Widget extends \yii\jui\Widget {
    protected function registerWidget($name = null, $id = null)
    {
        if ($name === null) {
            $name = $this->_getUnnamespacedClassName();
        }

        $this->_registerBundle();

        parent::registerWidget($name, $id);
    }

    protected function _renderContainer($content = '') {
        return $this->_beginContainer() . $content . $this->_endContainer();
    }

    protected function _beginContainer() {
        $options = $this->options;

        if (empty($options['class'])) {
            $parts = explode('\\', get_class($this));
            $cssClass = Inflector::camel2id($parts[count($parts) - 1]);
            Html::addCssClass($options, $cssClass);
        }

        return Html::beginTag('div', $options);
    }

    protected function _endContainer() {
        return Html::endTag('div');
    }

    protected function _getUnnamespacedClassName($className = null)
    {
        if ($className === null) {
            $className = get_class($this);
        }

        $parts = explode('\\', $className);
        $name = $parts[count($parts) - 1];
        return $name;
    }

    protected function _registerBundle()
    {
        $bundleClass = get_class($this) . 'Asset';
        if (class_exists($bundleClass)) {
            $bundleClass::register($this->view);
        }
    }
} 