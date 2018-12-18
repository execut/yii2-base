<?php
/**
 */

namespace execut\yii;


use yii\base\Application;
use yii\base\BaseObject;
use yii\base\BootstrapInterface;
use yii\helpers\ArrayHelper;
use yii\i18n\PhpMessageSource;

class Bootstrap extends BaseObject implements BootstrapInterface
{
    protected $_defaultDepends = [];
    protected $isBootstrapI18n = false;
    public $vendorNamespace = 'execut';
    protected const CORE_COMPONENTS = [
        'user',
        'session',
        'log',
        'view',
        'formatter',
        'i18n',
        'mailer',
        'urlManager',
        'assetManager',
        'request',
        'errorHandler',
        'security',
    ];

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
    protected static $coreComponents = [];

    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        if ($this->isBootstrapI18n) {
            $this->bootstrapI18n($app);
        }

        $bootstraps = [];
        foreach ($this->getDepends() as $key => $depends) {
            foreach ($depends as $name => $depend) {
                switch ($key) {
                    case 'bootstrap':
                        $bootstraps[$name] = $depend;
                    break;
                    case 'modules':
                        if (!$app->hasModule($name)) {
                            $app->setModule($name, $depend);
                        }
                    break;
                    case 'components':
                        if (in_array($name, self::CORE_COMPONENTS) || !$app->has($name)) {
                            if (in_array($name, self::CORE_COMPONENTS)) {
                                if (!empty(self::$coreComponents[$name])) {
                                    break;
                                }

                                self::$coreComponents[$name] = true;
                            }

                            $app->set($name, $depend);
                        }
                    break;
                }
            }
        }

        foreach ($bootstraps as $bootstrapKey => $bootstrap) {
            if (is_string($bootstrap)) {
                $module = $app->getModule($bootstrap);
                if ($module instanceof BootstrapInterface) {
                    $module->bootstrap($app);
                }
            } else if (is_callable($bootstrap)) {
                if (in_array($bootstrapKey, self::$boostrapped)) {
                    continue;
                }

                $bootstrap();
                self::$boostrapped[] = $key;
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

    public function bootstrapI18n($app) {
        $className = static::class;
        $reflector = new \ReflectionClass($className);
        $fileName = $reflector->getFileName();
        $level = 0;
        $baseFolder = $this->vendorNamespace;
        while (pathinfo(dirname($fileName), PATHINFO_BASENAME) !== $baseFolder) {
            if ($level > 2) {
                return;
            }

            $fileName = dirname($fileName);
            $level++;
        }

        $fileName = pathinfo($fileName, PATHINFO_BASENAME);
        $moduleName = explode('\\', $className)[1];
        $app->i18n->translations['execut/' . $moduleName] = [
            'class' => PhpMessageSource::class,
            'basePath' => '@vendor/' . $baseFolder . '/' . $fileName . '/messages',
            'sourceLanguage' => 'en-US',
            'fileMap' => [
                'execut/' . $moduleName => $moduleName . '.php',
            ],
        ];
    }
}