<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 10/18/16
 * Time: 12:32 PM
 */

namespace execut\yii\jui\widget;


use yii\helpers\Html;
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
        $result = '';
        if ($this->isPjax) {
            ob_start();
            Pjax::begin([
                'id' => $this->widget->pjaxId,
                'options' => $this->widget->pjaxOptions,
            ]);
            $result .= ob_get_clean();
        }

        return $result . Html::beginTag('div', $options);
    }

    public function endContainer() {
        if ($this->isPjax) {
            ob_start();
            Pjax::end();
            echo Html::endTag('div');
            $result = ob_get_clean();

            return $result;
        }

        return Html::endTag('div');
    }
}
