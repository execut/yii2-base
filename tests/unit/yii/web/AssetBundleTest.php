<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 08.10.14
 * Time: 12:12
 */

namespace execut\yii\web;


use execut\TestCase;

class AssetBundleTest extends TestCase
{
    public function testConfigureFromClass()
    {
        $bundle = new AssetBundle();
        $bundle->ignoreFileExists = true;
        $bundle->configureFromClass();
        $expected = realpath(__DIR__ . '/../../../../web');
        $this->assertEquals($expected, $bundle->basePath);
        $this->assertEquals($expected . '/assets', $bundle->sourcePath);
        $this->assertEquals(['Bundle.js'], $bundle->js);
        $this->assertEquals(['Bundle.css'], $bundle->css);

        $bundle = new AssetBundle();
        $bundle->js[] = 'test.js';
        $bundle->configureFromClass();
        $this->assertEquals(['test.js'], $bundle->js);
        $this->assertEquals([], $bundle->css);
    }
}