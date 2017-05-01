<?php
/**
 */

namespace execut\yii;


use yii\base\BootstrapInterface;
use yii\base\Object;
use yii\helpers\ArrayHelper;

class Bootstrap extends Object implements BootstrapInterface
{
    protected $_defaultDepends = [];

    public function getDefaultDepends() {
        return $this->_defaultDepends;
    }

    protected $_depends = [
        'bootstrap' => [],
        'modules' => [],
        'components' => [],
    ];

    public function getDepends() {
        return ArrayHelper::merge($this->defaultDepends, $this->_depends);
    }

    public function setDepends($depends) {
        $this->depends = $depends;
    }
    public function bootstrap($app)
    {
        $bootstraps = [];
        foreach ($this->depends as $key => $depends) {
            foreach ($depends as $name => $depend) {
                switch ($key) {
                    case 'bootstrap':
                        $bootstraps[] = $depend;
                    break;
                    case 'modules':
                        if (!$app->hasModule($name)) {
                            $app->setModule($name, $depend);
                        }
                    break;
                    case 'components':
                        if (!$app->has($name)) {
                            $app->set($name, $depend);
                        }
                    break;
                }
            }
        }

        foreach ($bootstraps as $bootstrap) {
            if (is_string($bootstrap)) {
                $app->getModule($bootstrap);
            } else {
                $bootstrap = \yii::createObject($bootstrap);
                $bootstrap->bootstrap($app);
            }
        }
    }
}