<?php

declare(strict_types=1);

namespace Baraja\PhoneNumber;


final class PhoneNumber implements \Stringable
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


	public function __toString(): string
	{
		return $this->getNumber();
	}


	public function getNumber(): string
	{
		return $this->number;
	}


	public function getRegion(): int
	{
		return (int) preg_replace('/^\+(\d+).+?$/', '$1', $this->number);
	}


	public function toHtml(): string
	{
		return str_replace(' ', '&nbsp;', $this->getNumber());
	}


	public function toHtmlLink(): string
	{
		return '<a href="tel:' . str_replace(' ', '', $this->getNumber()) . '">'
			. $this->toHtml()
			. '</a>';
	}
}
