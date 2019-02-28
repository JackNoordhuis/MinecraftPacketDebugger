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

use jacknoordhuis\minecraftpacketdebugger\lib\network\NetworkFilter;
use jacknoordhuis\minecraftpacketdebugger\lib\network\NetworkLogger;
use pocketmine\network\mcpe\protocol\DataPacket;
use raklib\protocol\AcknowledgePacket;
use raklib\protocol\EncapsulatedPacket;
use raklib\protocol\OfflineMessage;

abstract class RakNetLogger extends NetworkLogger {

	/** @var \jacknoordhuis\minecraftpacketdebugger\lib\network\raknet\RakNetFilter */
	protected $filter;

	/**
	 * Called when we receive an ACK or NACK.
	 *
	 * @param \raklib\protocol\AcknowledgePacket $packet
	 * @param bool                               $serverSide
	 */
	abstract public function logAcknowledgement(AcknowledgePacket $packet, bool $serverSide) : void;

	/**
	 * Called when we receive an encapsulated packet from a datagram.
	 *
	 * @param \pocketmine\network\mcpe\protocol\DataPacket $pk
	 * @param bool                                         $serverSide
	 */
	abstract public function logMinecraft(DataPacket $pk, bool $serverSide) : void;

	/**
	 * Called when we receive an unknown encapsulated packet from a datagram.
	 *
	 * @param \raklib\protocol\EncapsulatedPacket $packet
	 * @param bool                                $serverSide
	 */
	abstract public function logUnknownMinecraft(EncapsulatedPacket $packet, bool $serverSide) : void;

	/**
	 * Called when we receive an offline message.
	 *
	 * @param \raklib\protocol\OfflineMessage $message
	 * @param bool                            $serverSide
	 */
	abstract public function logOffline(OfflineMessage $message, bool $serverSide) : void;

	/**
	 * Called when we receive an offline message from an open session.
	 *
	 * @param \raklib\protocol\OfflineMessage $message
	 * @param bool                            $serverSide
	 */
	abstract public function logConnectedOffline(OfflineMessage $message, bool $serverSide) : void;

	/**
	 * Called when we receive a connected datagram without an open session.
	 *
	 * @param string $buffer
	 * @param bool   $serverSide
	 */
	abstract public function logOfflineConnected(string $buffer, bool $serverSide) : void;

	/**
	 * Called when we receive an unknown offline message.
	 *
	 * @param string $buffer
	 * @param bool   $serverSide
	 */
	abstract public function logUnknownOffline(string $buffer, bool $serverSide) : void;

	/**
	 * Called when we receive a raw message (status query).
	 *
	 * @param string $buffer
	 * @param bool   $serverSide
	 */
	abstract public function logRaw(string $buffer, bool $serverSide) : void;

	/**
	 * @return \jacknoordhuis\minecraftpacketdebugger\lib\network\raknet\RakNetFilter|\jacknoordhuis\minecraftpacketdebugger\lib\network\NetworkFilter
	 */
	public function getFilter() : NetworkFilter {
		return $this->filter;
	}

	/**
	 * @inheritdoc
	 */
	public function getInterfaceName() : string {
		return "RakNet";
	}

}