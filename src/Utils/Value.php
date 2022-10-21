<?php

declare(strict_types=1);

namespace Atk4\GoogleAddress\Utils;

final class Value
{
    private string $addressType;

    /** Google Map property to use. (long_name or short_name) */
    private string $property;

    private function __construct(string $addressType, string $property)
    {
        $this->addressType = $addressType;
        $this->property = $property;
    }

    public static function of(string $addressType, string $property = 'long_name'): self
    {
        return new static($addressType, $property);
    }

    public function getAddressType(): string
    {
        return $this->addressType;
    }

    public function getProperty(): string
    {
        return $this->property;
    }

    /**
     * @return array{type: string, prop: string}
     */
    public function getDefinition(): array
    {
        return ['type' => $this->addressType, 'prop' => $this->property];
    }
}
