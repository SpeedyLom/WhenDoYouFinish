<?php

namespace SpeedyLom\WhenDoYouFinish;

class Converter
{
	
	public static function convertMillisecondsToMinutes(int $millisecondTotal) : int
	{
		return ceil(($millisecondTotal / 1000) / 60);
	}
	
	public static function convertTotalMinutesIntoPercentage(int $dayTotalInMinutes) : int
	{
		return round(($dayTotalInMinutes * 100) / $_ENV['WORKDAY_LENGTH_IN_MINUTES'], 2);
	}
	
	public static function determineRemainingWorkdayInMinutes(int $dayTotalInMinutes)
	{
		$workdayLengthInMinutes = $_ENV['WORKDAY_LENGTH_IN_MINUTES'];
		
		return max(0, ($workdayLengthInMinutes - $dayTotalInMinutes));
	}
	
	public static function determineEndOfDayTimestamp(int $dayTotalInMinutes) : int
	{
		$remainingWorkdayInMinutes = static::determineRemainingWorkdayInMinutes($dayTotalInMinutes);
		
		return strtotime('+' . $remainingWorkdayInMinutes . ' minutes');
	}
	
}