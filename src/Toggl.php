<?php

namespace SpeedyLom\WhenDoYouFinish;

class Toggl
{
	private static $togglUrl = 'https://toggl.com/reports/api/';
	
	private static function makeCurlRequest(string $path) : ?array
	{
		$url = static::$togglUrl . 'v2/' . $path;
		
		$apiToken = $_ENV['API_TOKEN'];
		
		$httpHeaders = array
		(
			'Authorization: Basic ' . base64_encode($apiToken . ':api_token')
		);
		
		$getArgs = array
		(
			'user_agent' => $_ENV['USER_AGENT'],
			'workspace_id' => $_ENV['WORKSPACE_ID'],
			'api_token' => $apiToken,
		);
		
		$url .= '?'.http_build_query($getArgs);
		
		// create CURL request
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $httpHeaders);
		$curlRequest = curl_exec($curl);
		
		if(curl_errno($curl))
		{
			echo curl_error($curl);
			
			return null;
		}
		
		// execute the CURL request
		return json_decode($curlRequest, true);
	}
	
	public static function getTotalMinutesForToday(): int
	{
		$togglData = static::makeCurlRequest('weekly');
		
		if(!isset($togglData['week_totals'][6]))
		{
			return 0;
		}
		
		$dayTotalInMilliseconds = $togglData['week_totals'][6];
		
		return Converter::convertMillisecondsToMinutes($dayTotalInMilliseconds);
	}
	
}