<?php
namespace execut\yii\web;
use yii\base\Widget;

class AssetBundle extends \yii\web\AssetBundle {
    public $ignoreFileExists = false;
    public $isDebug = false;
    public function configureFromClass() {
        $class = str_replace('Asset', '', get_class($this));
        $parts = explode('\\', $class);
        $fileName = $parts[count($parts) - 1];
        $parts = array_splice($parts, 0, count($parts) - 1);
        $this->basePath = '@' . implode('/', $parts);
        $this->sourcePath = $this->basePath . '/assets';
        $src = \yii::getAlias($this->sourcePath);
        $jsFile = $fileName . '.js';
        if ($this->ignoreFileExists || $this->_fileExists($jsFile)) {
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