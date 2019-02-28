<?php

/**
 * StringLogger.php – MinecraftPacketDebugger
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

use jacknoordhuis\minecraftpacketdebugger\lib\network\raknet\RakNetFilter;
use jacknoordhuis\minecraftpacketdebugger\lib\network\raknet\RakNetLogger;
use jacknoordhuis\minecraftpacketdebugger\lib\utils\Utils;
use pocketmine\network\mcpe\protocol\DataPacket;
use raklib\protocol\AcknowledgePacket;
use raklib\protocol\EncapsulatedPacket;
use raklib\protocol\OfflineMessage;

abstract class StringLogger extends RakNetLogger{

	public function logAcknowledgement(AcknowledgePacket $packet, bool $serverSide) : void {
		$this->log("Received acknowledgement message '" . Utils::getShortClassName($packet) . "' ({$packet::$ID}) from " . ($serverSide ? "server" : "client"), RakNetFilter::TYPE_ACK);
	}

	public function logMinecraft(DataPacket $pk, bool $serverSide) : void {
		$this->log("Received minecraft packet " . Utils::getShortClassName($pk) . " from " . ($serverSide ? "server" : "client"), RakNetFilter::TYPE_MINECRAFT);
	}

	public function logUnknownMinecraft(EncapsulatedPacket $packet, bool $serverSide) : void {
		$this->log("Received unknown minecraft packet from " . ($serverSide ? "server" : "client") . ": " . bin2hex($packet->buffer), RakNetFilter::TYPE_UNKNOWN_MINECRAFT);
	}

	public function logOffline(OfflineMessage $message, bool $serverSide) : void {
		$this->log("Received offline message '" . Utils::getShortClassName($message) . "' ({$message::$ID}) from " . ($serverSide ? "server" : "client"), RakNetFilter::TYPE_OFFLINE);
	}

	public function logConnectedOffline(OfflineMessage $message, bool $serverSide) : void {
		$this->log("Received offline message '" . Utils::getShortClassName($message) . "' ({$message::$ID}) from " . ($serverSide ? "server" : "client") . " when session is already open", RakNetFilter::TYPE_CONNECTED_OFFLINE);
	}

	public function logOfflineConnected(string $buffer, bool $serverSide) : void {
		$this->log("Received connected message from " . ($serverSide ? "server" : "client") . " when session is not open: " . bin2hex($buffer), RakNetFilter::TYPE_OFFLINE_CONNECTED);
	}

	public function logUnknownOffline(string $buffer, bool $serverSide) : void {
		$this->log("Received unknown offline message from " . ($serverSide ? "server" : "client") . " when session is already open: " . bin2hex($buffer), RakNetFilter::TYPE_UNKNOWN_OFFLINE);
	}

	public function logRaw(string $buffer, bool $serverSide) : void {
		$this->log("Received raw message from " . ($serverSide ? "server" : "client") . ": " . bin2hex($buffer), RakNetFilter::TYPE_RAW);
	}

}