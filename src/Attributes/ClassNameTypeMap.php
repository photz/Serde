<?php

declare(strict_types=1);

namespace Crell\Serde\Attributes;

use Attribute;
use Crell\Serde\TypeMap;

/**
 * A special case of a type map where the class name is its own identifier.
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
class ClassNameTypeMap implements TypeMap
{
    public function __construct(
        public readonly string $key,
    ) {}

    public function keyField(): string
    {
        return $this->key;
    }

    /**
     * @param class-string $id
     * @return class-string|null
     */
    public function findClass(string $id): ?string
    {
        return $id;
    }

    /**
     * @param class-string $class
     * @return class-string|null
     */
    public function findIdentifier(string $class): ?string
    {
        return $class;
    }
}