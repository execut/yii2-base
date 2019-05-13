<?php
namespace execut\yii\web;
use yii\base\Widget;
use yii\console\Application;

class AssetBundle extends \yii\web\AssetBundle {
    public $ignoreFileExists = false;
    public $isDebug = false;
    protected $jsIsSet = false;
    protected $cssIsSet = false;
    public function __construct($config = []) {
        if (array_key_exists('js', $config)) {
            $this->jsIsSet = true;
        }

        if (array_key_exists('css', $config)) {
            $this->cssIsSet = true;
        }

        parent::__construct($config);
    }


    public function configureFromClass() {
        if ($this->jsIsSet && $this->cssIsSet) {
            return;
        }

        $class = str_replace('Asset', '', get_class($this));
        $parts = explode('\\', $class);
        $fileName = $parts[count($parts) - 1];
        $parts = array_splice($parts, 0, count($parts) - 1);
        if ($this->basePath === null) {
            $a = new \ReflectionClass(static::class);
            $this->basePath = dirname($a->getFileName());
        }

        $sourcePath = $this->basePath . '/assets';
        $src = \yii::getAlias($sourcePath);
        if (!file_exists($src) && !$this->ignoreFileExists) {
            return;
        }

        $this->sourcePath = $sourcePath;

        $jsFile = $fileName . '.js';
        $jsMinFile = $fileName . '.min.js';
        if ($this->ignoreFileExists || ($this->_fileExists($jsFile))) {
            if (!YII_DEBUG && $this->_fileExists($jsMinFile)) {
                $jsFile = $jsMinFile;
            }

            if (empty($this->js)) {
                $this->js = [];
            }

            $this->js[] = $jsFile;
        }

        $cssFile = $fileName . '.css';
        if (!$this->isDebug && ($this->ignoreFileExists || $this->_fileExists($cssFile))) {
            if (empty($this->css)) {
                $this->css = [];
            }

            $this->css[] = $cssFile;
        } else {
            $cssFile = $fileName . '.less';
            if ($this->ignoreFileExists || $this->_fileExists($cssFile)) {
                if (empty($this->css)) {
                    $this->css = [];
                }

                $this->css[] = $cssFile;
            }
        }
    }

    public function publish($am)
    {
        $this->configureFromClass();

        return parent::publish($am);
    }

    protected function _fileExists($file) {
        return file_exists(\yii::getAlias($this->sourcePath) . '/' . $file);
    }
} 