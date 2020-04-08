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

/**
 * Class Rig
 */
class Rig
{

    /**
     * Id of rig
     *
     * @var int
     */
    public $rigId;
    /**
     * Name of rig
     *
     * @var string
     */
    public $rigName;
    /**
     * Url of rig API
     *
     * @var string
     */
    public $rigUrl;
    /**
     * Port of rig API
     *
     * @var int
     */
    public $rigPort;
    /**
     * Access Token of rig API
     *
     * @var string
     */
    public $rigAccessToken;
    /**
     * Worker ID of the rig
     *
     * @var string
     */
    public $workerId;
    /**
     * Current mining difficulty
     *
     * @var string
     */
    public $diffCurrent;
    /**
     * Current hashrate of rig
     *
     * @var double
     */
    public $currentHash;
    /**
     * Version of miner
     *
     * @var string
     */
    public $minerVersion;
    /**
     * Uptime of rig
     *
     * @var string
     */
    public $uptime;

    /**
     * Update available
     *
     * @var int
     */
    public $update;

    /**
     * Rig Error
     *
     * @var int
     */
    public $error;

    /**
     * Notified of error
     *
     * @var int
     */
    public $notified;

    /**
     * Rig constructor.
     */
    function __construct()
    {
        $get_arguments       = func_get_args();
        $number_of_arguments = func_num_args();

        if (method_exists($this, $method_name = '_construct'.$number_of_arguments)) {
            call_user_func_array(array($this, $method_name), $get_arguments);
        }
    }

    /**
     * Constructor that takes 10 arguments
     * 
     * @param int    $rigId          ID of rig
     * @param string $rigName        Name of rig
     * @param string $rigUrl         URL of the rig API
     * @param int    $rigPort        Port of the rig API
     * @param string $rigAccessToken Access Token for API
     * @param string $workerId       Worker name of rig
     * @param string $diffCurrent    Current miner difficulty
     * @param double $currentHash    Current hashrate of miner
     * @param string $minerVersion   Version of miner
     * @param string $uptime         Uptime of miner
     *
     * @return void
     */
    function _construct10($rigId, $rigName, $rigUrl, $rigPort,
        $rigAccessToken, $workerId, $diffCurrent,
        $currentHash, $minerVersion, $uptime
    ) {
        $this->rigId = $rigId;
        $this->rigName = $rigName;
        $this->rigUrl = $rigUrl;
        $this->rigPort = $rigPort;
        $this->rigAccessToken = $rigAccessToken;
        $this->workerId = $workerId;
        $this->diffCurrent = $diffCurrent;
        $this->currentHash = $currentHash;
        $this->minerVersion = $minerVersion;
        $this->uptime = $uptime;
    }

    /**
     * Constructor that takes 5 arguments
     *
     * @param int    $rigId          ID of rig
     * @param string $rigName        Name of rig
     * @param string $rigUrl         URL of the rig API
     * @param int    $rigPort        Port of the rig API
     * @param string $rigAccessToken Access Token for API
     *
     * @return void
     */
    function _construct5($rigId, $rigName, $rigUrl, $rigPort, $rigAccessToken)
    {
        $this->rigId = $rigId;
        $this->rigName = $rigName;
        $this->rigUrl = $rigUrl;
        $this->rigPort = $rigPort;
        $this->rigAccessToken = $rigAccessToken;
    }

    /**
     * Constructor that takes 6 arguments
     *
     * @param int    $rigId          ID of rig
     * @param string $rigName        Name of rig
     * @param string $rigUrl         URL of the rig API
     * @param int    $rigPort        Port of the rig API
     * @param string $rigAccessToken Access Token for API
     * @param int   $update         Update needed
     *
     * @return void
     */
    function _construct6($rigId, $rigName, $rigUrl, $rigPort, $rigAccessToken, $update)
    {
        $this->rigId = $rigId;
        $this->rigName = $rigName;
        $this->rigUrl = $rigUrl;
        $this->rigPort = $rigPort;
        $this->rigAccessToken = $rigAccessToken;
        $this->update = $update;
    }

    /**
     * Constructor that takes 4 arguments
     *
     * @param string $rigName        Name of rig
     * @param string $rigUrl         URL of the rig API
     * @param int    $rigPort        Port of the rig API
     * @param string $rigAccessToken Access Token for API
     *
     * @return void
     */
    function _construct4($rigName, $rigUrl, $rigPort, $rigAccessToken)
    {
        $this->rigName = $rigName;
        $this->rigUrl = $rigUrl;
        $this->rigPort = $rigPort;
        $this->rigAccessToken = $rigAccessToken;
    }

    /**
     * Constructor that takes 1 argument
     *
     * @param int $rigId ID of rig
     *
     * @return void
     */
    function _construct1($rigId)
    {
        $this->rigId = $rigId;
    }

    /**
     * Adds rig to rigs table
     *
     * @return void
     */
    function add()
    {
        DB::query(
            "INSERT INTO rigs SET name=%s, address=%s, port=%i, accesstoken=%s",
            $this->rigName, $this->rigUrl, $this->rigPort, $this->rigAccessToken
        );
    }

    /**
     * Deletes single rig from rigs table
     *
     * @return void
     */
    function delete()
    {
        DB::delete('rigs', 'id=%s', $this->rigId);
    }

    /**
     * Queries rigs table for single rig
     *
     * @return void
     */
    function get()
    {
        $row = DB::queryFirstRow('SELECT * FROM rigs WHERE id=%i LIMIT 1', $this->rigId);
        $this->rigId = $row['id'];
        $this->rigName = $row['name'];
        $this->rigUrl = $row['address'];
        $this->rigPort = $row['port'];
        $this->rigAccessToken = array_key_exists('accessToken', $row) ? "" : $row['accessToken'];
    }

    /**
     * Updates single rig in rigs table
     *
     * @return void
     */
    function edit()
    {
        DB::query(
            "UPDATE rigs SET name=%s, address=%s, port=%i, accesstoken=%s WHERE id=%i",
            $this->rigName, $this->rigUrl, $this->rigPort, $this->rigAccessToken, $this->rigId
        );
    }

    /**
     * Queries rig API for data
     *
     * @return void
     */
    function query()
    {
        $url = $this->rigUrl . ":" . $this->rigPort . "/1/summary";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if (!empty($this->rigAccessToken)) {
            $headers = [
                'Authorization: Bearer ' . $this->rigAccessToken
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $result = json_decode($response);
        curl_close($ch);
        if ($httpCode == 200) {
            $this->workerId = $result->worker_id;
            $this->diffCurrent = $result->results->diff_current;
            $this->currentHash = number_format(
                (float)($result->hashrate->total[0] / 1000),
                2, '.', ''
            );
            $this->minerVersion = $result->version;
            $this->uptime = formatTime($result->connection->uptime);
            $this->setError(0);
            $this->setNotified(0);
        } else {
            $this->workerId = "0";
            $this->diffCurrent = "0";
            $this->currentHash = "0";
            $this->minerVersion = "0";
            $this->uptime = "0";
            $this->error = "0";
            $this->setError(1);
        }
    }

    /**
     * Set if update is needed
     *
     * @param  int $update Int for update needed
     *
     * @return void
     */
    function setUpdate($update) {
        $this->update = $update;
        DB::query(
            "UPDATE rigs SET `update`=%i WHERE id=%i", $update, $this->rigId
        );
    }

    /**
     * Set if error
     *
     * @param  int $error Int for update needed
     *
     * @return void
     */
    function setError($error) {
        $this->error = $error;
        DB::query(
            "UPDATE rigs SET `error`=%i WHERE id=%i", $error, $this->rigId
        );
    }

    /**
     * Set if notified of error
     *
     * @param int $notified Set if notified
     *
     * @return void
     */
    function setNotified($notified) {
        DB::query(
            "UPDATE rigs SET `notified`=%i WHERE id=%i", $notified, $this->rigId
        );
    }

    function getNotified() {
        $account = DB::queryFirstRow("SELECT `notified` FROM rigs WHERE id=%i", $this->rigId);
        return $account['notified'];
    }
}