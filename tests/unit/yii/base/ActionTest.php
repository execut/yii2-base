<?php
/**
 * User: execut
 * Date: 06.07.15
 * Time: 17:51
 */

namespace execut\yii\base;


use execut\TestCase;
use execut\yii\base\Action;
use execut\yii\base\action\Adapter;
use execut\yii\base\action\Params;
use execut\yii\web\Controller;
use yii\base\Module;
use yii\web\Response;
use yii\web\Session;

class ActionTest extends TestCase
{
    public $beforeRunTriggered = false;
    public $afterRunTriggered = false;
    public $beforeRenderTriggered = false;

    public function testGetParams() {
        $action = $this->getAction();
        $this->assertInstanceOf(Params::className(), $action->params);
    }

    public function testRun() {
        $action = $this->getAction();
        $adapter = $this->getMockForAbstractClass(Adapter::className());
        $action->controller->expects($this->once())->method('render')->with('test', ['test'])->will($this->returnValue('test'));

        $adapter->expects($this->exactly(2))->method('_run')->will($this->returnCallback(function () use ($adapter) {
            $this->assertInstanceOf(Params::className(), $adapter->actionParams);
            return ['test'];
        }));

        $action->on('beforeRun', function () {
            $this->beforeRunTriggered = true;
        });
        $action->on('afterRun', function () {
            $this->afterRunTriggered = true;
        });
        $action->on('beforeRender', function () {
            $this->beforeRenderTriggered = true;
        });
        $action->adapter = $adapter;
        $action->view = 'test';
        $this->assertEquals('test', $action->run());
        $this->assertTrue($this->beforeRunTriggered);
        $this->assertTrue($this->afterRunTriggered);
        $this->assertTrue($this->beforeRenderTriggered);

        \yii::$app->response->format = Response::FORMAT_JSON;
        $this->assertEquals(['test'], $action->run());
    }

    public function testGetAdapter() {
        $adapter = $this->getMockForAbstractClass(Adapter::className());
        $action = new Action('id', '');
        $action->adapter = [
            'class' => $adapter->className(),
        ];

        $this->assertInstanceOf($adapter->className(), $action->adapter);
    }

    public function testAddFlashes() {
        $action = $this->getAction();

        $adapter = $this->getMockForAbstractClass(Adapter::className());
        $adapter->method('_run')->will($this->returnCallback(function () use ($adapter) {
            $adapter->flashes[] = 'test';
        }));
        $action->adapter = $adapter;

        $session = $this->getMockBuilder(Session::className())->setMethods([
            'setFlash',
        ])->getMock();
        $session->expects($this->once())->method('setFlash')->with(0, 'test');
        \yii::$app->set('session', $session);
        $action->run();
    }

    public function testRenderWithResponse() {
        $action = $this->getAction();

        $adapter = $this->getMockForAbstractClass(Adapter::className());
        $adapter->method('_run')->will($this->returnCallback(function () use ($adapter) {
            return new Response();
        }));
        $action->view = 'test';
        $action->adapter = $adapter;
        $this->assertInstanceOf(Response::className(), $action->run());
        $this->assertFalse(\yii::$app->layout);
    }

    /**
     * @return \execut\yii\base\Action
     */
    protected function getAction()
    {
        $controller = $this->getMockBuilder(Controller::className())->setMethods(['render', 'getPost', 'getGet', 'isAjax', 'isPjax', 'getFiles'])->setConstructorArgs(['id', new Module('id')])->getMock();

        $action = new Action('id', $controller);
        return $action;
    }
}