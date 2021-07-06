<?php

declare(strict_types=1);

namespace Baraja\PhoneNumber;


final class PhoneNumber
{
	private string $number;


	public function __construct(string $phone, int $region)
	{
		$this->number = PhoneNumberFormatter::fix($phone, $region);
	}


	public static function from(string $phone, int $region = 420): self
	{
		return new self($phone, $region);
	}


	public function getNumber(): string
	{
		return $this->number;
	}


	public function getRegion(): int
	{
		return (int) preg_replace('/^+(\d+).+?$/', '$1', $this->number);
	}
}
