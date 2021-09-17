<?php

declare(strict_types=1);

namespace Atk4\GoogleAddress;

class Property
{
    /** @var string */
    private $addressType;

    /** @var string */
    private $resultProp;

    private function __construct(string $addressType, string $resultProp)
    {
        $this->addressType = $addressType;
        $this->resultProp  = $resultProp;
    }

    public static function of(string $addressType, string $resultProp = 'long_name'): self
    {
        return new static($addressType, $resultProp);
    }

    public function getAddressType(): string
    {
        return $this->addressType;
    }

    public function getResultProperty(): string
    {
        return $this->resultProp;
    }

    public function getDefinition()
    {
        return ['type' => $this->addressType, 'prop' => $this->resultProp];
    }
}
