<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 10/18/16
 * Time: 12:09 PM
 */

namespace execut\yii\jui\widget;


use execut\TestCase;
use execut\yii\jui\Widget;

class RendererTest extends TestCase
{
    public function testBeginContainer() {
        $widget = new Widget();
        $renderer = new Renderer([
            'widget' => $widget,
        ]);
        $this->assertEquals('<div id="' . $widget->id . '">', $renderer->beginContainer());
    }

    public function testEndContainer() {
        $widget = new Widget();
        $renderer = new Renderer([
            'widget' => $widget,
        ]);
        $this->assertEquals('</div>', $renderer->endContainer());
    }

    public function testPjaxBegin() {
        $widget = new Widget([
            'id' => 'test',
            'pjaxOptions' => [
                'id' => 'pjaxContainer'
            ],
        ]);
        $renderer = new Renderer([
            'widget' => $widget,
            'isPjax' => true,
        ]);
        $this->assertEquals('<div id="pjaxContainer" data-pjax-container="" data-pjax-push-state data-pjax-timeout="1000"><div id="test">', $renderer->beginContainer());
    }
}