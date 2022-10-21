<?php

declare(strict_types=1);

namespace Atk4\GoogleAddress\Utils;

/**
 * Components are made with Google address component_type property.
 * Properties store in each build can then be output by the
 * address lookup plugin concatenating each property using the specified
 * glue in order to set input value.
 */
final class Build
{
    /** @var Value[] */
    private $components = [];

    private string $glue = '';

    /**
     * @param Value[] $components
     */
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

    /**
     * @return array{
     *     def: array<int, array{type: string, prop: string}>,
     *     glue:string
     * }
     */
    public function getBuiltValue(): array
    {
        return [
            'def' => array_map(function (Value $value) {
                return $value->getDefinition();
            }, $this->components),
            'glue' => $this->glue,
        ];
    }
}
