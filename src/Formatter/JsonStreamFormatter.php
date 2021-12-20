<?php

declare(strict_types=1);

namespace Crell\Serde\Formatter;

use Crell\Serde\ClassDef;
use Crell\Serde\CollectionItem;
use Crell\Serde\Dict;
use Crell\Serde\Field;
use Crell\Serde\Sequence;
use Crell\Serde\Serializer;

class JsonStreamFormatter implements Formatter
{
    public function format(): string
    {
        return 'json-stream';
    }

    public function initialField(Serializer $serializer, string $type): Field
    {
        return Field::create('root', $type);
    }

    public function serializeInitialize(ClassDef $classDef): FormatterStream
    {
        return FormatterStream::new(fopen('php://temp/', 'wb'));
    }

    /**
     * @param FormatterStream $runningValue
     */
    public function serializeFinalize(mixed $runningValue, ClassDef $classDef): FormatterStream
    {
        return $runningValue;
    }

    /**
     * @param FormatterStream $runningValue
     */
    public function serializeInt(mixed $runningValue, Field $field, int $next): FormatterStream
    {
        $runningValue->write((string)$next);
        return $runningValue;
    }

    /**
     * @param FormatterStream $runningValue
     */
    public function serializeFloat(mixed $runningValue, Field $field, float $next): FormatterStream
    {
        $runningValue->write((string)$next);
        return $runningValue;
    }

    /**
     * @param FormatterStream $runningValue
     */
    public function serializeString(mixed $runningValue, Field $field, string $next): FormatterStream
    {
        return $runningValue->printf('"%s"', $next);
    }

    /**
     * @param FormatterStream $runningValue
     */
    public function serializeBool(mixed $runningValue, Field $field, bool $next): FormatterStream
    {
        $runningValue->write($next ? 'true' : 'false');
        return $runningValue;
    }

    /**
     * @param FormatterStream $runningValue
     */
    public function serializeSequence(mixed $runningValue, Field $field, Sequence $next, Serializer $serializer): FormatterStream
    {
        $runningValue->write('[');

        $isFirst = true;

        /** @var CollectionItem $item */
        foreach ($next->items as $item) {
            if (!$isFirst) {
                $runningValue->write(',');
            }
            $isFirst = false;
            $serializer->serialize($item->value, $runningValue->unnamedContext(), $item->field);
        }

        $runningValue->write(']');

        return $runningValue;
    }

    /**
     * @param FormatterStream $runningValue
     */
    public function serializeDictionary(mixed $runningValue, Field $field, Dict $next, Serializer $serializer): FormatterStream
    {
        if ($runningValue->isNamedContext()) {
            $runningValue->write("\"$field->serializedName\":");
        }

        $runningValue->write('{');

        $isFirst = true;

        /** @var CollectionItem $item */
        foreach ($next->items as $item) {
            if (!$isFirst) {
                $runningValue->write(',');
            }
            $isFirst = false;
            $runningValue->printf('"%s":', $item->field->serializedName);
            $serializer->serialize($item->value, $runningValue->unnamedContext(), $item->field);
        }

        foreach ($field->extraProperties as $k => $v) {
            if (!$isFirst) {
                fwrite($runningValue, ',');
            }
            $runningValue->printf('"%s":"%s"\n', $k, $v);
        }

        $runningValue->write('}');

        return $runningValue;
    }

    public function serializeObject(mixed $runningValue, Field $field, Dict $next, Serializer $serializer): FormatterStream
    {
        return $this->serializeDictionary($runningValue, $field, $next, $serializer);
    }

}
