<?php
declare(strict_types=1);

namespace Common;

abstract class NumbersHelper
{
	public static function strToFloat(string $number): float
	{
		return \floatval(\str_replace(',', '.', $number));
	}
}