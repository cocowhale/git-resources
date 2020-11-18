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

                sendMessage($chatId, "<b>Bitcoin Futures Premiums (OKCoin)</b>\n<code>Index : </code>$".number_format($okcixprice,"2")."\n<code>Weekly: </code>$".number_format($okcwkprice,"2")." ($".number_format($wkp,"2")." ; ".number_format($wkpremium,"2")."%)\n<code>Biwkly: </code>$".number_format($okcbiwkprice,"2")." ($".number_format($bip, "2")." ; ".number_format($biwkpremium, "2")."%)\n<code>Qtly  : </code>$".number_format($okcqtprice, "2")." ($".number_format($qtp,"2")." ; ".number_format($qtpremium,"2")."%)");
                break;

   case "/getwesternticker@FOMO_bot":
                $finex = file_get_contents('https://api.bitfinex.com/v1/pubticker/BTCUSD');
                $stamp = file_get_contents('https://www.bitstamp.net/api/ticker');
                $gaydax = url_get_contents('https://api.gdax.com/products/BTC-USD/ticker');
                $btce = file_get_contents('https://btc-e.com/api/3/ticker/btc_usd');
                $itbit = file_get_contents('https://api.itbit.com/v1/markets/XBTUSD/ticker');
                $okcoin = file_get_contents('https://www.okcoin.com/api/v1/ticker.do?symbol=btc_usd');
                $gemini = file_get_contents('https://api.gemini.com/v1/pubticker/btcusd');



                $finexarray = json_decode($finex,true);
                $stamparray = json_decode($stamp,true);
                $gaydaxarray = json_decode($gaydax,true);
                $btcearray = json_decode($btce,true);
                $itbitarray = json_decode($itbit,true);
                $okcoinarray = json_decode($okcoin, true);
                $geminiarray = json_decode($gemini, true);

                $finexprice = $finexarray['last_price'];
                $stampprice = $stamparray['last'];
                $gaydaxprice = $gaydaxarray['price'];
                $btceprice = $btcearray['btc_usd']['last'];
                $itbitprice = $itbitarray['lastPrice'];
                $okcoinprice = $okcoinarray['ticker']['last'];
                $geminiprice = $geminiarray['last'];

                $finexvol = $finexarray['volume'];
                $stampvol = $stamparray['volume'];
                $gaydaxvol = $gaydaxarray['volume'];
                $btcevol = $btcearray['btc_usd']['vol_cur'];
                $itbitvol = $itbitarray['volume24h'];
                $okcoinvol = $okcoinarray['ticker']['vol'];
                $geminivol = $geminiarray['volume']['BTC'];

sendMessage($chatId, "<b>BTC/USD Ticker (24H BTC Vol)</b>\n<code>Bitfinrek: </code>$".number_format($finexprice,"2")." (".number_format($finexvol,"0").")\n<code>Bearstamp: </code>$".number_format($stampprice,"2")." (".number_format($stampvol,"0").")\n<code>OKCasino : </code>$".number_format($okcoinprice,"2")." (".number_format($okcoinvol,"0").")\n<code>BTC-Putin: </code>$".number_format($btceprice,"2")." (".number_format($btcevol,"0").")\n<code>Gaydax   : </code>$".number_format($gaydaxprice,"2")." (".number_format($gaydaxvol,"0").")\n<code>ShitBit  : </code>$".number_format($itbitprice,"2")." (".number_format($itbitvol,"0").")\n<code>GeminiLOL: </code>$".number_format($geminiprice,"2")." (".number_format($geminivol,"0").")");

			break;
case "/getchinaticker@FOMO_bot":
                $huobifetch = file_get_contents('http://api.huobi.com/staticmarket/ticker_btc_json.js');
                $huobiarray = json_decode($huobifetch, true);
                $huobiprice = $huobiarray['ticker']['last'];


                $chinafetch = file_get_contents('https://www.okcoin.cn/api/v1/ticker.do?symbol=btc_cny');
                $chinaarray = json_decode($chinafetch, true);
                $chinaprice = $chinaarray['ticker']['last'];

                $btcchinafetch = file_get_contents('https://data.btcchina.com/data/ticker?market=btccny');
                $btcchinaarray = json_decode($btcchinafetch, true);
                $btcchinaprice = $btcchinaarray['ticker']['last'];

                sendMessage($chatId, "<b>CNY Bitcoin Exchange Ticker</b>\n<code>Huobi : </code>¥".number_format($huobiprice,"0")."\n<code>OKCoin: </code>¥".number_format($chinaprice,"0")."\n<code>BTCC  : </code>¥".number_format($btcchinaprice,"0"));
			break;

case "/getchinapremium@FOMO_bot":
                $huobifetch = file_get_contents('http://api.huobi.com/staticmarket/ticker_btc_json.js');
                $huobiarray = json_decode($huobifetch, true);
                $huobiprice = $huobiarray['ticker']['last'];
                $huobipricer = round($huobiarray['ticker']['last'],0);

                $chinafetch = file_get_contents('https://www.okcoin.cn/api/v1/ticker.do?symbol=btc_cny');
                $chinaarray = json_decode($chinafetch, true);
                $chinaprice = $chinaarray['ticker']['last'];
                $chinapricer = round($chinaprice,0);

                $usdcny = file_get_contents('http://free.currencyconverterapi.com/api/v3/convert?q=USD_CNY');
                $usdcnydec = json_decode($usdcny, true);
                $cnyconv = $usdcnydec['results']['USD_CNY']['val'];

                #$finex = file_get_contents('https://api.bitfinex.com/v1/pubticker/BTCUSD');
                $finex = file_get_contents('https://www.bitstamp.net/api/ticker');
                $finexarray = json_decode($finex,true);
                #$finexprice = $finexarray['last_price'];
                $finexprice = $finexarray['last'];
                $chinausd=round($huobiprice/$cnyconv,2);
                $bfxcny=round($finexprice*$cnyconv,0);
                $chinadiff =round($chinausd - $finexprice,2);
                $chinaprem=round(($chinadiff/$finexprice)*100,2);
                //sendMessage($chatId, "<b>China vs. Western Exchange Balance</b>\nPremium in Huobi China \nCurrent Price: (¥".$huobipricer."->$".$chinausd.")\nRelative to Finex ($".$finexprice."): $".$chinadiff." (".$chinaprem."%)");
                sendMessage($chatId, "<b>CNY vs. USD (".$cnyconv.") Spot Prices</b>\n<code>Huobi        :</code> ¥".$huobipricer." ($".$chinausd.")\n<code>Bitstamp     :</code> $".$finexprice." (¥".$bfxcny.")\n<code>China Premium:</code> $".number_format($chinadiff,"2")." (".number_format($chinaprem,"2")."%)");
                break;

case "/getsettlementtime@FOMO_bot":
                $currenttime=gmdate(time());
                $daytoday = date( "w", $currenttime);
                $hw = date( "H", $currenttime);

                if ($daytoday == 5 && $hw < 8):
                    $date = strtotime("today, 8:00 AM UTC");
                else:
                    $date = strtotime("next Friday, 8:00 AM UTC");
                endif;

                $rem = $date - time();
                $day = floor($rem / 86400);
                $hr  = floor(($rem % 86400) / 3600);
                $min = floor(($rem % 3600) / 60);
                $sec = ($rem % 60);

                if ($day != 0 && $hr != 0 && $min != 0 && $sec != 0):
                    $timeleft = "$day Days $hr Hours $min Minutes $sec Seconds";
                elseif ($hr != 0 && $min != 0 && $sec != 0): 
                    $timeleft = "$hr Hours $min Minutes $sec Seconds";
                elseif ($min != 0 && $sec != 0):
                    $timeleft = "$min Minutes $sec Seconds";
                elseif ($sec != 0):
                    $timeleft = "$sec Seconds ";
                endif;
                sendMessage($chatId, "<b>Bitcoin Futures Settlement Countdown</b>\nOKCoin (Friday 8 UTC): \n".$timeleft);
                break;


case "/getfinexlongshort@FOMO_bot":
                           #bitcoin

                #BTCUSD long
                $finexlong = file_get_contents('https://api.bitfinex.com/v1/stats_history/pos_open_long_BTCUSD');
                $finexlongarray = json_decode($finexlong,true);
                $finexlongprice = intval($finexlongarray[0]['v']);

                #BTCUSD short
                $finexshort = file_get_contents('https://api.bitfinex.com/v1/stats_history/pos_open_short_BTCUSD');
                $finexshortarray = json_decode($finexshort,true);
                $finexshortprice = intval($finexshortarray[0]['v']);

                $btcpctlong=$finexlongprice/($finexlongprice+$finexshortprice);
                $btcpctshort=$finexshortprice/($finexlongprice+$finexshortprice);
                #zcash

                #ZECUSD long
                $finexZECusdlong = file_get_contents('https://api.bitfinex.com/v1/stats_history/pos_open_long_ZECUSD');
                $finexZECusdlongarray = json_decode($finexZECusdlong,true);
                $finexZECusdlongprice = intval($finexZECusdlongarray[0]['v']);

                #ZECBTC long
                $finexZECbtclong = file_get_contents('https://api.bitfinex.com/v1/stats_history/pos_open_long_ZECBTC');
                $finexZECbtclongarray = json_decode($finexZECbtclong,true);
                $finexZECbtclongprice = intval($finexZECbtclongarray[0]['v']);

                #total ZEC longs
                $totalZEClong=$finexZECbtclongprice+$finexZECusdlongprice;

                #ZECBTC short
                $finexZECbtcshort = file_get_contents('https://api.bitfinex.com/v1/stats_history/pos_open_short_ZECBTC');
                $finexZECbtcshortarray = json_decode($finexZECbtcshort,true);
                $finexZECbtcshortprice = intval($finexZECbtcshortarray[0]['v']);

                #ZECUSD short
                $finexZECusdshort = file_get_contents('https://api.bitfinex.com/v1/stats_history/pos_open_short_ZECUSD');
                $finexZECusdshortarray = json_decode($finexZECusdshort,true);
                $finexZECusdshortprice = intval($finexZECusdshortarray[0]['v']);

                #total ZEC shorts
                $totalZECshort=$finexZECbtcshortprice+$finexZECusdshortprice;
                $totalZEC=$totalZECshort+$totalZEClong;
                $ZECpctshort=$totalZECshort/$totalZEC;
                $ZECpctlong=$totalZEClong/$totalZEC;
                #litecoin

                #LTCUSD long
                $finexLTCusdlong = file_get_contents('https://api.bitfinex.com/v1/stats_history/pos_open_long_LTCUSD');
                $finexLTCusdlongarray = json_decode($finexLTCusdlong,true);
                $finexLTCusdlongprice = intval($finexLTCusdlongarray[0]['v']);

                #LTCBTC long
                $finexLTCbtclong = file_get_contents('https://api.bitfinex.com/v1/stats_history/pos_open_long_LTCBTC');
                $finexLTCbtclongarray = json_decode($finexLTCbtclong,true);
                $finexLTCbtclongprice = intval($finexLTCbtclongarray[0]['v']);

                #total LTC longs
                $totalLTClong=$finexLTCbtclongprice+$finexLTCusdlongprice;

                #LTCBTC short
                $finexLTCbtcshort = file_get_contents('https://api.bitfinex.com/v1/stats_history/pos_open_short_LTCBTC');
                $finexLTCbtcshortarray = json_decode($finexLTCbtcshort,true);
                $finexLTCbtcshortprice = intval($finexLTCbtcshortarray[0]['v']);

                #LTCUSD short
                $finexLTCusdshort = file_get_contents('https://api.bitfinex.com/v1/stats_history/pos_open_short_LTCUSD');
                $finexLTCusdshortarray = json_decode($finexLTCusdshort,true);
                $finexLTCusdshortprice = intval($finexLTCusdshortarray[0]['v']);

                #total LTC shorts
                $totalLTCshort=$finexLTCbtcshortprice+$finexLTCusdshortprice;
                $totalLTC=$totalLTCshort+$totalLTClong;
                $LTCpctshort=$totalLTCshort/$totalLTC;
                $LTCpctlong=$totalLTClong/$totalLTC;
                #bfxcoin

                #BFXUSD long
                $finexBFXusdlong = file_get_contents('https://api.bitfinex.com/v1/stats_history/pos_open_long_BFXUSD');
                $finexBFXusdlongarray = json_decode($finexBFXusdlong,true);
                $finexBFXusdlongprice = intval($finexBFXusdlongarray[0]['v']);

                #BFXBTC long
                $finexBFXbtclong = file_get_contents('https://api.bitfinex.com/v1/stats_history/pos_open_long_BFXBTC');
                $finexBFXbtclongarray = json_decode($finexBFXbtclong,true);
                $finexBFXbtclongprice = intval($finexBFXbtclongarray[0]['v']);

                #total BFX longs
                $totalBFXlong=$finexBFXbtclongprice+$finexBFXusdlongprice;

                #BFXBTC short
                $finexBFXbtcshort = file_get_contents('https://api.bitfinex.com/v1/stats_history/pos_open_short_BFXBTC');
                $finexBFXbtcshortarray = json_decode($finexBFXbtcshort,true);
                $finexBFXbtcshortprice = intval($finexBFXbtcshortarray[0]['v']);

                #BFXUSD short
                $finexBFXusdshort = file_get_contents('https://api.bitfinex.com/v1/stats_history/pos_open_short_BFXUSD');
                $finexBFXusdshortarray = json_decode($finexBFXusdshort,true);
                $finexBFXusdshortprice = intval($finexBFXusdshortarray[0]['v']);

                #total BFX shorts
                $totalBFXshort=$finexBFXbtcshortprice+$finexBFXusdshortprice;
                $totalBFX=$totalBFXshort+$totalBFXlong;
                $BFXpctshort=$totalBFXshort/$totalBFX;
                $BFXpctlong=$totalBFXlong/$totalBFX;
                #ethereum

                #ETHUSD long
                $finexethusdlong = file_get_contents('https://api.bitfinex.com/v1/stats_history/pos_open_long_ETHUSD');
                $finexethusdlongarray = json_decode($finexethusdlong,true);
                $finexethusdlongprice = intval($finexethusdlongarray[0]['v']);

                #ETHBTC long
                $finexethbtclong = file_get_contents('https://api.bitfinex.com/v1/stats_history/pos_open_long_ETHBTC');
                $finexethbtclongarray = json_decode($finexethbtclong,true);
                $finexethbtclongprice = intval($finexethbtclongarray[0]['v']);

                #total eth longs
                $totalethlong=$finexethbtclongprice+$finexethusdlongprice;

                #ETHBTC short
                $finexethbtcshort = file_get_contents('https://api.bitfinex.com/v1/stats_history/pos_open_short_ETHBTC');
                $finexethbtcshortarray = json_decode($finexethbtcshort,true);
                $finexethbtcshortprice = intval($finexethbtcshortarray[0]['v']);

                #ETHUSD short
                $finexethusdshort = file_get_contents('https://api.bitfinex.com/v1/stats_history/pos_open_short_ETHUSD');
                $finexethusdshortarray = json_decode($finexethusdshort,true);
                $finexethusdshortprice = intval($finexethusdshortarray[0]['v']);

                #total eth shorts
                $totalethshort=$finexethbtcshortprice+$finexethusdshortprice;
                $totaleth=$totalethshort+$totalethlong;
                $ethpctshort=$totalethshort/$totaleth;
                $ethpctlong=$totalethlong/$totaleth;

                #ETCUSD long
                $finexetcusdlong = file_get_contents('https://api.bitfinex.com/v1/stats_history/pos_open_long_ETCUSD');
                $finexetcusdlongarray = json_decode($finexetcusdlong,true);
                $finexetcusdlongprice = intval($finexetcusdlongarray[0]['v']);

                #ETCBTC long
                $finexetcbtclong = file_get_contents('https://api.bitfinex.com/v1/stats_history/pos_open_long_ETCBTC');
                $finexetcbtclongarray = json_decode($finexetcbtclong,true);
                $finexetcbtclongprice = intval($finexetcbtclongarray[0]['v']);

                #total etc longs
                $totaletclong=$finexetcbtclongprice+$finexetcusdlongprice;

                #ETCUSD short
                $finexetcusdshort = file_get_contents('https://api.bitfinex.com/v1/stats_history/pos_open_short_ETCUSD');
                $finexetcusdshortarray = json_decode($finexetcusdshort,true);
                $finexetcusdshortprice = intval($finexetcusdshortarray[0]['v']);

                #ETCBTC short
                $finexetcbtcshort = file_get_contents('https://api.bitfinex.com/v1/stats_history/pos_open_short_ETCBTC');
                $finexetcbtcshortarray = json_decode($finexetcbtcshort,true);
                $finexetcbtcshortprice = intval($finexetcbtcshortarray[0]['v']);

                #total etc shorts
                $totaletcshort=$finexetcbtcshortprice+$finexetcusdshortprice;
                $totaletc=$totaletcshort+$totaletclong;
                $etcpctshort=$totaletcshort/$totaletc;
                $etcpctlong=$totaletclong/$totaletc;

                sendMessage($chatId, "<b>Bfx Positions     LONG SHORT</b>\n<code>Bitcoin (BTC):</code> ".number_format($btcpctlong*100)."%   ".number_format($btcpctshort*100)."%\n<code>Zcrash (ZEC) :</code> ".number_format($ZECpctlong*100)."%   ".number_format($ZECpctshort*100)."%\n<code>BFXtoken(BFX):</code> ".number_format($BFXpctlong*100)."%   ".number_format($BFXpctshort*100)."%\n<code>Classy (ETC) :</code> ".number_format($etcpctlong*100)."%   ".number_format($etcpctshort*100)."%\n<code>Ternium (ETH):</code> ".number_format($ethpctlong*100)."%   ".number_format($ethpctshort*100)."%\n<code>Litecoin(LTC):</code> ".number_format($LTCpctlong*100)."%   ".number_format($LTCpctshort*100)."%");
                break;

case "/getmarginfunding@FOMO_bot":
                 $grabusdmarg = file_get_contents('https://api.bitfinex.com/v1/lends/usd');
                $usdmargarray = json_decode($grabusdmarg, true);
                $usdmarglent = intval($usdmargarray[0]['amount_lent']);
                $usdmargused = intval($usdmargarray[0]['amount_used']);
                $margts = gmdate("Y-m-d\TH:i:s\Z",$usdmargarray[0]['timestamp']);
                $usduseddiff=$usdmarglent - $usdmargused;
                $usdusedperc=round(($usdmargused/$usdmarglent)*100,1);


                $finexlong=file_get_contents('https://api2.bitfinex.com:3000/api/v2/stats1/credits.size.sym:1m:fUSD:tBTCUSD/hist');
                $finexlongarray = json_decode($finexlong,true);
                $finexusdmargbtcusd = intval($finexlongarray[0][1]);

                $finexlong=file_get_contents('https://api2.bitfinex.com:3000/api/v2/stats1/credits.size.sym:1m:fUSD:tETHUSD/hist');
                $finexlongarray = json_decode($finexlong,true);
                $finexusdmargethusd = intval($finexlongarray[0][1]);

                $finexlong=file_get_contents('https://api2.bitfinex.com:3000/api/v2/stats1/credits.size.sym:1m:fUSD:tETCUSD/hist');
                $finexlongarray = json_decode($finexlong,true);
                $finexusdmargetcusd = intval($finexlongarray[0][1]);

                $finexlong=file_get_contents('https://api2.bitfinex.com:3000/api/v2/stats1/credits.size.sym:1m:fUSD:tLTCUSD/hist');
                $finexlongarray = json_decode($finexlong,true);
                $finexusdmargltcusd = intval($finexlongarray[0][1]);

                $finexlong=file_get_contents('https://api2.bitfinex.com:3000/api/v2/stats1/credits.size.sym:1m:fUSD:tBFXUSD/hist');
                $finexlongarray = json_decode($finexlong,true);
                $finexusdmargbfxusd = intval($finexlongarray[0][1]);

                $usdusedbtcusdperc=($finexusdmargbtcusd/$usdmargused)*100;
                $usdusedethusdperc=($finexusdmargethusd/$usdmargused)*100;
                $usdusedetcusdperc=($finexusdmargetcu