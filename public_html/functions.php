<?php
/**
 * Jmz XMR
 * Copyright (C) 2020  James Taylor
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require 'Rig.php';
require 'XmrPool.php';
require 'db.class.php';
require 'config.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['logout'])) {
    $_SESSION = array();
    session_destroy();
}

if (isset($_POST['session_key'])
    && $_SESSION['session_key'] == $_POST['session_key']
) {
    if (isset($_POST['addRig'])) {
        $rig = new Rig(
            $_POST['AddRigName'],
            $_POST['AddRigUrl'],
            $_POST['AddRigPort'],
            $_POST['AddRigAccessToken']
        );

        $rig->add();
    }

    if (isset($_POST['deleteRig'])) {
        $rig = new Rig($_POST['id']);
        $rig->delete();
    }

    if (isset($_POST['getRig'])) {
        $rig = new Rig($_POST['id']);
        $rig->get();
        echo json_encode($rig);
    }

    if (isset($_POST['editRigId'])) {
        $rig = new Rig(
            $_POST['editRigId'],
            $_POST['EditRigName'],
            $_POST['EditRigUrl'],
            $_POST['EditRigPort'],
            $_POST['EditRigAccessToken']
        );

        $rig->edit();
    }

    if (isset($_POST['addPool'])) {
        $pool = new XmrPool(
            $_POST['poolName'],
            $_POST['address'],
            $_POST['poolType']
        );
        $pool->add();
    }

    if (isset($_POST['deletePool'])) {
        $pool = new XmrPool($_POST['id']);
        $pool->delete();
    }

    if (isset($_POST['getPool'])) {
        $pool = new XmrPool($_POST['id']);
        $pool->get();
        echo json_encode($pool);
    }

    if (isset($_POST['updatePool'])) {
        $pool = new XmrPool(
            $_POST['id'],
            $_POST['poolName'],
            $_POST['address'],
            $_POST['poolType']
        );
        $pool->update();
    }
}

/**
 * Verifies users credentials and sets SESSION as needed
 *
 * @param string $username Entered username
 * @param string $password Entered password
 *
 * @return void
 */
function checkUser($username, $password)
{
    $row = DB::queryFirstRow('SELECT * FROM users WHERE username=%s LIMIT 1', $username);
    if (password_verify($password, $row['password'])) {
        $now = time();
        $_SESSION['loggedin'] = true;
        $_SESSION['discard_after'] = $now + 3600;
        $_SESSION['session_key'] = crypt($username, date("h:i a"));
    }
}

/**
 * Gets all rigs from rigs table
 *
 * @return array
 */
function getRigs()
{
    $Rigs = array();
    $results = DB::query("SELECT * FROM rigs");
    foreach ($results as $row) {
        $rig = new Rig(
            $row['id'],
            $row['name'],
            $row['address'],
            $row['port'],
            $row['accesstoken'],
            $row['update']
        );
        $rig->query();
        $Rigs[] = $rig;
    }
    return $Rigs;
}

/**
 * Gets entered pools from pools table
 *
 * @return array
 */
function getPools()
{
    $pools = array();
    $results = DB::query("SELECT * FROM pools");
    foreach ($results as $row) {
        $pool = new XmrPool($row['id'], $row['name'], $row['address'], $row['poolname']);
        switch ($row['poolname']) {
            case 1:
                $pool->getSupportXmrData();
                $pools[] = $pool;
                break;
            case 2:
                $pool->getNanoPoolData();
                $pools[] = $pool;
                break;
            default:
                break;
        }
    }

    return $pools;
}

/**
 * Formats time for display
 *
 * @param datetime $t time
 *
 * @return string
 */
function formatTime($t)
{
    return sprintf("%02d%s%02d%s%02d", floor($t / 3600), ':', ($t / 60) % 60, ':', $t % 60);
}

/**
 * Queries APIs for monero value
 *
 * @param int $total total calculated of current hashrate
 *
 * @return array
 */
function getDailyNumbers($total)
{
    $url = file_get_contents("https://moneroblocks.info/api/get_stats");
    $json = json_decode($url);
    $url2 = file_get_contents(
        "https://min-api.cryptocompare.com/data/price?fsym=XMR&tsyms=USD"
    );
    $value2 = json_decode($url2);
    $price = $value2->USD;
    $reward = $json->last_reward / 1000000000000;
    $monero = number_format(
        (((($total * 1000) * (1 - ((.6) / 100))) /
                ((142942420993 * 1e9))) * $reward * 86400) * 1000000000, 5
    );
    $usd = number_format(
        ((((($total * 1000) * (1 - ((.6) / 100))) /
                    ((142942420993 * 1e9))) * $reward * 86400) * 1000000000) * $price,
        2
    );
    return array("monero" => $monero, "usd" => $usd);
}

/**
 * Gets supported pools from poolslist table
 *
 * @return array
 */
function getSupportedPools()
{
    $pools = array();
    $results = DB::query("SELECT * FROM poolslist");
    foreach ($results as $row) {
        $pools[] = new XmrPool($row['id'], $row['name'], "");
    }

    return $pools;
}

/**
 * Sends telegram message
 *
 * @param array  $rigInfo Array of rigs
 * @param string $api_key API_KEY from botfather
 * @param string $chat_id CHAT_ID of group
 *
 * @return void
 */
function sendTelegram($rigInfo, $api_key, $chat_id)
{
    $poolInfo = getPools();
    $totalPending = 0;
    $totalPoolHashrate = 0;
    for ($x = 0; $x < count($poolInfo); $x++) {
        $totalPending = $totalPending + $poolInfo[$x]->amtDue;
        $totalPoolHashrate = $totalPoolHashrate + $poolInfo[$x]->totalHashrate;
    }

    $totalHashrate = 0;
    $totalOnline = 0;
    $totalOffline = array();
    $totalUpdates = 0;

    for ($i = 0; $i < count($rigInfo); $i++) {
        $totalHashrate = $totalHashrate + $rigInfo[$i]->currentHash;
        if ($rigInfo[$i]->currentHash != 0) {
            $totalOnline++;
        } else {
            $totalOffline[] = $rigInfo[$i]->rigName . " ";
        }

        if ($rigInfo[$x]->update == true) {
            $totalUpdates++;
        }
    }

    $text = 'Current Hashrate: ' . $totalHashrate . " KH/s" . PHP_EOL .
        'Pool Reported Hashrate: ' . $totalPoolHashrate . " KH/s" . PHP_EOL .
        'Pending Balance: ' . $totalPending . " ɱ" . PHP_EOL .
        "Rig(s) Online: " . $totalOnline;

    if (count($totalOffline) > 0) {
        for ($j = 0; $j < count($totalOffline); $j++) {
            $text .= PHP_EOL . "Rig(s) Offline: " . $totalOffline[$j];
        }
    }

    if ($totalUpdates > 0) {
        $text .= PHP_EOL . "Rigs that needed updates: " . $totalUpdates;
    }

    $params = [
        'chat_id' => $chat_id,
        'text' => $text
    ];

    $ch = curl_init("https://api.telegram.org/bot" . $api_key . "/sendMessage");
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_exec($ch);
    curl_close($ch);
}

/**
 * Sends telegram error message
 *
 * @param string $api_key API_KEY from botfather
 * @param string $chat_id CHAT_ID of group
 *
 * @return void
 */
function checkRigError($api_key, $chat_id)
{
    $notify = false;
    $totalOffline = array();
    $rigInfo = getRigs();

    for ($i = 0; $i < count($rigInfo); $i++) {
        if ($rigInfo[$i]->error == 1) {
            $totalOffline[] = $rigInfo[$i];
        }
    }

    $text = "";

    if (count($totalOffline) > 0) {
        for ($j = 0; $j < count($totalOffline); $j++) {
            if ($totalOffline[$j]->getNotified() == 0) {
                $text .= PHP_EOL . "Rig Offline: " . $totalOffline[$j]->rigName;
                $totalOffline[$j]->setNotified(1);
                $notify = true;
            }
        }
    }

    $params = [
        'chat_id' => $chat_id,
        'text' => $text
    ];

    if ($notify) {
        $ch = curl_init("https://api.telegram.org/bot" . $api_key . "/sendMessage");
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
        curl_close($ch);
    }

}

/**
 * Gets Telegram information if specified
 *
 * @return array
 */
function getTelegram()
{
    return array(getenv("TG_API_KEY"), getenv("TG_CHAT_ID"));
}

/**
 * Gets latest XMRig version
 *
 * @return string Latest version of XMRig from Github
 */
function getLatestMinerVersion() {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.github.com/repos/xmrig/xmrig/releases/latest");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
    $output = json_decode(curl_exec($ch), true);
    curl_close($ch);
    return substr($output["tag_name"], 1);
}
