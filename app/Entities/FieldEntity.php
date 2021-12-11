<?php

namespace App\Entities;

use App\Enums\FieldTypeEnum;
use App\Exceptions\FieldEntityException;

class FieldEntity
{
    private string $name;
    private string $type;
    private bool $required = false;
    private bool $unique = false;
    private bool $queryable = false;
    private bool $readonly = false;
    public mixed $default = null;

    /**
     * @param string $name
     * @param string $type
     * @throws FieldEntityException
     */
    public function __construct(string $name, string $type)
    {
        if (empty($name)){
            throw new FieldEntityException("Field name cannot be empty");
        }
        $this->name = $name;
        if (!in_array($type, FieldTypeEnum::getAllTypes())) {
            throw new FieldEntityException("Unknown field type " . $type . ". Possible types are: " . implode(", ", FieldTypeEnum::getAllTypes()));
        }
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @return bool
     */
    public function isUnique(): bool
    {
        return $this->unique;
    }

    /**
     * @return bool
     */
    public function isQueryable(): bool
    {
        return $this->queryable;
    }

    /**
     * @return bool
     */
    public function isReadonly(): bool
    {
        return $this->readonly;
    }

    /**
     * @return mixed
     */
    public function getDefault(): mixed
    {
        return $this->default;
    }

    /**
     * @param bool $required
     * @return FieldEntity
     */
    public function setRequired(bool $required): FieldEntity
    {
        $this->required = $required;
        return $this;
    }

    /**
     * @param bool $unique
     * @return FieldEntity
     */
    public function setUnique(bool $unique): FieldEntity
    {
        $this->unique = $unique;
        return $this;
    }

    /**
     * @param bool $queryable
     * @return FieldEntity
     */
    public function setQueryable(bool $queryable): FieldEntity
    {
        $this->queryable = $queryable;
        return $this;
    }

    /**
     * @param bool $readonly
     * @return FieldEntity
     */
    public function setReadonly(bool $readonly): FieldEntity
    {
        $this->readonly = $readonly;
        return $this;
    }

    /**
     * @param mixed|null $default
     */
    public function setDefault(mixed $default): FieldEntity
    {
        $this->default = $this->castDefault($default);
        return $this;
    }

    /**
     * @return array
     */
    public function asArray(): array
    {
        return [
            "name" => $this->name,
            "type" => $this->type,
            "required" => $this->required,
            "unique" => $this->unique,
            "queryable" => $this->queryable,
            "readonly" => $this->readonly,
            "default" => $this->default
        ];
    }

    /**
     * @param mixed $default
     * @return bool|int|string|null
     */
    private function castDefault(mixed $default): bool|int|string|null
    {
        if (is_null($default)) {
            return null;
        }
        if ($this->type == FieldTypeEnum::TYPE_BOOLEAN) {
            return (bool)$default;
        }
        if ($this->type == FieldTypeEnum::TYPE_INTEGER) {
            return (int)$default;
        }
        return (string)$default;
    }


}
