<?php

/**
 * RakNetLogger.php – MinecraftPacketDebugger
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

namespace jacknoordhuis\minecraftpacketdebugger\lib\network\raknet;

use jacknoordhuis\minecraftpacketdebugger\lib\network\NetworkLogger;
use jacknoordhuis\minecraftpacketdebugger\lib\utils\Utils;
use pocketmine\network\mcpe\protocol\DataPacket;
use raklib\protocol\AcknowledgePacket;
use raklib\protocol\EncapsulatedPacket;
use raklib\protocol\OfflineMessage;

class RakNetLogger implements NetworkLogger {

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
		echo $raw . PHP_EOL;
	}

	/**
	 * @inheritdoc
	 */
	public function getInterfaceName() : string {
		return "RakNet";
	}

}