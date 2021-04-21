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
		$phone = (string) preg_replace('/[^\d+]/', '', $phone); // remove spaces
		if (preg_match('/^(?:0{2,}|\+)\s*([1-9]\d{2})\s*(.+)$/', $phone, $phoneRegionParser)) {
			$region = (int) ($phoneRegionParser[1] ?? throw new \LogicException('Invalid phone prefix.'));
			$phone = '+' . $region . ($phoneRegionParser[2] ?? '');
		}

		if (preg_match('/^([+0-9]+)/', $phone, $trimUnexpected)) { // remove user notice and unexpected characters
			$phone = (string) $trimUnexpected[1];
		}
		if (preg_match('/^\+(4\d{2})(\d{3})(\d{3})(\d{3})$/', $phone, $prefixParser)) { // +420 xxx xxx xxx
			$phone = '+' . $prefixParser[1] . ' ' . $prefixParser[2] . ' ' . $prefixParser[3] . ' ' . $prefixParser[4];
		} elseif (preg_match('/^\+(4\d{2})(\d+)$/', $phone, $prefixSimpleParser)) { // +420 xxx
			$phone = '+' . $prefixSimpleParser[1] . ' ' . $prefixSimpleParser[2];
		} elseif (preg_match('/^(\d{3})(\d{3})(\d{3})$/', $phone, $regularParser)) { // numbers only
			$phone = '+' . $region . ' ' . $regularParser[1] . ' ' . $regularParser[2] . ' ' . $regularParser[3];
		} else {
			throw new \InvalidArgumentException(
				'Phone number "' . $phone . '" for region "' . $region . '" does not exist.',
			);
		}

		return $phone;
	}
}
