<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 17.06.16
 * Time: 17:06
 */

namespace execut\yii\bash;


use yii\base\Component;

class Command extends Component
{
    public $params = null;
    public $values = null;
    public function __toString()
    {
        $command = '';
        $params = $this->params;
        if (!is_array($params)) {
            $params = [$params];
        }

        foreach ($params as $paramKey => $paramValue) {
            if (is_int($paramKey)) {
                $paramKey = '';
            } else {
                $paramKey = '-' . $paramKey;
                $valuesKey = str_replace(['{', '}'], '', $paramValue);
                if (empty($this->values[$valuesKey]) ) {
                    continue;
                }
            }

            $command .= ' ' . $paramKey . $paramValue;
        }

        $values = [];
        foreach ($this->values as $key => $value) {
            if (is_array($value)) {
                $value = implode(' ', $value);
            }

            $values['{' . $key . '}'] = $value;
        }

        $command = strtr($command, $values);

        return $command;
    }

    public function execute() {
        return exec($this->__toString());
    }
}