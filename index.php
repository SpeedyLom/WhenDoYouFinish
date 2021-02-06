<?php

use SpeedyLom\WhenDoYouFinish\Converter;
use SpeedyLom\WhenDoYouFinish\Toggl;

require_once __DIR__ . '/vendor/autoload.php';

// load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$totalMinutesForToday = Toggl::getTotalMinutesForToday();
$workdayLengthInMinutes = $_ENV['WORKDAY_LENGTH_IN_MINUTES'];
$percentageWorked = Converter::convertTotalMinutesIntoPercentage($totalMinutesForToday);
$endOfDayTimestamp = Converter::determineEndOfDayTimestamp($totalMinutesForToday);

if($totalMinutesForToday > 60)
{
	$timeWorkedFormat = 'g \h\o\u\r\s \a\n\d i \m\i\n\u\t\e\s';
}
else
{
	$timeWorkedFormat = 'i \m\i\n\u\t\e\s';
}

if($totalMinutesForToday > 0)
{
	$content = '<div class="card mb-4 shadow-sm"><div class="card-header"><h4 class="my-0 fw-normal">' . date('l jS') . '</h4></div><div class="card-body"><h1 class="card-title pricing-card-title"><small class="text-muted">Estimated finish </small>' . date('g.ia', $endOfDayTimestamp) . '</h1><p class="card-text">Time worked: ' . date($timeWorkedFormat, ($totalMinutesForToday * 60)) . '</p><div class="progress" style="height: 30px;"><div class="progress-bar" role="progressbar" style="width: ' . $percentageWorked . '%;" aria-valuenow="' . $percentageWorked . '" aria-valuemin="0" aria-valuemax="100">' . $percentageWorked . '%</div></div></div></div>';
}
else
{
	$content = '<div class="alert alert-primary" role="alert">Nothing recorded! Are they working today?</div>';
}

?>

<!doctype html>
<html lang="en">
<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Bootstrap CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet"
		  integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">

	<title>When Do You Finish?</title>
</head>
<body>
<main class="container">
	<div class="px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
		<h1 class="display-4">When do you finish?</h1>
		<p class="lead">Built to answer your partner's daily question of "When do you finish?"</p>
	</div>

	<div class="row row-cols-1 row-cols-md-3 mb-3 text-center">
		<div class="col offset-md-4">
			<?=$content?>
		</div>
	</div>
</main>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"
		integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW"
		crossorigin="anonymous"></script>

</body>
</html>

