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
                $usdusedetcusdperc=($finexusdmargetcusd/$usdmargused)*100;
                $usdusedltcusdperc=($finexusdmargltcusd/$usdmargused)*100;
                $usdusedbfxusdperc=($finexusdmargbfxusd/$usdmargused)*100;

                $grabbtcmarg = file_get_contents('https://api.bitfinex.com/v1/lends/btc');
                $btcmargarray = json_decode($grabbtcmarg, true);
                $btcmarglent = intval($btcmargarray[0]['amount_lent']);
                $btcmargused = intval($btcmargarray[0]['amount_used']);
                $btcuseddiff=$btcmarglent - $btcmargused;
                $btcusedperc=round(($btcmargused/$btcmarglent)*100,1);


                $finexlong=file_get_contents('https://api2.bitfinex.com:3000/api/v2/stats1/credits.size.sym:1m:fBTC:tBTCUSD/hist');
                $finexlongarray = json_decode($finexlong,true);
                $finexbtcmargbtcusd = intval($finexlongarray[0][1]);

                $finexlong=file_get_contents('https://api2.bitfinex.com:3000/api/v2/stats1/credits.size.sym:1m:fBTC:tETHBTC/hist');
                $finexlongarray = json_decode($finexlong,true);
                $finexbtcmargethbtc = intval($finexlongarray[0][1]);

                $finexlong=file_get_contents('https://api2.bitfinex.com:3000/api/v2/stats1/credits.size.sym:1m:fBTC:tETCBTC/hist');
                $finexlongarray = json_decode($finexlong,true);
                $finexbtcmargetcbtc = intval($finexlongarray[0][1]);

                $finexlong=file_get_contents('https://api2.bitfinex.com:3000/api/v2/stats1/credits.size.sym:1m:fBTC:tLTCBTC/hist');
                $finexlongarray = json_decode($finexlong,true);
                $finexbtcmargltcbtc = intval($finexlongarray[0][1]);

                $finexlong=file_get_contents('https://api2.bitfinex.com:3000/api/v2/stats1/credits.size.sym:1m:fBTC:tBFXBTC/hist');
                $finexlongarray = json_decode($finexlong,true);
                $finexbtcmargbfxbtc = intval($finexlongarray[0][1]);

                $btcusedbtcusdperc=($finexbtcmargbtcusd/$btcmargused)*100;
                $btcusedethbtcperc=($finexbtcmargethbtc/$btcmargused)*100;
                $btcusedetcbtcperc=($finexbtcmargetcbtc/$btcmargused)*100;
                $btcusedltcbtcperc=($finexbtcmargltcbtc/$btcmargused)*100;
                $btcusedbfxbtcperc=($finexbtcmargbfxbtc/$btcmargused)*100;

                $finex = file_get_contents('https://api.bitfinex.com/v1/pubticker/BTCUSD');
                $finexarray = json_decode($finex,true);
                $finexprice = $finexarray['last_price'];
                $btcmarglentusd=$btcmarglent*$finexprice;
                $ratiolend=round($btcmarglentusd/$usdmarglent,2);
            
                sendMessage($chatId, "<b>Bitfinex Margin Funding Statistics</b>\n<code>USD lent: </code>$".number_format($usdmarglent)."\n<code>USD used: </code>$".number_format($usdmargused)." (<b>".$usdusedperc."%</b>)\nBTC: ".number_format($usdusedbtcusdperc)."% ETH: ".number_format($usdusedethusdperc)."% ETC: ".number_format($usdusedetcusdperc)."% LTC: ".number_format($usdusedltcusdperc)."% BFX: ".number_format($usdusedbfxusdperc)."% \n<code>BTC lent: </code>Ƀ".number_format($btcmarglent)."\n<code>BTC used: </code>Ƀ".number_format($btcmargused)." (<b>".$btcusedperc."%</b>)\nBTC: ".number_format($btcusedbtcusdperc)."% ETH: ".number_format($btcusedethbtcperc)."% ETC: ".number_format($btcusedetcbtcperc)."% LTC: ".number_format($btcusedltcbtcperc)."% BFX: ".number_format($btcusedbfxbtcperc)."% \nRatio of BTC to USD Lent: <b>".$ratiolend."</b>");
                break;

case "/topminers@FOMO_bot":
                $grabtopminers = file_get_contents('https://api.blockchain.info/pools?timespan=1days');
                $topminers = json_decode($grabtopminers, true);
                arsort($topminers);
                $minercount=count($topminers);

                #sum the blocks mined in past 24 hr
                $blocksinday=0;
                $minercount2=$minercount-1;
                foreach(range(0,$minercount2) as $x) { 
                $blocksinday=$blocksinday+$topminers[array_keys($topminers)[$x]]; 
                }
	            #build tg string
                $minerstring="<b>Miners of Bitcoin blocks past 24 Hours</b>\n<code>Name        Blocks    Share</code>\n";
                foreach(range(0,$minercount2) as $x) {
                $minershare=($topminers[array_keys($topminers)[$x]]/$blocksinday)*100;
                $minername=array_keys($topminers)[$x];
                if ($minershare<10):
                    $minername=str_pad($minername, 16);
                else:
                    $minername=str_pad($minername, 15);   
                endif;
                $numblocks=$topminers[array_keys($topminers)[$x]];
                if (intval($numblocks)<10):
                    $numblocks=str_pad($numblocks,12);
                else:
                    $numblocks=str_pad($numblocks,11);
                endif;
                if ($minershare<5):
                $minerstring=$minerstring; 
                else:
                $minerstring=$minerstring."<code>".$minername."</code>".$numblocks."    ".number_format($minershare,"0")."%\n"; 
                endif;
                }
                sendMessage($chatId, $minerstring);
                break;
        case "/toptenaltcoins@FOMO_bot":
                $coinmarketcap = file_get_contents('https://api.coinmarketcap.com/v1/ticker/?limit=11');
                $wsi = json_decode($coinmarketcap, true);

                $marketcaptotal=0;
                #sum the marketcaps
                foreach(range(1,10) as $x) { 
                $marketcaptotal=$marketcaptotal+$wsi[$x]['market_cap_usd']; 
                }
	
                $marketcaptotal=floor($marketcaptotal);
                #WSI is just marketcap standardized to 1 billion to 1000 pts
                $wsivalue=($marketcaptotal/1000000000)*1000;


                $weightedpct=0;
                foreach(range(1,10) as $x) { 
                $weightedpct=$weightedpct+( ($wsi[$x]['percent_change_24h'])*($wsi[$x]['market_cap_usd'])/$marketcaptotal);
                }

                #build individual message
                if ($weightedpct < 0):
                    $wsistring="<b>Whalepool Shitcoin Index (WSI 10)</b>\n<b>         ".number_format($wsivalue,"2")." pts (".number_format($weightedpct,"2")."%) </b>\n<code> Name Value(BTC) 24Hr Chg</code>\n";
                else:
                    $wsistring="<b>Whalepool Shitcoin Index (WSI 10)</b>\n<b>         ".number_format($wsivalue,"2")." pts (+".number_format($weightedpct,"2")."%) </b>\n<code> Name Value(BTC) 24Hr Chg</code>\n";
                endif;

                foreach(range(1,10) as $x) { 
                $wsirank=$wsi[$x]['rank']-1;
                if ($wsi[$x]['percent_change_24h'] < 0):
                    $wsistring=$wsistring.$wsirank.". <code>".$wsi[$x]['symbol']."</code>: ".number_format($wsi[$x]['price_btc'],8)." (".number_format($wsi[$x]['percent_change_24h'],2)."%)\n";
                else:
                    $wsistring=$wsistring.$wsirank.". <code>".$wsi[$x]['symbol']."</code>: ".number_format($wsi[$x]['price_btc'],8)." (+".number_format($wsi[$x]['percent_change_24h'],2)."%)\n";
                endif;
                
                }

                sendMessage($chatId, $wsistring);
                break;

case "/getfutureslongshort@FOMO_bot":
                $grabratios = file_get_contents('https://www.okcoin.com/future/getFuturePositionRatio.do?type=1&symbol=0');
                $grabratiosarray = json_decode($grabratios, true);
                $latestshort = $grabratiosarray['selldata'][19]*100;
                $min90short = $grabratiosarray['selldata'][0]*100;
                $min45short = $grabratiosarray['selldata'][10]*100;
                $latestlong = $grabratiosarray['buydata'][19]*100;
                $min90long = $grabratiosarray['buydata'][0]*100;
                $min45long = $grabratiosarray['buydata'][10]*100;
                

                sendMessage($chatId, "<b>OKCoin  LONG     SHORT</b>\n<code>Now  :</code> ".number_format($latestlong,"2")."%   ".number_format($latestshort,"2")."%\n<code>45min:</code> ".number_format($min45long,"2")."%   ".number_format($min45short,"2")."%\n<code>90min:</code> ".number_format($min90long,"2")."%   ".number_format($min90short,"2")."%");
                break;
        case "/getfuturestoptrader@FOMO_bot":
                $grabratiosarray = json_decode($grabratios, true);
                $latestshort = $grabratiosarray['selldata'][49]*100;
                $latestlong = $grabratiosarray['buydata'][49]*100;

                

                sendMessage($chatId, "<b>OKCoin Top Trader Sentiment</b>\n<code>Long :</code> ".number_format($latestlong,"2")."%\n<code>Short:</code> ".number_format($latestshort,"2")."%");
                break;
        case "/getbitmexfunding@FOMO_bot":
                $grabmex = file_get_contents('https://www.bitmex.com/api/v1/instrument?symbol=XBTUSD&count=100&reverse=false');
                $grabmexarray = json_decode($grabmex, true);
                $fundingrate8hr = $grabmexarray[0]['fundingRate']*100;
                $fundingratedaily = (pow((1+($fundingrate8hr/100)),3)-1)*100;

                $predictedfundingrate = $grabmexarray[0]['indicativeFundingRate']*100;
                $fundingrateannual = (pow((1+($fundingrate8hr/100)),1095)-1)*100;
                $nextfunding = strtotime($grabmexarray[0]['fundingTimestamp']);
                $currentts = strtotime($grabmexarray[0]['timestamp']);
                $timetofunding = $nextfunding-$currentts;
                $strtimetofunding=gmdate("H:i:s", $timetofunding);
                $thehours=floor($timetofunding/60/60);
                $theminutes=floor($timetofunding/60)-($thehours*60);
                $predictedtime=($timetofunding/60/60)+8;               

                sendMessage($chatId, "<b>BitMEX BTC/USD Swap Funding</b>\nPositive rate -> Longs pay shorts\nCurrent payment in: ".$thehours." hr ".$theminutes." min\n<code>Nominal(8-hour):</code> ".number_format($fundingrate8hr,"4")."%\n<code>Daily Rate     :</code> ".number_format($fundingratedaily, "3")."%\n<code>Next predicted :</code> ".number_format($predictedfundingrate,"4")."% (in ".number_format($predictedtime)." hours)");
                break;

case "/getswaprates@FOMO_bot":
                $btcffrjson = file_get_contents('https://api.bitfinex.com/v1/lendbook/BTC?limit_bids=1&limit_asks=0');
                $btcffrarray = json_decode($btcffrjson, true);
                $btcffrjson2 = file_get_contents('https://api.bitfinex.com/v1/lendbook/BTC?limit_bids=0&limit_asks=1');
                $btcffrarray2 = json_decode($btcffrjson2, true);
                if (isset($btcffrarray)) {
                $btcffr1 = round($btcffrarray['bids'][0]['rate'],1);
                $btcffr1d=round($btcffr1/365,4);
                } else {
                $btcffr1 = "N/A";
                }
                if (isset($btcffrarray2)) {
                $btcffr2 = round($btcffrarray2['asks'][0]['rate'],1);
                $btcffr2d=round($btcffr2/365,4);
                } else {
                $btcffr2 = "N/A";
                }

                $grabbtcmarg = file_get_contents('https://api.bitfinex.com/v1/lends/btc');
                $btcmargarray = json_decode($grabbtcmarg, true);
                $thebtcffr = $btcmargarray[0]['rate'];
                $thebtcffr1=round($thebtcffr/365,4);

                // USD swaps

                $usdffrjson = file_get_contents('https://api.bitfinex.com/v1/lendbook/USD?limit_bids=0&limit_asks=1');
                $usdffrarray = json_decode($usdffrjson, true);
                if (isset($usdffrarray)) {
                $usdffr = round($usdffrarray['asks'][0]['rate'],1);
                $usdffrd=round($usdffr/365,4);
                } else {
                $usdffr = "N/A";
                }
                $usdffrjson2 = file_get_contents('https://api.bitfinex.com/v1/lendbook/USD?limit_bids=1&limit_asks=0');
                $usdffrarray2 = json_decode($usdffrjson2, true);
                if (isset($usdffrarray2)) {
                $usdffr2 = round($usdffrarray2['bids'][0]['rate'],1);
                $usdffr2d=round($usdffr2/365,4);
                } else {
                $usdffr2 = "N/A";
                }

                $grabusdmarg = file_get_contents('https://api.bitfinex.com/v1/lends/usd');
                $usdmargarray = json_decode($grabusdmarg, true);
                $theusdffr = $usdmargarray[0]['rate'];
                $theusdffr1=round($theusdffr/365,4);

                // LTC swaps

                $ltcffrjson = file_get_contents('https://api.bitfinex.com/v1/lendbook/LTC?limit_bids=0&limit_asks=1');
                $ltcffrarray = json_decode($ltcffrjson, true);
                if (isset($ltcffrarray)) {
                $ltcffr = round($ltcffrarray['asks'][0]['rate'],1);
                $ltcffrd=round($ltcffr/365,4);
                } else {
                $ltcffr = "N/A";
                }
                $ltcffrjson2 = file_get_contents('https://api.bitfinex.com/v1/lendbook/LTC?limit_bids=1&limit_asks=0');
                $ltcffrarray2 = json_decode($ltcffrjson2, true);
                if (isset($ltcffrarray2)) {
                $ltcffr2 = round($ltcffrarray2['bids'][0]['rate'],1);
                $ltcffr2d=round($ltcffr2/365,4);
                } else {
                $ltcffr2 = "N/A";
                }
                $grabltcmarg = file_get_contents('https://api.bitfinex.com/v1/lends/ltc');
                $ltcmargarray = json_decode($grabltcmarg, true);
                $theltcffr = $ltcmargarray[0]['rate'];
                $theltcffr1=round($theltcffr/365,4);

                // ETH swaps

                $ethffrjson = file_get_contents('https://api.bitfinex.com/v1/lendbook/ETH?limit_bids=0&limit_asks=1');
                $ethffrarray = json_decode($ethffrjson, true);
                if (isset($ethffrarray)) {
                $ethffr = round($ethffrarray['asks'][0]['rate'],1);
                $ethffrd=round($ethffr/365,4);
                } else {
                $ethffr = "N/A";
                }
                $ethffrjson2 = file_get_contents('https://api.bitfinex.com/v1/lendbook/ETH?limit_bids=1&limit_asks=0');
                $ethffrarray2 = json_decode($ethffrjson2, true);
                if (isset($ethffrarray2)) {
                $ethffr2 = round($ethffrarray2['bids'][0]['rate'],1);
                $ethffr2d=round($ethffr2/365,4);
                } else {
                $ethffr2 = "N/A";
                }

                $grabethmarg = file_get_contents('https://api.bitfinex.com/v1/lends/eth');
                $ethmargarray = json_decode($grabethmarg, true);
                $theethffr = $ethmargarray[0]['rate'];
                $theethffr1=round($theethffr/365,4);
                // ETC swaps

                $etcffrjson = file_get_contents('https://api.bitfinex.com/v1/lendbook/ETC?limit_bids=0&limit_asks=1');
                $etcffrarray = json_decode($etcffrjson, true);
                if (isset($etcffrarray)) {
                $etcffr = round($etcffrarray['asks'][0]['rate'],1);
                $etcffrd=round($etcffr/365,4);
                } else {
                $etcffr = "N/A";
                }
                $etcffrjson2 = file_get_contents('https://api.bitfinex.com/v1/lendbook/ETC?limit_bids=1&limit_asks=0');
                $etcffrarray2 = json_decode($etcffrjson2, true);
                if (isset($etcffrarray2)) {
                $etcffr2 = round($etcffrarray2['bids'][0]['rate'],1);
                $etcffr2d=round($etcffr2/365,4);
                } else {
                $etcffr2 = "N/A";
                }
                $grabetcmarg = file_get_contents('https://api.bitfinex.com/v1/lends/etc');
                $etcmargarray = json_decode($grabetcmarg, true);
                $theetcffr = $etcmargarray[0]['rate'];
                $theetcffr1=round($theetcffr/365,4);
                sendMessage($chatId, "<b>Bitfinex Margin Funding Daily Rates</b>\n<code>     Borrow  Lend    FFR</code>\n<code>BTC: </code>".number_format($btcffr2d, "4")."% : ".number_format($btcffr1d, "4")."% : ".number_format($thebtcffr1, "4")."%\n<code>USD: </code>".number_format($usdffrd, "4")."% : ".number_format($usdffr2d, "4")."% : ".number_format($theusdffr1, "4")."%\n<code>LTC: </code>".number_format($ltcffrd, "4")."% : ".number_format($ltcffr2d, "4")."% : ".number_format($theltcffr1, "4")."%\n<code>ETC: </code>".number_format($etcffrd, "4")."% : ".number_format($etcffr2d, "4")."% : ".number_format($theetcffr1, "4")."%\n<code>ETH: </code>".number_format($ethffrd, "4")."% : ".number_format($ethffr2d, "4")."% : ".number_format($theethffr1, "4")."% ");
                break;

*/

/*



private commands 





*/
 case "/futures_okcoin_top_holders":
sendMessageTypingAction($chatId);
         include_once('/usr/share/nginx/html/simplehtml/simple_html_dom.php');
$topconts=file_get_html('https://www.okcoin.com/future/futureTop.do?type=0&symbol=0');
$tdcount=0;
foreach($topconts->find('div.futureIndexTable') as $table) {
foreach($table->find('table') as $tr) {
foreach($tr->find('td') as $td) {

$tdcount++;

if ($tdcount==14) {
$top1=$td->text();
}
elseif ($tdcount==26) {
$top2=$td->text();
}
elseif ($tdcount==38) {
$top3=$td->text();
}
elseif ($tdcount==50) {
$top4=$td->text();
}
elseif ($tdcount==62) {
$top5=$td->text();
}
elseif ($tdcount==74) {
$top6=$td->text();
}
elseif ($tdcount==86) {
$top7=$td->text();
}
elseif ($tdcount==98) {
$top8=$td->text();
}
elseif ($tdcount==110) {
$top9=$td->text();
}
elseif ($tdcount==122) {
$top10=$td->text();
}

}
}
}


           sendMessage($chatId, "<b>Top Futures Contract Holders (OKCoin)</b>\n<code> 1: </code>".number_format($top1)." BTC\n<code> 2: </code>".number_format($top2)." BTC\n<code> 3: </code>".number_format($top3)." BTC\n<code> 4: </code>".number_format($top4)." BTC\n<code> 5: </code>".number_format($top5)." BTC\n<code> 6: </code>".number_format($top6)." BTC\n<code> 7: </code>".number_format($top7)." BTC\n<code> 8: </code>".number_format($top8)." BTC\n<code> 9: </code>".number_format($top9)." BTC\n<code>10: </code>".number_format($top10)." BTC\n".$currtimestamp);
                break;


          case "/futures_okcoin_premium":
sendMessageTypingAction($chatId);
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

                //sendMessage($chatId, "<b>Bitcoin Futures Premiums (OKCoin)</b>\n<code>Index    : </code>$".$okcixprice."\n<code>Weekly   : </code>$".$okcwkprice." ($".$wkp." ; ".$wkpremium."%)\n<code>Biweekly : </code>$".$okcbiwkprice." ($".$bip." ; ".$biwkpremium."%)\n<code>Quarterly: </code>$".$okcqtprice." ($".$qtp." ; ".$qtpremium."%)");

                sendMessage($chatId, "<b>Bitcoin Futures Premiums (OKCoin)</b>\n<code>Index : </code>$".number_format($okcixprice,"2")."\n<code>Weekly: </code>$".number_format($okcwkprice,"2")." ($".number_format($wkp,"2")." ; ".number_format($wkpremium,"2")."%)\n<code>Biwkly: </code>$".number_format($okcbiwkprice,"2")." ($".number_format($bip, "2")." ; ".number_format($biwkpremium, "2")."%)\n<code>Qtly  : </code>$".number_format($okcqtprice, "2")." ($".number_format($qtp,"2")." ; ".number_format($qtpremium,"2")."%)\n".$currtimestamp);
                break;
    
        case "/btcusd_ticker":
sendMessageTypingAction($chatId);
                $finex = file_get_contents('https://api.bitfinex.com/v1/pubticker/BTCUSD');
                $stamp = file_get_contents('https://www.bitstamp.net/api/ticker');
                $gaydax = url_get_contents('https://api.gdax.com/products/BTC-USD/ticker');
                $btce = file_get_contents('https://btc-e.com/api/3/ticker/btc_usd');
                $itbit = file_get_contents('https://api.itbit.com/v1/markets/XBTUSD/ticker');
                $okcoin = file_get_contents('https://www.okcoin.com/api/v1/ticker.do?symbol=btc_usd');
                $gemini = file_get_contents('https://api.gemini.com/v1/pubticker/btcusd');
		$kraken = file_get_contents('https://api.kraken.com/0/public/Ticker?pair=XBTUSD');
		$quoine = file_get_contents('https://api.quoine.com/products/');

                $finexarray = json_decode($finex,true);
                $stamparray = json_decode($stamp,true);
                $gaydaxarray = json_decode($gaydax,true);
                $btcearray = json_decode($btce,true);
                $itbitarray = json_decode($itbit,true);
                $okcoinarray = json_decode($okcoin, true);
                $geminiarray = json_decode($gemini, true);
		$krakenarray = json_decode($kraken, true);
		$quoinearray = json_decode($quoine, true);


                $finexprice = $finexarray['last_price'];
                $stampprice = $stamparray['last'];
                $gaydaxprice = $gaydaxarray['price'];
                $btceprice = $btcearray['btc_usd']['last'];
                $itbitprice = $itbitarray['lastPrice'];
                $okcoinprice = $okcoinarray['ticker']['last'];
                $geminiprice = $geminiarray['last'];
                $krakenprice = $krakenarray['result']['XXBTZUSD']['c'][0];
		$quoineprice = $quoinearray[0]['last_traded_price'];


                $finexvol = $finexarray['volume'];
                $stampvol = $stamparray['volume'];
                $gaydaxvol = $gaydaxarray['volume'];
                $btcevol = $btcearray['btc_usd']['vol_cur'];
                $itbitvol = $itbitarray['volume24h'];
                $okcoinvol = $okcoinarray['ticker']['vol'];
                $geminivol = $geminiarray['volume']['BTC'];
                $krakenvol = $krakenarray['result']['XXBTZUSD']['v'][1];
		$quoinevol = $quoinearray[0]['volume_24h'];


		$data = array(
		    array('name' => 'Bitfinex', 'price' => $finexprice, 'vol' => $finexvol),
		    array('name' => 'Bitstamp', 'price' => $stampprice, 'vol' => $stampvol),
		    array('name' => 'GDAX    ', 'price' => $gaydaxprice, 'vol' => $gaydaxvol),
		    array('name' => 'BTC-e   ', 'price' => $btceprice, 'vol' => $btcevol),
		    array('name' => 'itBit   ', 'price' => $itbitprice, 'vol' => $itbitvol),
		    array('name' => 'OKCoin  ', 'price' => $okcoinprice, 'vol' => $okcoinvol),
		    array('name' => 'Gemini  ', 'price' => $geminiprice, 'vol' => $geminivol),
		    array('name' => 'Kraken  ', 'price' => $krakenprice, 'vol' => $krakenvol),
		    array('name' => 'Quoine  ', 'price' => $quoineprice, 'vol' => $quoinevol),
		);

		usort($data, make_comparer(['vol', SORT_DESC]));



           $totalvol = $finexvol+$stampvol+$gaydaxvol+$btcevol+$itbitvol+$okcoinvol+$geminivol+$krakenvol+$quoinevol;
                $volwgtavg=($finexprice*($finexvol/$totalvol))+($stampprice*($stampvol/$totalvol))+($gaydaxprice*($gaydaxvol/$totalvol))+($btceprice*($btcevol/$totalvol))+($itbitprice*($itbitvol/$totalvol))+($okcoinprice*($okcoinvol/$totalvol))+($geminiprice*($geminivol/$totalvol))+($krakenprice*($krakenvol/$totalvol))+($quoineprice*($quoinevol/$totalvol));

#sendMessage($chatId, "<b>BTC/USD Ticker (24H BTC Vol)</b>\n<code>".$data[0]['name'].": </code>$".number_format($data[0]['price'],"2")." (".number_format($data[0]['vol'],"0").")\n<code>Bitstamp: </code>$".number_format($stampprice,"2")." (".number_format($stampvol,"0").")\n<code>BTCe    : </code>$".number_format($btceprice,"2")." (".number_format($btcevol,"0").")\n<code>Kraken  : </code>$".number_format($krakenprice,"2")." (".number_format($krakenvol,"0").")\n<code>GDAX    : </code>$".number_format($gaydaxprice,"2")." (".number_format($gaydaxvol,"0").")\n<code>itBit   : </code>$".number_format($itbitprice,"2")." (".number_format($itbitvol,"0").")\n<code>OKCoin  : </code>$".number_format($okcoinprice,"2")." (".number_format($okcoinvol,"0").")\n<code>Gemini  : </code>$".number_format($geminiprice,"2")." (".number_format($geminivol,"0").")\n<code>Quoine  : </code>$".number_format($quoineprice,"2")." (".number_format($quoinevol,"0").")\n<code>------------------------</code>\n<code>VolWgtPr: </code>$".number_format($volwgtavg,"2").", (".number_format($totalvol,"0").")\n".$currtimestamp);

sendMessage($chatId, "<b>BTC/USD Ticker (24H BTC Vol)</b>\n<code>".$data[0]['name'].": </code>$".number_format($data[0]['price'],"2")." (".number_format($data[0]['vol'],"0").")\n<code>".$data[1]['name'].": </code>$".number_format($data[1]['price'],"2")." (".number_format($data[1]['vol'],"0").")\n<code>".$data[2]['name'].": </code>$".number_format($data[2]['price'],"2")." (".number_format($data[2]['vol'],"0").")\n<code>".$data[3]['name'].": </code>$".number_format($data[3]['price'],"2")." (".number_format($data[3]['vol'],"0").")\n<code>".$data[4]['name'].": </code>$".number_format($data[4]['price'],"2")." (".number_format($data[4]['vol'],"0").")\n<code>".$data[5]['name'].": </code>$".number_format($data[5]['price'],"2")." (".number_format($data[5]['vol'],"0").")\n<code>".$data[6]['name'].": </code>$".number_format($data[6]['price'],"2")." (".number_format($data[6]['vol'],"0").")\n<code>".$data[7]['name'].": </code>$".number_format($data[7]['price'],"2")." (".number_format($data[7]['vol'],"0").")\n<code>".$data[8]['name'].": </code>$".number_format($data[8]['price'],"2")." (".number_format($data[8]['vol'],"0").")\n<code>------------------------</code>\n<code>VolWgtPr: </code>$".number_format($volwgtavg,"2").", (".number_format($totalvol,"0").")\n".$currtimestamp);
 
                break;
case "/btceur_ticker":
sendMessageTypingAction($chatId);
                $gaydax = url_get_contents('https://api.gdax.com/products/BTC-EUR/ticker');
                $stamp = file_get_contents('https://www.bitstamp.net/api/v2/ticker/btceur/');
		$kraken = file_get_contents('https://api.kraken.com/0/public/Ticker?pair=XBTEUR');

                $gaydaxarray = json_decode($gaydax,true);
                $stamparray = json_decode($stamp,true);
		$krakenarray = json_decode($kraken, true);

                $gaydaxprice = $gaydaxarray['price'];
                $stampprice = $stamparray['last'];
                $krakenprice = $krakenarray['result']['XXBTZEUR']['c'][0];

                $gaydaxvol = $gaydaxarray['volume'];
                $stampvol = $stamparray['volume'];
                $krakenvol = $krakenarray['result']['XXBTZEUR']['v'][1];

		$data = array(
		    array('name' => 'GDAX    ', 'price' => $gaydaxprice, 'vol' => $gaydaxvol),
		    array('name' => 'Bitstamp', 'price' => $stampprice, 'vol' => $stampvol),
		    array('name' => 'Kraken  ', 'price' => $krakenprice, 'vol' => $krakenvol),
		);

		usort($data, make_comparer(['vol', SORT_DESC]));



           $totalvol = $gaydaxvol+$stampvol+$krakenvol;
                $volwgtavg=($gaydaxprice*($gaydaxvol/$totalvol))+($stampprice*($stampvol/$totalvol))+($krakenprice*($krakenvol/$totalvol));

sendMessage($chatId, "<b>BTC/EUR Ticker (24H BTC Vol)</b>\n<code>".$data[0]['name'].": </code>€".number_format($data[0]['price'],"2")." (".number_format($data[0]['vol'],"0").")\n<code>".$data[1]['name'].": </code>€".number_format($data[1]['price'],"2")." (".number_format($data[1]['vol'],"0").")\n<code>".$data[2]['name'].": </code>€".number_format($data[2]['price'],"2")." (".number_format($data[2]['vol'],"0").")\n<code>------------------------</code>\n<code>VolWgtPr: </code>€".number_format($volwgtavg,"2").", (".number_format($totalvol,"0").")\n".$currtimestamp);
 
                break;
      case "/coinpit":
sendMessageTypingAction($chatId);
                $finex = file_get_contents('https://api.bitfinex.com/v1/pubticker/BTCUSD');
                $stamp = file_get_contents('https://www.bitstamp.net/api/ticker');
                $gaydax = url_get_contents('https://api.gdax.com/products/BTC-USD/ticker');
                $btce = file_get_contents('https://btc-e.com/api/3/ticker/btc_usd');
                $itbit = file_get_contents('https://api.itbit.com/v1/markets/XBTUSD/ticker');
                $okcoin = file_get_contents('https://www.okcoin.com/api/v1/ticker.do?symbol=btc_usd');
                $gemini = file_get_contents('https://api.gemini.com/v1/pubticker/btcusd');
		$kraken = file_get_contents('https://api.kraken.com/0/public/Ticker?pair=XBTUSD');
		$quoine = file_get_contents('https://api.quoine.com/products/');

                $finexarray = json_decode($finex,true);
                $stamparray = json_decode($stamp,true);
                $gaydaxarray = json_decode($gaydax,true);
                $okcoinarray = json_decode($okcoin, true);
                $geminiarray = json_decode($gemini, true);
                $itbitarray = json_decode($itbit, true);
                $krakenarray = json_decode($kraken, true);


                $finexprice = $finexarray['last_price'];
                $stampprice = $stamparray['last'];
                $gaydaxprice = $gaydaxarray['price'];
                $okcoinprice = $okcoinarray['ticker']['last'];
                $geminiprice = $geminiarray['last'];
		$itbitprice = $itbitarray['lastPrice'];
                $krakenprice = $krakenarray['result']['XXBTZUSD']['c'][0];

              
		$data = array(
		    array('name' => 'Bitfinex', 'price' => $finexprice),
		    array('name' => 'Bitstamp', 'price' => $stampprice),
		    array('name' => 'GDAX    ', 'price' => $gaydaxprice),
		    array('name' => 'OKCoin  ', 'price' => $okcoinprice),
		    array('name' => 'Gemini  ', 'price' => $geminiprice),
		    array('name' => 'Itbit  ', 'price' => $itbitprice),
		    array('name' => 'Kraken  ', 'price' => $krakenprice),
		);

		usort($data, make_comparer('price'));

sendMessage($chatId, "<b>Coinpit Index: </b>".number_format($data[3]['price'],"2")."\n<code>".$data[0]['name'].": </code>$".number_format($data[0]['price'],"2")."\n<code>".$data[1]['name'].": </code>$".number_format($data[1]['price'],"2")."\n<code>".$data[2]['name'].": </code>$".number_format($data[2]['price'],"2")."\n<code>".$data[3]['name'].": </code>$".number_format($data[3]['price'],"2")."\n<code>".$data[4]['name'].": </code>$".number_format($data[4]['price'],"2")."\n<code>".$data[5]['name'].": </code>$".number_format($data[5]['price'],"2")."\n<code>".$data[6]['name'].": </code>$".number_format($data[6]['price'],"2")."\n".$currtimestamp);
 
                break;
        case "/china_ticker":
sendMessageTypingAction($chatId);
                $huobifetch = file_get_contents('http://api.huobi.com/staticmarket/ticker_btc_json.js');
                $huobiarray = json_decode($huobifetch, true);
                $huobiprice = $huobiarray['ticker']['last'];
                $huobivol = $huobiarray['ticker']['vol'];


                $chinafetch = file_get_contents('https://www.okcoin.cn/api/v1/ticker.do?symbol=btc_cny');
                $chinaarray = json_decode($chinafetch, true);
                $chinaprice = $chinaarray['ticker']['last'];
                $chinavol = $chinaarray['ticker']['vol'];

                $btcchinafetch = file_get_contents('https://data.btcchina.com/data/ticker?market=btccny');
                $btcchinaarray = json_decode($btcchinafetch, true);
                $btcchinaprice = $btcchinaarray['ticker']['last'];
                $btcchinavol = $btcchinaarray['ticker']['vol'];


		$totalvol=$huobivol+$chinavol+$btcchinavol;
		$volwgtprice=($huobivol/$totalvol)*$huobiprice+($chinavol/$totalvol)*$chinaprice+($btcchinavol/$totalvol)*$btcchinaprice;

                sendMessage($chatId, "<b>CNY Bitcoin Exchange Ticker</b>\n<code>Huobi : </code>¥".number_format($huobiprice,"0")." (".number_format($huobivol)." BTC)\n<code>OKCoin: </code>¥".number_format($chinaprice,"0")." (".number_format($chinavol)." BTC)\n<code>BTCC  : </code>¥".number_format($btcchinaprice,"0")." (".number_format($btcchinavol)." BTC)\n<code>------------------------</code>\n<code>VoLWgtPrice: </code>¥".number_format($volwgtprice,"0")." (".number_format($totalvol)." BTC)\n".$currtimestamp);
                break;
        
        case "/china_premium":
sendMessageTypingAction($chatId);
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
$cfbpijson = file_get_contents('https://www.cryptofacilities.com/derivatives/api/cfbpi');
$cfbpiarray = json_decode($cfbpijson, true);
if (isset($cfbpiarray)) {
if ($cfbpiarray['result'] == "success") {
$cfbpi = $cfbpiarray['cf-bpi'];
} else {
$cfbpi = "0";
return;
} 
} else {
$cfbpi = "0";
return;
}
$finexprice = $cfbpi;

#$finexprice = $finexarray['last'];
                $chinausd=round($chinaprice/$cnyconv,2);
                $bfxcny=round($finexprice*$cnyconv,0);
                $chinadiff =round($chinausd - $finexprice,2);
                $chinaprem=round(($chinadiff/$finexprice)*100,2);
                sendMessage($chatId, "<b>CNY vs. USD (".$cnyconv.") Spot Prices</b>\n<code>OKCoin       :</code> ¥".number_format($chinaprice,"0")." ($".number_format($chinausd,"0").")\n<code>CF-BPI       :</code> $".number_format($finexprice,"0")." (¥".number_format($bfxcny,"0").")\n<code>China Premium:</code> $".number_format($chinadiff,"2")." (".number_format($chinaprem,"2")."%)\n".$currtimestamp);
                break;

        case "/japan_ticker":
sendMessageTypingAction($chatId);
                $coincheckfetch = file_get_contents('https://coincheck.com/api/ticker');
                $coincheckarray = json_decode($coincheckfetch, true);
                $coincheckprice = $coincheckarray['last'];
                $coincheckvol = $coincheckarray['volume'];


                $quoinefetch = file_get_contents('https://api.quoine.com/products/');
                $quoinearray = json_decode($quoinefetch, true);
                $quoineprice = $quoinearray[2]['last_traded_price'];
                $quoinevol = $quoinearray[2]['volume_24h'];

                $bitflyerfetch = file_get_contents('https://api.bitflyer.jp/v1/ticker?productcode=BTC_JPY');
                $bitflyerarray = json_decode($bitflyerfetch, true);
                $bitflyerprice = $bitflyerarray['ltp'];
                $bitflyervol = $bitflyerarray['volume_by_product'];

		$totalvol=$coincheckvol+$quoinevol+$bitflyervol;
		$volwgtprice=($coincheckvol/$totalvol)*$coincheckprice+($quoinevol/$totalvol)*$quoineprice+($bitflyervol/$totalvol)*$bitflyerprice;


                sendMessage($chatId, "<b>JPY Bitcoin Exchange Ticker</b>\n<code>CoinCheck: </code>¥".number_format($coincheckprice,"0")." (".number_format($coincheckvol)." BTC)\n<code>Quoine   : </code>¥".number_format($quoineprice,"0")." (".number_format($quoinevol)." BTC)\n<code>BitFlyer : </code>¥".number_format($bitflyerprice,"0")." (".number_format($bitflyervol)." BTC)\n<code>------------------------</code>\n<code>VolWgtPr: </code>¥".number_format($volwgtprice,"0")." (".number_format($totalvol)." BTC)\n".$currtimestamp);
                break;
        
        case "/japan_premium":
sendMessageTypingAction($chatId);
                $quoinefetch = file_get_contents('https://api.quoine.com/products/');
                $quoinearray = json_decode($quoinefetch, true);
                $quoineprice = $quoinearray[2]['last_traded_price'];

                $usdjpy = file_get_contents('http://free.currencyconverterapi.com/api/v3/convert?q=USD_JPY');
                $usdjpydec = json_decode($usdjpy, true);
                $jpyconv = $usdjpydec['results']['USD_JPY']['val'];


$cfbpijson = file_get_contents('https://www.cryptofacilities.com/derivatives/api/cfbpi');
$cfbpiarray = json_decode($cfbpijson, true);
if (isset($cfbpiarray)) {
if ($cfbpiarray['result'] == "success") {
$cfbpi = $cfbpiarray['cf-bpi'];
} else {
$cfbpi = "0";
return;
} 
} else {
$cfbpi = "0";
return;
}
$finexprice = $cfbpi;

#$finexprice = $finexarray['last'];
                $chinausd=round($quoineprice/$jpyconv,2);
                $bfxjpy=round($finexprice*$jpyconv,0);
                $chinadiff =round($chinausd - $finexprice,2);
                $chinaprem=round(($chinadiff/$finexprice)*100,2);
                //sendMessage($chatId, "<b>China vs. Western Exchange Balance</b>\nPremium in Huobi China \nCurrent Price: (¥".$huobipricer."->$".$chinausd.")\nRelative to Finex ($".$finexprice."): $".$chinadiff." (".$chinaprem."%)");

                sendMessage($chatId, "<b>JPY vs. USD (".$jpyconv.") Spot Prices</b>\n<code>Quoine       :</code> ¥".number_format($quoineprice,"0")." ($".number_format($chinausd,"0").")\n<code>CF-BPI       :</code> $".number_format($finexprice,"0")." (¥".n