<?php

declare(strict_types=1);

namespace Baraja\PhoneNumber;


final class PhoneNumberValidator
{
	public static function isValid(string $phone, int $region = 420): bool
	{
		try {
			return PhoneNumberFormatter::fix($phone, $region) !== '';
		} catch (\Throwable) {
		}

		return false;
	}
}
