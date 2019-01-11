<?php

namespace Src\Server\Database\Eloquent\Traits;

trait AttributeTrait
{
    protected $attributes = [];

    protected $original = [];

    public function setRawAttributes(array $attributes, $sync = false)
    {
        $this->attributes = $attributes;

        if ($sync) {
            $this->syncOriginal();
        }

        return $this;
    }

    public function syncOriginal()
    {
        $this->original = $this->attributes;

        return $this;
    }

    public function getAttribute($key)
    {
        if (!$key) {
            return;
        }

        if (array_key_exists($key, $this->attributes) ||
            $this->hasGetMutator($key)) {
            return $this->getAttributeValue($key);
        }

        if (method_exists(self::class, $key)) {
            return;
        }

        return $this->getRelationValue($key);
    }

    public function hasGetMutator($key)
    {
        return method_exists($this, 'get'. camelize($key). 'Attribute');
    }

    /**
     * Get a plain attribute (not a relationship).
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttributeValue($key)
    {
        $value = $this->getAttributeFromArray($key);

        if ($this->hasGetMutator($key)) {
            return $this->mutateAttribute($key, $value);
        }

        if ($this->hasCast($key)) {
            return $this->castAttribute($key, $value);
        }

        return $value;
    }

    protected function getAttributeFromArray($key)
    {
        if (isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }
    }

    protected function mutateAttribute($key, $value)
    {
        return $this->{'get' . camelize($key) . 'Attribute'}($value);
    }

    public function hasCast($key, $types = null)
    {
        if (array_key_exists($key, $this->getCasts())) {
            return $types ? in_array($this->getCastType($key), (array)$types, true) : true;
        }

        return false;
    }

    public function getCasts()
    {
        return $this->casts;
    }

    protected function getCastType($key)
    {
        return trim(strtolower($this->getCasts()[$key]));
    }

    protected function castAttribute($key, $value)
    {
        if (is_null($value)) {
            return $value;
        }

        switch ($this->getCastType($key)) {
            case 'int':
            case 'integer':
                return (int)$value;
            case 'real':
            case 'float':
            case 'double':
                return (float)$value;
            case 'string':
                return (string)$value;
            case 'bool':
            case 'boolean':
                return (bool)$value;
            case 'object':
                return $this->fromJson($value, true);
            case 'array':
            case 'json':
                return $this->fromJson($value);
            default:
                return $value;
        }
    }

    public function fromJson($value, $asObject = false)
    {
        return json_decode($value, !$asObject);
    }
}