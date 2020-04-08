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

require 'functions.php';

while(true) {
    $telegram = getTelegram();

    if ($telegram[0] != null) {
        checkRigError($telegram[0], $telegram[1]);
    }

    if (date('i') == 00) {
        $rigInfo = getRigs();
        $latestVersion = getLatestMinerVersion();

        for ($x = 0; $x < count($rigInfo); $x++) {
            if ($rigInfo[$x]->minerVersion != 0
                && version_compare($rigInfo[$x]->minerVersion, $latestVersion, '<')) {
                $rigInfo[$x]->setUpdate(true);
            } else {
                $rigInfo[$x]->setUpdate(false);
            }
        }

        if ($telegram[0] != null) {
            sendTelegram($rigInfo, $telegram[0], $telegram[1]);
        }
        sleep(60);
    }
    sleep(30);
}