<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 8/3/17
 * Time: 4:26 PM
 */

namespace execut\yii\jui;


use execut\yii\jui\widget\Helper;
use execut\yii\jui\widget\Renderer;
use yii\helpers\Html;

trait WidgetTrait
{
    public $isPjax = false;
    public $pjaxId = null;
    public $pjaxOptions = [];
    protected function registerWidget($name = null, $id = null)
    {
        if ($name === null) {
            $name = $this->getDefaultJsWidgetName();
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
            $cssClass = $this->getDefaultCssClass();
            Html::addCssClass($this->options, $cssClass);
        }

        return $this->getRenderer()->beginContainer();
    }

    protected function _endContainer() {
        return $this->getRenderer()->endContainer();
    }

    public function getHelper() {
        return new Helper([
            'widget' => $this,
        ]);
    }

    protected function getDefaultJsWidgetName($className = null)
    {
        return $this->getHelper()->getDefaultJsWidgetName($className);
    }

    protected function _registerBundle()
    {
        $this->getHelper()->registerBundle();
    }

    /**
     * @return string
     */
    protected function getDefaultCssClass()
    {
        return $this->getHelper()->getDefaultCssClass();
    }

    /**
     * @return string
     */
    protected function getBundleClass()
    {
        return $this->getHelper()->getBundleClass();
    }
}