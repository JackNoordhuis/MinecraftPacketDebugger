<?php

/**
 * entry.php – MinecraftPacketDebugger
 *
 * Copyright (C) 2019 Jack Noordhuis
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author Jack
 *
 */

declare(strict_types=1);

use jacknoordhuis\minecraftpacketdebugger\lib\MinecraftPacketDebugger;
use jacknoordhuis\minecraftpacketdebugger\lib\utils\Utils;

require_once __DIR__ . "/vendor/autoload.php";

$opts = getopt("", ["bind:", "server:"]);
if(count(array_values($opts)) !== 2) {
	echo "[Error] Usage: php entry.php --bind x.x.x.x:port --server x.x.x.x:port" . PHP_EOL;
	exit();
}
foreach($opts as $name => $value) {
	if(!strpos($value, ":")) {
		echo "[Error] Invalid address format '$value' given for option '$name'" . PHP_EOL;
		exit();
	}

	switch($name) {
		case "bind":
			$bind = explode(":", $value);
			break;
		case "server":
			$server = explode(":", $value);
			break;
	}
}

(new MinecraftPacketDebugger(Utils::addressFromHostname($server[0], (int) $server[1]), Utils::addressFromHostname($bind[0], (int) $bind[1])))->start();
