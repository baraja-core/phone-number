Validation and formatting of telephone numbers
==============================================

[Read Czech version](https://php.baraja.cz/telefonni-cisla)

There is no easy way to validate and format phone numbers in PHP, so I wrote a simple library for this that has no dependencies, but still handles this role.

The goal is to check the format of a phone number, or convert it to a basic canonical form (which is always valid).

Installing
----------

Simply composer:

```
$ composer require baraja-core/phone-number
```

How to use the library
----------------------

The principle of this tool is based on formatting and validating phone numbers from the user, or from sources over which you have no control.

The most common use is to correct phone number formatting:

```php
$original = '+420 777123456';
$formatted = \Baraja\PhoneNumber\PhoneNumberFormatter::fix($original);

echo $original . '<br>';
echo $formatted;
```

The function fixes the number formatting and returns the string `+420 777 123 456`.

If no prefix is specified by the user, the `+420` prefix is assumed. You can change the default preference with the second parameter:

```php
// returns: +421 777 123 456
\Baraja\PhoneNumber\PhoneNumberFormatter::fix('+420 777123456', 421);
```

The phone code is overwritten only if the user does not enter it and it fails to be detected automatically.

Input and output formatting
---------------------------

The input string can look (almost) any way. The built-in algorithm can automatically remove non-valid characters (for example, some users write a note next to a phone number that will be removed automatically). So you don't have to worry about formatting the input at all, but the output will always be consistent.

The output always looks the same (it is normalized to the canonical format).

The general format is:

```
   +420 777 123 456
     | \_________/
  Prefix |
      National number
```

If you submit a non-valid input (or an input that cannot be automatically corrected), an exception will be thrown.

Error trapping
--------------

If the number cannot be safely normalized to a base form, or does not exist, throw an `\InvalidArgumentException` exception.

If you wish to convert the exception to a boolean, use the built-in asset validator:

```php
\Baraja\PhoneNumber\PhoneNumberValidator::isValid('123'); // false
\Baraja\PhoneNumber\PhoneNumberValidator::isValid('777123456'); // true
\Baraja\PhoneNumber\PhoneNumberValidator::isValid('+420 777123456'); // true
\Baraja\PhoneNumber\PhoneNumberValidator::isValid('+420 777 123 456'); // true
\Baraja\PhoneNumber\PhoneNumberValidator::isValid('+420 77 712 34 56'); // true
```
