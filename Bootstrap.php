<?php
/**
 */

namespace execut\yii;


use yii\base\BaseObject;
use yii\base\BootstrapInterface;
use yii\helpers\ArrayHelper;

class Bootstrap extends BaseObject implements BootstrapInterface
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
        $this->_depends = $depends;
    }

    protected static $boostrapped = [];
    public function bootstrap($app)
    {
        $bootstraps = [];
        foreach ($this->getDepends() as $key => $depends) {
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
                $module = $app->getModule($bootstrap);
                if ($module instanceof BootstrapInterface) {
                    $module->bootstrap($app);
                }
            } else {
                $bootstrapKey = $bootstrap['class'];
                if (array_key_exists('moduleId', $bootstrap)) {
                    $bootstrapKey .= '-' . $bootstrap['moduleId'];
                }

                if (in_array($bootstrapKey, self::$boostrapped)) {
                    continue;
                }

                self::$boostrapped[] = $bootstrap['class'];
                $bootstrap = \yii::createObject($bootstrap);
                $bootstrap->bootstrap($app);
            }
        }
    }
}