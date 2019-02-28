<?php

/**
 * NetworkLogger.php – MinecraftPacketDebugger
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

namespace jacknoordhuis\minecraftpacketdebugger\lib\network;

interface NetworkLogger {

	/**
	 * Retrieve the name/protocol name of the network interface being logged.
	 *
	 * @return string
	 */
	public function getInterfaceName() : string;

	/**
	 * Log the message/data to the output destination.
	 *
	 * @param string $raw
	 */
	public function log(string $raw) : void;

}