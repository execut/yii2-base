# yii2-base
My base classes for yii2

# execut\yii\jui\Widget
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
(function(){
    $.widget("customNamespace.CustomWidget", {
        form : null,
        _create: function () {
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