<?php

/**
 * Utils.php – MinecraftPacketDebugger
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

namespace jacknoordhuis\minecraftpacketdebugger\lib\utils;

use raklib\utils\InternetAddress;

/**
 * Random utility functions that don't fit anywhere else.
 */
abstract class Utils {

	/**
	 * Helper method to construct a new internet address object from a domain name.
	 *
	 * @param string $address
	 * @param int    $port
	 * @param int    $version
	 *
	 * @return \raklib\utils\InternetAddress
	 */
	public static function addressFromHostname(string $address, int $port, int $version = 4) : InternetAddress {
		return new InternetAddress(gethostbyname($address), $port, $version);
	}

	/**
	 * Get the short name of an objects class.
	 *
	 * @param object $object
	 *
	 * @return string
	 */
	public static function getShortClassName(object $object) : string {
		return (new \ReflectionObject($object))->getShortName();
	}

}