<?php

declare(strict_types=1);

namespace Atk4\GoogleAddress\Utils;

class Property
{
    /** @var string */
    private $addressType;

    /** @var string  Google Map property to use. (long_name or short_name) */
    private $property;

    private function __construct(string $addressType, string $property)
    {
        $this->addressType = $addressType;
        $this->property    = $property;
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

    public function getDefinition()
    {
        return ['type' => $this->addressType, 'prop' => $this->property];
    }
}
