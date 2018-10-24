<?php
function wpubg_getData ($url, $apikey) {
	$params = array(
		'sslverify' => false, 
        	'headers' => array(
        		'Authorization' => $apikey,
			'Accept' => 'application/json'
        	)
	);
	$response = wp_remote_get($url, $params);
	$result = wp_remote_retrieve_body($response);
    return $result;
}

function wpubg_getRank ($points) {
	if ($points == 0) {
		$rank = "Unranked";
	} else if ($points > 0 && $points < 1399) {
		$rank = "Bronze";
	} else if ($points >=1400 && $points <=1499) {
		$rank = "Silver";
	} else if ($points >=1500 && $points <=1599) {
		$rank = "Gold";
	} else if ($points >=1600 && $points <=1699) {
		$rank = "Platinum";
	} else if ($points >=1700 && $points <=1799) {
		$rank = "Diamond";
	} else if ($points >=1800 && $points <=1899) {
		$rank = "Elite";
	} else if ($points >=1900 && $points <=1999) {
		$rank = "Master";
	} else if ($points >=2000) {
		$rank = "Grandmaster";
	}
	return $rank;
}

?>
