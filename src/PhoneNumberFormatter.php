<?php

declare(strict_types=1);

namespace Baraja\PhoneNumber;


final class PhoneNumberFormatter
{
	/**
	 * Normalize phone to basic format if pattern match.
	 *
	 * @param int $region use this prefix when number prefix does not exist
	 */
	public static function fix(string $phone, int $region = 420): string
	{
		if ($region < 0) {
			throw new \InvalidArgumentException(sprintf('Region can not be negative number, but "%d" given.', $region));
		}
		$phone = (string) preg_replace('/[^\d+]/', '', $phone); // remove spaces
		$phone = (string) preg_replace('/^0([1-9])/', '$1', $phone); // remove starting zero
		if (preg_match('/^(?:0{2,}|\+)\s*([1-9]\d{2})\s*(.+)$/', $phone, $phoneRegionParser) === 1) {
			$region = (int) ($phoneRegionParser[1] ?? throw new \LogicException('Invalid phone prefix.'));
			$phone = sprintf('+%s%s', $region, $phoneRegionParser[2] ?? '');
		}
		if (preg_match('/^([+0-9]+)/', $phone, $trimUnexpected) === 1) { // remove user notice and unexpected characters
			$phone = (string) $trimUnexpected[1];
		}
		if ($region >= 100 && $region <= 999 && str_starts_with($phone, (string) $region)) {
			$phoneWithoutPrefix = substr($phone, 3);
			/** @phpstan-ignore-next-line */
			assert(is_string($phoneWithoutPrefix));
			$phone = $phoneWithoutPrefix === '' ? $phone : $phoneWithoutPrefix;
		}
		if (preg_match('/^\+(4\d{2})(\d{3})(\d{3})(\d{3})$/', $phone, $prefixParser) === 1) { // +420 xxx xxx xxx
			$phone = sprintf('+%s %s %s %s', $prefixParser[1], $prefixParser[2], $prefixParser[3], $prefixParser[4]);
		} elseif (preg_match('/^\+(4\d{2})(\d+)$/', $phone, $prefixSimpleParser) === 1) { // +420 xxx
			$phone = sprintf('+%s %s', $prefixSimpleParser[1], $prefixSimpleParser[2]);
		} elseif (preg_match('/^(\d{3})(\d{3})(\d{3})$/', $phone, $regularParser) === 1) { // numbers only
			$phone = sprintf('+%s %s %s %s', $region, $regularParser[1], $regularParser[2], $regularParser[3]);
		} else {
			throw new \InvalidArgumentException(sprintf('Phone number "%s" for region "%s" does not exist.', $phone, $region));
		}

		return $phone;
	}
}
