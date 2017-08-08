<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 8/3/17
 * Time: 4:25 PM
 */

namespace execut\yii\jui;


use execut\TestCase;
use execut\yii\jui\widget\Helper;

class WidgetTraitTest extends TestCase
{
    use WidgetTrait;
    public function testGetHelper() {
        $this->assertInstanceOf(Helper::class, $this->getHelper());
        $this->assertEquals($this, $this->getHelper()->widget);
    }
}