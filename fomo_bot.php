<?php

include_once('Bitfinex.php');

$botToken = "Your_bot_token_here";
$website = "https://api.telegram.org/bot".$botToken;
 
$update = file_get_contents('php://input');
$update = json_decode($update, TRUE);
 
 
$chatId = $update["message"]["chat"]["id"];
$message = $update["message"]["text"];
 
function url_get_contents ($Url) {
    if (!function_exists('curl_init')){ 
        die('CURL is not installed!');
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $Url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

function make_comparer() {
    // Normalize criteria up front so that the comparer finds everything tidy
    $criteria = func_get_args();
    foreach ($criteria as $index => $criterion) {
        $criteria[$index] = is_array($criterion)
            ? array_pad($criterion, 3, null)
            : array($criterion, SORT_ASC, null);
    }

    return function($first, $second) use (&$criteria) {
        foreach ($criteria as $criterion) {
            // How will we compare this round?
            list($column, $sortOrder, $projection) = $criterion;
            $sortOrder = $sortOrder === SORT_DESC ? -1 : 1;

            // If a projection was defined project the values now
            if ($projection) {
                $lhs = call_user_func($projection, $first[$column]);
                $rhs = call_user_func($projection, $second[$column]);
            }
            else {
                $lhs = $first[$column];
                $rhs = $second[$column];
            }

            // Do the actual comparison; do not return if equal
            if ($lhs < $rhs) {
                return -1 * $sortOrder;
            }
            else if ($lhs > $rhs) {
                return 1 * $sortOrder;
            }
        }

        return 0; // tiebreakers exhausted, so $first == $second
    };
}



$date = date('Y-m-d H:i:s');
$currtimestamp=$date."(UTC)";


// Futures premium stuff

switch($message) {
/*


 public commands 



*/

/*
    case "/getfuturespremium@FOMO_bot":p
                $okcindex = file_get_contents('https://www.okcoin.com/api/v1/future_index.do?symbol=btc_usd');
                $okcixarray = json_decode($okcindex, true);
                $okcixprice = $okcixarray['future_index'];

                $okcweekly = file_get_contents('https://www.okcoin.com/api/v1/future_ticker.do?symbol=btc_usd&contract_type=this_week');
                $okcwkarray = json_decode($okcweekly, true);
                $okcwkprice = $okcwkarray['ticker']['last'];
                $wkpremium = round((($okcwkprice - $okcixprice)/$okcwkprice)*100,2);
                $wkp=round($okcwkprice - $okcixprice,2);

                $okcbiweekly = file_get_contents('https://www.okcoin.com/api/v1/future_ticker.do?symbol=btc_usd&contract_type=next_week');
                $okcbiwkarray = json_decode($okcbiweekly, true);
                $okcbiwkprice = $okcbiwkarray['ticker']['last'];
                $biwkpremium = round((($okcbiwkprice - $okcixprice)/$okcbiwkprice)*100,2);
                $bip=round($okcbiwkprice - $okcixprice,2);

                $okcqtly = file_get_contents('https://www.okcoin.com/api/v1/future_ticker.do?symbol=btc_usd&contract_type=quarter');
                $okcqtarray = json_decode($okcqtly, true);
                $okcqtprice = $okcqtarray['ticker']['last'];
                $qtp=round($okcqtprice - $okcixprice,2);
                $qtpremium = round((($okcqtprice - $okcixprice)/$okcqtprice)*100,2);

                sendMessage($chatId, "<b>Bitcoin Futures Premiums (OKCoin)</b>\n