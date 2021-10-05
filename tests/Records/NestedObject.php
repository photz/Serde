<?php

declare(strict_types=1);

namespace Crell\Serde\Records;

use Crell\Serde\Field;

class NestedObject
{
    public function __construct(
        public string $name,
        public ?NestedObject $child = null,
    ) {}
}
