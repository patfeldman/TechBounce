<?php

define('BASE_GOOGLE_URL_', 'https://www.google.com/finance/info?q=');
define('BASE_GOOGLE_HIGH_', 'https://www.google.com/finance/getprices?i=300&p=10m&f=h,l&q=');
define('BASE_GOOGLE_HIGH_FIVEMIN_', 'https://www.google.com/finance/getprices?i=300&p=15m&f=h,l&q=');
define('BASE_GOOGLE_STOCH_', 'https://www.google.com/finance/getprices?i=300&p=2d&f=d,c,h,l&q=');

class finance_google {
	static function retrieveCurrentPrice($stock) {
		$url = BASE_GOOGLE_URL_;
		if (is_array($stock)) {
			$stockquery = implode(',', $stock);
			for ($i = count($stock) - 1; $i != -1; $i--) {
				$stocklist[] = 0;
			}
			$stockarray = array_combine($stock, $stocklist);
			$url = $url . $stockquery;
		}
		echo "\n<br/>" . $url . "\n<br/>";
		if (!$file = fopen($url, 'r'))
			die('ERROR RETRIEVING STOCK PRICE URL ' . $url);

		$i = 0;
		$info = array();
		$currentArrayElement = array();
		while ($line = fgets($file)) {
			if (strpos(trim($line), "{") !== false) {
				$currentArrayElement = array();
			} else if (strpos(trim($line), "}") !== false) {
				$info[] = $currentArrayElement;
			} else {
				$tempStr = str_replace("\"", '', trim($line, "\ \,"));
				$strSplit = explode(":", $tempStr);
				if (!empty($strSplit[1])) {
					$currentArrayElement[trim($strSplit[0])] = trim($strSplit[1]);
				}
			}
		}
		foreach ($info as $stock) {
			$retArr[$stock['t']] = $stock['l'] . ',' . $stock['cp'];
		}
		return $retArr;
	}

	public static function TEST__retrieveFastKAndParts($stock) {
		$retArr = array();
		if (is_array($stock)) {
			foreach ($stock as $ticker) {
				echo BASE_GOOGLE_STOCH_ . strtoupper($ticker) . "\n<br/>";
				if (!$file = fopen(BASE_GOOGLE_STOCH_ . strtoupper($ticker), 'r'))
					die('ERROR RETRIEVING STOCK PRICE URL ' . $url);

				$count = 0;
				$info = array();
				$now = time();
				$baseUnixTime = time();
				$interval = 300;
				$dateArr = array();
				$highArr = array();
				$lowArr = array();
				$closeArr = array();

				$currentHigh = 0;
				$currentLow = 100000;

				while ($line = fgets($file)) {
					if (strstr($line, "INTERVAL"))
						$interval = intval(substr($line, 9, strlen($line)));
					if ($count++ < 7)
						continue;
					$prices = explode(",", $line);
					if (strstr($prices[0], "a")) {
						$baseUnixTime = substr($prices[0], 1, strlen($prices[0]));
						$prices[0] = $baseUnixTime;
					} else {
						$prices[0] = $baseUnixTime + intval($prices[0] * $interval);
					}

					if (count($dateArr) >= 18)
						array_shift($dateArr);
					array_push($dateArr, $prices[0]);
					if (count($closeArr) >= 18)
						array_shift($closeArr);
					array_push($closeArr, $prices[1]);
					if (count($highArr) >= 18)
						array_shift($highArr);
					array_push($highArr, $prices[2]);
					if (count($lowArr) >= 18)
						array_shift($lowArr);
					array_push($lowArr, $prices[3]);

				}

				$tz = new DateTimeZone('America/New_York');
				for ($i = 0; $i < 18; $i++) {
					$date = new DateTime(date("h:i:s", $dateArr[$i]));
					$date -> setTimeZone($tz);
					echo " RECORD: " . $date -> format("g:i:s A T") . " high:" . $highArr[$i] . " low:" . $lowArr[$i] . " close:" . $closeArr[$i] . "\n<br/>";
				}

				for ($i = 0; $i <= 4; $i++) {
					$tempHigh = array_slice($highArr, $i, 14);
					$tempLow = array_slice($lowArr, $i, 14);
					$currentHigh = max($tempHigh);
					$currentLow = min($tempLow);
					$currentClose = floatval($closeArr[$i + 13]);
					echo "<br/>" . $i . "::HIGH=" . number_format($currentHigh, 2);
					echo "::LOW=" . number_format($currentLow, 2);
					echo "::CLOSE=" . number_format($currentClose, 2);
					$fastK[] = 100 * (($currentClose - $currentLow) / ($currentHigh - $currentLow));
					$r1[] = $currentClose - $currentLow;
					$r2[] = $currentHigh - $currentLow;
					echo "::FASTK=" . number_format(end($fastK), 2);

					if ($i >= 2) {
						$tempR1 = array_sum(array_slice($r1, ($i - 2), 3));
						$tempR2 = array_sum(array_slice($r2, ($i - 2), 3));
						$slowK[] = ($tempR1 / $tempR2) * 100;
						echo "::SLOWK=" . number_format(end($slowK), 2);
						if ($i == 4) {
							$slowD = array_sum($slowK) / count($slowK);
							echo "::SLOWD=" . number_format($slowD, 2);
						}
					}
				}
				$retArr[$ticker]['fastK'] = 100 * (($currentClose - $currentLow) / ($currentHigh - $currentLow));
				$retArr[$ticker]['r1'] = $last - $currentLow;
				$retArr[$ticker]['r2'] = $currentHigh - $currentLow;
			}
		}
		return $retArr;
	}

	public static function retrieveSlowDAndParts($ticker) {
		$retArr = array();
		echo BASE_GOOGLE_STOCH_ . strtoupper($ticker) . "\n<br/>";
		if (!$file = fopen(BASE_GOOGLE_STOCH_ . strtoupper($ticker), 'r'))
			die('ERROR RETRIEVING STOCK PRICE URL ' . $url);

		$count = 0;
		$info = array();
		$now = time();
		$baseUnixTime = time();
		$interval = 300;
		$dateArr = array();
		$highArr = array();
		$lowArr = array();
		$closeArr = array();

		$currentHigh = 0;
		$currentLow = 100000;

		while ($line = fgets($file)) {
			if (strstr($line, "INTERVAL"))
				$interval = intval(substr($line, 9, strlen($line)));
			if ($count++ < 7)
				continue;
			$prices = explode(",", $line);
			if (strstr($prices[0], "a")) {
				$baseUnixTime = substr($prices[0], 1, strlen($prices[0]));
				$prices[0] = $baseUnixTime;
			} else {
				$prices[0] = $baseUnixTime + intval($prices[0] * $interval);
			}

			if (count($dateArr) >= 18)
				array_shift($dateArr);
			array_push($dateArr, $prices[0]);
			if (count($closeArr) >= 18)
				array_shift($closeArr);
			array_push($closeArr, $prices[1]);
			if (count($highArr) >= 18)
				array_shift($highArr);
			array_push($highArr, $prices[2]);
			if (count($lowArr) >= 18)
				array_shift($lowArr);
			array_push($lowArr, $prices[3]);

		}

		$tz = new DateTimeZone('America/New_York');
		for ($i = 0; $i < 18; $i++) {
			$date = new DateTime(date("h:i:s", $dateArr[$i]));
			$date -> setTimeZone($tz);
		}

		for ($i = 0; $i <= 4; $i++) {
			$tempHigh = array_slice($highArr, $i, 14);
			$tempLow = array_slice($lowArr, $i, 14);
			$currentHigh = max($tempHigh);
			$currentLow = min($tempLow);
			$currentClose = floatval($closeArr[$i + 13]);
			$fastK[] = 100 * (($currentClose - $currentLow) / ($currentHigh - $currentLow));
			$r1[] = $currentClose - $currentLow;
			$r2[] = $currentHigh - $currentLow;
			if ($i >= 2) {
				$tempR1 = array_sum(array_slice($r1, ($i - 2), 3));
				$tempR2 = array_sum(array_slice($r2, ($i - 2), 3));
				$slowK[] = ($tempR1 / $tempR2) * 100;
				if ($i == 4) {
					$slowD = array_sum($slowK) / count($slowK);
				}
			}
		}
		$retArr['r1'] = end($r1);
		$retArr['r2'] = end($r2);
		$retArr['fastK'] = end($fastK);
		$retArr['slowK'] = end($slowK);
		$retArr['slowD'] = $slowD;
		$retArr['last'] = end($closeArr);
		$retArr['high'] = end($highArr);
		$retArr['low'] = end($lowArr);
		$retArr['date'] = end($dateArr);
		return $retArr;
	}

	public static function retrieveHighsLowsPrice($stock) {
		$retArr = array();
		if (is_array($stock)) {
			foreach ($stock as $ticker) {
				echo BASE_GOOGLE_HIGH_FIVEMIN_ . strtoupper($ticker) . "\n</br>";
				if (!$file = fopen(BASE_GOOGLE_HIGH_FIVEMIN_ . strtoupper($ticker), 'r'))
					die('ERROR RETRIEVING STOCK PRICE URL ' . $url);

				$count = 0;
				$info = array();
				$currentArrayElement = array();
				$currentHigh = 0;
				$currentLow = 100000;
				while ($line = fgets($file)) {
					if ($count++ < 7)
						continue;
					$prices = explode(",", $line);
					//				if ($prices[0] > $currentHigh)
					//					$currentHigh = $prices[0];
					//				if ($prices[1] < $currentLow)
					//					$currentLow = $prices[1];
					// STORE THE LAST AND MOST RECENT PRICE WINDOW
					$currentHigh = $prices[0];
					$currentLow = $prices[1];
				}
				$retArr[$ticker]['high'] = $currentHigh;
				$retArr[$ticker]['low'] = $currentLow;
			}
		}
		return $retArr;
	}

}
