<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 9/26/17
 * Time: 3:59 PM
 */

namespace execut\yii;


use yii\base\Exception;
use yii\base\Widget;

class OnceWidget extends Widget
{
    public $widget = [];
    protected static $alreadyRenderedWidgets = [];
    public function run()
    {
        $widgets = $this->widget;
        if (!is_array($widgets)) {
            $class = $widgets;
            $widgets = [
                [
                    'class' => $class,
                ],
            ];
        } else if (isset($widgets['class'])) {
            $widgets = [$widgets];
        }

        $result = '';
        foreach ($widgets as $widget) {
            if (!isset($widget['class'])) {
                throw new Exception('Widget class required in widget config');
            }

            $class = $widget['class'];

            if (isset(self::$alreadyRenderedWidgets[$class])) {
                return;
            }

            self::$alreadyRenderedWidgets[$class] = true;

            $result .= $class::widget($widget);
        }

        return $result;
    }
}