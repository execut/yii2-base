<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 7/31/17
 * Time: 3:54 PM
 */

namespace execut\yii\db;


use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

class ActiveQuery extends \yii\db\ActiveQuery
{


    public function __call($name, $params)
    {
        if (strpos($name, 'by') === 0 && ctype_upper(substr($name, 2, 1))) {
            if (empty($params[0])) {
                $values = null;
            } else {
                $values = $params[0];
            }

            if (!empty($params[1])) {
                $tableName = $params[1];
            } else {
                $tableName = null;
            }

            if (!empty($params[2])) {
                $isOr = $params[2];
            } else {
                $isOr = false;
            }

            $attribute = substr($name, 2);
            if (strpos($attribute, '_') !== false) {
                $relationParts = explode('_', $attribute);
                $attribute = array_shift($relationParts);
                $method = 'by' . ucfirst(implode('_', $relationParts));
                if (substr($attribute, strlen($attribute) - 1, 1) !== 's') {
                    $class = $attribute . 's';
                } else {
                    $class = $attribute;
                }

                $namespace = explode('\\queries\\', get_class($this))[0];

                $class = $namespace . '\\' . ucfirst($class);
//                if (empty($values)) {
//                    return $this->andWhere('0=1');
//                }

                $values = $class::find()->$method($values, $tableName, $isOr)->select('id');

                $attribute .= '_id';
            }

            $attribute = Inflector::camel2id($attribute, '_');

            return $this->byAttribute($attribute, $values, $tableName, $isOr);
        }

        return parent::__call($name, $params);
    }

    public function byAttribute($name, $value, $tableName = null, $isOr = false) {
//        if (!$this->owner->hasAttribute($name)) {
//            throw new Exception('Attribute ' . $name . ' not found');
//        }
        if ($tableName === null) {
            $tableName = $this->getTableName();
        }

        if ($isOr) {
            $method = 'orWhere';
        } else {
            $method = 'andWhere';
        }

        return $this->$method([
            $tableName . '.' . $name => $value,
        ]);
    }

    public function getTableName() {
        $class = $this->modelClass;
        return $class::tableName();
    }

    public function forSelect($mainAttribute = 'name', $hasEmpty = true, $idKey = 'id') {
        $list = $this->select([
            $this->getTableName() . '.id',
            $this->getTableName() . '.' . $mainAttribute,
        ])->orderBy($this->getTableName() . '.' . $mainAttribute)->distinct(true)->asArray()->all();
        $result = [];
        if ($hasEmpty) {
            if ($hasEmpty === true) {
                $emptyKey = '';
            } else {
                $emptyKey = $hasEmpty;
            }

            $result[$emptyKey] = '';
        }

        return array_merge($result, ArrayHelper::map($list, $idKey, $mainAttribute));
    }
}