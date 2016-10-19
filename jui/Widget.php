<?php
namespace execut\yii\jui;
use execut\yii\jui\widget\Renderer;
use yii\helpers\Html;
use yii\helpers\Inflector;

class Widget extends \yii\jui\Widget {
    public $isPjax = false;
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

    /**
     * @var Renderer
     */
    protected $renderer = null;
    protected function getRenderer() {
        if ($this->renderer === null) {
            $this->renderer = new Renderer([
                'widget' => $this,
                'isPjax' => $this->isPjax,
            ]);
        }

        return $this->renderer;
    }

    protected function _beginContainer() {
        $options = $this->options;

        if (empty($options['class'])) {
            $cssClass = $this->getCssClass();
            Html::addCssClass($this->options, $cssClass);
        }

        return $this->getRenderer()->beginContainer();
    }

    protected function _endContainer() {
        return $this->getRenderer()->endContainer();
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

    /**
     * @return string
     */
    protected function getCssClass()
    {
        $parts = explode('\\', get_class($this));
        $cssClass = Inflector::camel2id($parts[count($parts) - 1]);
        return $cssClass;
    }
} 