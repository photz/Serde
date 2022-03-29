<?php

declare(strict_types=1);

namespace Crell\Serde\Attributes;

use Attribute;
use Crell\Serde\TypeMap;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
class StaticTypeMap implements TypeMap
{
    /**
     * @param string $key
     * @param array<string, class-string> $map
     */
    public function __construct(
        public readonly string $key,
        public readonly array $map,
    ) {}

    public function keyField(): string
    {
        return $this->key;
    }

    public function findClass(string $id): ?string
    {
        return $this->map[$id] ?? null;
    }

    public function findIdentifier(string $class): ?string
    {
        return array_search($class, $this->map, true) ?: null;
    }
}