<?php

declare(strict_types=1);

namespace Atk4\GoogleAddress\Utils;

/**
 * Component are made with Google address component_type property.
 * Properties store in each component can then be output by the
 * address lookup plugin concatenating each property using the specified
 * glue to set input value.
 */
class Build
{
    /** @var Value[]  */
    private $components = [];

    /** @var string  */
    private $glue = '';

    private function __construct(array $components)
    {
        $this->components = $components;
    }

    public static function with(Value $def): self
    {
        return new static([$def]);
    }

    public function concat(Value $def): self
    {
        $this->components[] = $def;

        return $this;
    }

    public function glueWith(string $glue): self
    {
        $this->glue = $glue;

        return $this;
    }

    public function getBuiltValue(): array
    {
        $val = [];
        foreach ($this->components as $prop) {
            $val['def'][] = $prop->getDefinition();
        }
        if ($this->glue) {
            $val['glue'] = $this->glue;
        }

        return $val;
    }
}
