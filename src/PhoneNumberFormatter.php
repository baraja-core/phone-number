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
		if (preg_match('/^(?:0{2,}|\+)\s*([1-9]\d{1,2})\s+(.+)$/', $phone, $phoneRegionParser) === 1) {
			$region = (int) ($phoneRegionParser[1] ?? throw new \LogicException('Invalid phone prefix.'));
			$phone = sprintf('+%s%s', $region, $phoneRegionParser[2] ?? '');
		}
		if (preg_match('/^([+0-9]+)/', $phone, $trimUnexpected) === 1) { // remove user notice and unexpected characters
			$phone = $trimUnexpected[1];
		}
		if ($region >= 1 && $region <= 999) {
			foreach ([(string) $region, '+' . $region] as $try) {
				if (str_starts_with($phone, $try)) {
					$phoneWithoutPrefix = substr($phone, strlen($try));
					$phone = $phoneWithoutPrefix === '' ? $phone : $phoneWithoutPrefix;
					break;
				}
			}
		}
		if (preg_match('/^\+(4\d{2})(\d{3})(\d{3})(\d{3})$/', $phone, $prefixParser) === 1) { // +4xx xxx xxx xxx
			return sprintf('+%s %s %s %s', $prefixParser[1], $prefixParser[2], $prefixParser[3], $prefixParser[4]);
		}
		if (preg_match('/^\+(4\d{2})(\d+)$/', $phone, $prefixSimpleParser) === 1) { // +4xx xxx
			return sprintf('+%s %s', $prefixSimpleParser[1], $prefixSimpleParser[2]);
		}
		if (preg_match('/^\+(\d\d?)(\d+)$/', $phone, $prefixSimpleParser) === 1) { // (+x or +xx) xxx
			return sprintf('+%s %s', $prefixSimpleParser[1], $prefixSimpleParser[2]);
		}
		if (preg_match('/^(\d{7,})$/', $phone, $regularParser) === 1) { // numbers only
			$number = $regularParser[1];
			$numberParts = [];
			do {
				if (preg_match('/^(.*)(\d{3})$/', $number, $numberParser) === 1) {
					$number = $numberParser[1];
					$numberParts[] = $numberParser[2];
					if ($number === '') {
						break;
					}
				} else {
					$numberParts[] = $number;
					$number = null;
				}
			} while ($number !== null);

			return sprintf(
				'+%s %s',
				$region,
				preg_replace('/^(\d{1,2})\s(\d{3}.*)$/', '$1$2', implode(' ', array_reverse($numberParts))),
			);
		}

		throw new \InvalidArgumentException(sprintf('Phone number "%s" for region "%s" does not exist.', $phone, $region));
	}
}
