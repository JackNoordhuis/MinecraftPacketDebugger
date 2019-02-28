<?php

/**
 * FileLogger.php – MinecraftPacketDebugger
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

use function dirname;
use function file_exists;
use function fopen;
use function fwrite;
use function is_dir;
use function mkdir;
use function touch;

class FileLogger extends StringLogger {

	/** @var resource */
	private $handle;

	public function __construct(string $file) {
		if(!file_exists($file)) {
			$dir = dirname($file);
			if(!is_dir($dir)) {
				mkdir($dir, 0777, true);
				if(!is_dir($dir)) {
					throw new \RuntimeException("Couldn't create directory for log file! Make sure you have permission to access '$dir'.");
				}
			}

			touch($file);
			if(!file_exists($file)) {
				throw new \RuntimeException("Couldn't create log file! Make sure you have permission to access '$file'.");
			}
		}

		$this->handle = fopen($file, "w");
	}

	/**
	 * @inheritdoc
	 */
	public function doLog(string $raw) : void {
		fwrite($this->handle, $raw . PHP_EOL);
	}

}