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
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * Class XmrPool
 */
class XmrPool
{

    /**
     * ID of pool
     *
     * @var int
     */
    public $id;
    /**
     * Pool Name
     *
     * @var string
     */
    public $poolName;
    /**
     * XMR address
     *
     * @var string
     */
    public $address;
    /**
     * Total Hashes for pool
     *
     * @var int
     */
    public $totalHashes;
    /**
     * Total Valid Shares
     *
     * @var int
     */
    public $validShares;
    /**
     * Total Invalid Shares
     *
     * @var int
     */
    public $invalidShares;
    /**
     * Total Amount paid
     *
     * @var double
     */
    public $amtPaid;
    /**
     * Total Amount Due
     *
     * @var double
     */
    public $amtDue;
    /**
     * ID of Pool Type
     *
     * @var int
     */
    public $poolId;
    /**
     * Total Hashrate at pool
     *
     * @var double
     */
    public $totalHashrate;

    /**
     * XmrPool constructor.
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
     * Constructor for 3 arguments
     *
     * @param string $poolName Name of Pool
     * @param string $address  XMR address
     * @param int    $poolId   ID of pool type
     *
     * @return void
     */
    function _construct3($poolName, $address, $poolId)
    {
        $this->poolName = $poolName;
        $this->address = $address;
        $this->poolId = $poolId;
    }

    /**
     * Constructor for 4 arguments
     *
     * @param int    $id       ID of pool
     * @param string $poolName Pool Name
     * @param string $address  XMR Address
     * @param int    $poolId   ID of pool type
     *
     * @return void
     */
    function _construct4($id, $poolName, $address, $poolId)
    {
        $this->id = $id;
        $this->poolName = $poolName;
        $this->address = $address;
        $this->poolId = $poolId;
    }

    /**
     * Constructor for 1 argument
     *
     * @param int $id ID of pool
     *
     * @return void
     */
    function _construct1($id)
    {
        $this->id = $id;
    }

    /**
     * Adds pool to pools table
     *
     * @return void
     */
    function add()
    {
        $account = DB::queryFirstRow(
            "SELECT id FROM poolslist WHERE id=%s", $this->poolId
        );

        DB::query(
            "INSERT INTO pools set name=%s, address=%s, poolname=%s",
            $this->poolName,
            $this->address,
            $account['id']
        );
    }

    /**
     * Updates the Pools Table
     *
     * @return void
     */
    function update()
    {
        DB::query(
            "UPDATE pools SET name=%s, address=%s, poolname=%i WHERE id=%i",
            $this->poolName,
            $this->address,
            $this->poolId,
            $this->id
        );
    }

    /**
     * Queries the pools table for user entered pools
     *
     * @return void
     */
    function get()
    {
        $row = DB::queryFirstRow(
            'SELECT * FROM pools WHERE id=%i LIMIT 1',
            $this->id
        );
        $account = DB::queryFirstRow(
            "SELECT name FROM poolslist WHERE id=%i", $row['poolName']
        );
        $this->poolName = $row['name'];
        $this->address = $row['address'];
        $this->poolName = $account['name'];
    }

    /**
     * Deletes single pool from poolslist table
     *
     * @return void
     */
    function delete()
    {
        DB::delete('pools', 'id=%s', $this->id);
    }

    /**
     * Queries Nano Pool API for address stats
     *
     * @return void
     */
    function getNanoPoolData()
    {
        $url = file_get_contents(
            "https://api.nanopool.org/v1/xmr/user/" . $this->address
        );
        $json = json_decode($url);
        $balance = $json->data->balance;
        $totalHashrate = number_format(
            (float)($json->data->hashrate / 1000),
            2, '.', ''
        );

        $url = file_get_contents(
            "https://api.nanopool.org/v1/xmr/shareratehistory/" . $this->address
        );
        $json = json_decode($url);

        $totalShares = 0;

        for ($x = 0; $x < count($json->data); $x++) {
            $totalShares = $totalShares + $json->data[$x]->shares;
        }

        $this->totalHashes = $totalShares;
        $this->validShares = "No data";
        $this->invalidShares = "No data";
        $this->amtPaid = "No data";
        $this->amtDue = $balance;
        $this->totalHashrate = $totalHashrate;
        $this->poolId = 2;
    }

    /**
     * Queries Support XMR API for address stats
     *
     * @return void
     */
    function getSupportXmrData()
    {
        $url = file_get_contents(
            "https://supportxmr.com/api/miner/" . $this->address . "/stats"
        );
        $json = json_decode($url);

        $url = file_get_contents(
            "https://supportxmr.com/api/miner/" .
            $this->address .
            "/stats/allWorkers"
        );
        $json2 = json_decode($url);

        $totalHashrate = number_format(
            (float)($json2->global->hash / 1000), 2, '.', ''
        );

        $this->totalHashes = $json->totalHashes;
        $this->validShares = $json->validShares;
        $this->invalidShares = $json->invalidShares;
        $this->amtPaid = $json->amtPaid / 1000000000000;
        $this->amtDue = $json->amtDue / 1000000000000;
        $this->totalHashrate = $totalHashrate;
    }
}
