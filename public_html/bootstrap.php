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

require_once __DIR__.'/vendor/autoload.php';
require_once 'functions.php';

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['user'])) {
    checkUser($_POST['user'], $_POST['pass']);
}

if (isset($_SESSION['discard_after']) && time() > $_SESSION['discard_after']) {
    $_SESSION = array();
    session_destroy();
}
