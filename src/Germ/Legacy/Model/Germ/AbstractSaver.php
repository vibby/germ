<?php

namespace Germ\Legacy\Model\Germ;

use PommProject\Foundation\Pomm;

abstract class AbstractSaver
{
    protected $model;

    public function __construct(
        Pomm $pomm
    ) {
        $this->model = $pomm['germ']->getModel(static::getModelClassName());
    }

    abstract protected static function getModelClassName();

    abstract protected static function getEntityClassName();

    protected static function buildEntity($data)
    {
        if (is_a($data, static::getEntityClassName())) {
            $entity = $data;
        } elseif (is_array($data)) {
            $className = static::getEntityClassName();
            $entity = new $className;
            foreach ($data as $key => $value) {
                $method = 'set' . ucfirst($key);
                $entity->$method($value);
            }
        } else {
            throw \Exception('Cannot understand data to object conversion');
        }

        return $entity;
    }

    public function create($data)
    {
        $entity = self::buildEntity($data);
        $this->model->insertOne($entity);

        return $entity;
    }
}
