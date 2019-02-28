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

namespace jacknoordhuis\minecraftpacketdebugger\cli\loggers;

use jacknoordhuis\minecraftpacketdebugger\lib\network\raknet\RakNetLogger;
use jacknoordhuis\minecraftpacketdebugger\lib\utils\Utils;
use pocketmine\network\mcpe\protocol\DataPacket;
use raklib\protocol\AcknowledgePacket;
use raklib\protocol\EncapsulatedPacket;
use raklib\protocol\OfflineMessage;
use function dirname;
use function file_exists;
use function fopen;
use function fwrite;
use function is_dir;
use function mkdir;
use function touch;

class FileLogger extends RakNetLogger {

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

	public function logAcknowledgement(AcknowledgePacket $packet, bool $serverSide) : void {
		$this->log("Received acknowledgement message '" . Utils::getShortClassName($packet) . "' ({$packet::$ID}) from " . ($serverSide ? "server" : "client"));
	}

	public function logMinecraft(DataPacket $pk, bool $serverSide) : void {
		$this->log("Received minecraft packet " . Utils::getShortClassName($pk) . " from " . ($serverSide ? "server" : "client"));
	}

	public function logUnknownMinecraft(EncapsulatedPacket $packet, bool $serverSide) : void {
		$this->log("Received unknown minecraft packet from " . ($serverSide ? "server" : "client") . ": " . bin2hex($packet->buffer));
	}

	public function logOffline(OfflineMessage $message, bool $serverSide) : void {
		$this->log("Received offline message '" . Utils::getShortClassName($message) . "' ({$message::$ID}) from " . ($serverSide ? "server" : "client"));
	}

	public function logConnectedOffline(OfflineMessage $message, bool $serverSide) : void {
		$this->log("Received offline message '" . Utils::getShortClassName($message) . "' ({$message::$ID}) from " . ($serverSide ? "server" : "client") . " when session is already open");
	}

	public function logOfflineConnected(string $buffer, bool $serverSide) : void {
		$this->log("Received connected message from " . ($serverSide ? "server" : "client") . " when session is not open: " . bin2hex($buffer));
	}

	public function logUnknownConnectedOffline(string $buffer, bool $serverSide) : void {
		$this->log("Received unknown offline message from " . ($serverSide ? "server" : "client") . " when session is already open: " . bin2hex($buffer));
	}

	public function logRaw(string $buffer, bool $serverSide) : void {
		$this->log("Received raw message from " . ($serverSide ? "server" : "client") . ": " . bin2hex($buffer));
	}

	/**
	 * @inheritdoc
	 */
	public function log(string $raw) : void {
		fwrite($this->handle, $raw . PHP_EOL);
	}

}