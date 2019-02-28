<?php

/**
 * EchoLogger.php – MinecraftPacketDebugger
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

namespace jacknoordhuis\minecraftpacketdebugger\cli\loggers\raknet;

class EchoLogger extends StringLogger {

	/**
	 * @inheritdoc
	 */
	public function doLog(string $raw) : void {
		echo $raw . PHP_EOL;
	}

}