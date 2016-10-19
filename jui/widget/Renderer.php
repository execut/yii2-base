<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 10/18/16
 * Time: 12:32 PM
 */

namespace execut\yii\jui\widget;


use execut\yii\helpers\Html;
use execut\yii\jui\Widget;
use yii\base\Component;
use yii\widgets\Pjax;

class Renderer extends Component
{
    /**
     * @var Widget
     */
    public $widget;
    public $isPjax = false;
    public function beginContainer() {
        $options = $this->widget->options;
        if ($this->isPjax) {
            ob_start();
            Pjax::begin([
                'options' => $options,
            ]);
            $result = ob_get_clean();

            return $result;
        }

        return Html::beginTag('div', $options);
    }

    public function endContainer() {
        if ($this->isPjax) {
            ob_start();
            Pjax::end();
            $result = ob_get_clean();

            return $result;
        }

        return Html::endTag('div');
    }
}