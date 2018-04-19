# yii2-base
My base classes for yii2

# Bootstrap system
## execut\yii\Bootstrap

Этот класс необходим для возможности иерархического запуска компонентов для различных модулей. Допустим есть модуль
users, для своей работы он требует [навигацию execut/yii2-navigation](https://github.com/execut/yii2-navigation) и
модуль CRUD [execut/yii2-crud](https://github.com/execut/yii2-crud). Их необходимо запустить до запуска основного
модуля users. При этом самим этим компонентам нужно запускать ещё комоненты для своей работы. Например,
[execut/yii2-crud](https://github.com/execut/yii2-crud) требует пакет [execut/yii2-actions](https://github.com/execut/yii2-actions).
С помощью execut\yii\Bootstrap компоненты рекурсивно сами подцепят себе всё необходимое и не нужно об этом заботиться.
Нам-же нужно запустить модуль users и указать от запуска каких компонентов он зависит. Для этого необходимо реализовать
потомка класса \execut\yii\Bootstrap, объявив в нём все зависимости модуля users и саму настройку модуля users:
```php
<?php
namespace execut\users;


class Bootstrap extends \execut\yii\Bootstrap
{
    public function getDefaultDepends()
    {
        return [
            'bootstrap' => [
                'yii2-navigation' => [
                    'class' => \execut\navigation\Bootstrap::class,
                ],
                'yii2-crud' => [
                    'class' => \execut\crud\Bootstrap::class,
                ],
            ],
            'modules' => [
                'users' => [
                    'class' => Module::class,
                ],
            ],
        ];
    }
    
    public function bootstrap($app)
    {
        parent::bootstrap($app); // Здесь можно задать код для бутстрапа модуля. Родителя вызвать обязательно
    }
}
```

После этого добавляем в конфигурацию приложения запуск модуля users. Прелесть бутрапа в том, что сам модуль как и все
необходимые компоненты указывать в конфигурации приложения не нужно, поскольку это всё и так объявлено в бутстрапе:
```php
...
'bootstrap' => [
    'users' => [
        'class' => \execut\users\Bootsrap::class,
    ],
],
...
```
Если ещё необходимо задать модулю параметры окружения приложения, то указываем их через директиву depends в конфиге:
```php
...
'bootstrap' => [
    'users' => [
        'class' => \execut\users\Bootsrap::class,
        'depends' => [
            'modules' => [
                'users' => [
                    'defaultRoute' => '/users'
                ],
            ],
        ],
    ],
],
...
```

# Widgets system
## execut\yii\jui\Widget
This class provides the ability to simplify create jquery widgets with assets files if you want. To create a widget, you must create files in follow structure:

- CustomWidget.php
- CustomWidgetAsset.php
- assets\CustomWidget.js
- assets\CustomWidget.css

1. CustomWidget.php

Create class for you widget:
```php
class CustomWidget extends \execut\yii\jui\Widget
{
    public function run()
    {
        /**
         * If you want append assets files and create javascript widget instance
         */
        $this->registerWidget();
        
        /**
         * Or if you want only append assets files
         */
        $this->_registerBundle();

        /**
         * renderContainer - helper method for wrapping widget in div container with defined in widget options
         */
        $result = $this->renderContainer($this->renderWidget());

        return $result;
    }
    
    protected function renderWidget() {
        /**
         * Here generate widget out $result
         */
         
         return $result;
    }
}
```
2. CustomWidgetAsset.php

Define asset bundle class in same folder and name with postfix Asset
```php
class CustomWidgetAsset extends \execut\yii\web\AssetBundle
{
}
```
3. assets\CustomWidget.js

If you want javascript functional, create jquery widget file assets\CustomWidget.js:
```javascript
(function () {
    $.widget("customNamespace.CustomWidget", {
        _create: function () {
            var t = this;
            t._initElements();
            t._initEvents();
        },
        _initElements: function () {
            var t = this,
                el = t.element,
                opts = t.options;
        },
        _initEvents: function () {
            var t = this,
                el = t.element,
                opts = t.options;
        }
    });
}());
```
4. assets\CustomWidget.css

If you want css styles, create file assets\CustomWidget.css:
```css
.custom-widget {
}
```