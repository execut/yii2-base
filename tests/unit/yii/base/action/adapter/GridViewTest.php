<?php
/**
 * User: execut
 * Date: 07.07.15
 * Time: 10:05
 */

namespace execut\yii\base\action\adapter;


use execut\TestCase;
use yii\base\Model;
use yii\data\ArrayDataProvider;

class GridViewTest extends TestCase
{
    public function testGetDataProvider()
    {
        $dataProvider = new ArrayDataProvider();
        $dataProvider->models = [
            [
                'id' => 1,
                'test_text' => 'test',
            ]
        ];

        $filter = new GridViewTestFilter;
        $filter->dataProvider = $dataProvider;

        $formValue = [
            $filter->formName() => [
                'attribute' => 'test',
            ],
        ];

        $adapter = new GridView();
        $adapter->attributes = [];
        $adapter->setActionParams([
            'get' => $formValue,
        ]);
        $adapter->model = $filter;
        $this->assertEquals([
            'filter' => $filter,
            'dataProvider' => $dataProvider,
        ], $adapter->run());

        $adapter->actionParams->isAjax = true;

        $adapter->actionParams->post = $formValue;
        $adapter->attributes = [
            'id',
            'text' => 'test_text'
        ];
        $result = $adapter->run();
        $this->assertEquals([
            'results' => [
                [
                    'id' => 1,
                    'text' => 'test',
                ]
            ]
        ], $result);
    }
}

class GridViewTestFilter extends Model {
    public $attribute;
    protected $dataProvider = null;
    public function rules() {
        return [
            [['attribute'], 'required']
        ];
    }

    public function setDataProvider($dp) {
        $this->dataProvider = $dp;
    }

    public function getDataProvider() {
        return $this->dataProvider;
    }
}