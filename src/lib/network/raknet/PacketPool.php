<?php

/**
 * PacketPool.php – MinecraftPacketDebugger
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

use raklib\protocol\{ACK, AdvertiseSystem, ConnectedPing, ConnectedPong, ConnectionRequest, ConnectionRequestAccepted, DisconnectionNotification, IncompatibleProtocolVersion, NACK, NewIncomingConnection, OpenConnectionReply1, OpenConnectionReply2, OpenConnectionRequest1, OpenConnectionRequest2, Packet, UnconnectedPing, UnconnectedPingOpenConnections, UnconnectedPong};

class PacketPool {

	/** @var \SplFixedArray<\raklib\protocol\Packet> */
	protected static $pool = null;

	/**
	 * Static constructor, called automatically the first time we reference the class.
	 */
	private static function PacketPool() : void {
		self::registerPacket(new ACK());
		self::registerPacket(new AdvertiseSystem());
		self::registerPacket(new ConnectedPing());
		self::registerPacket(new ConnectedPong());
		self::registerPacket(new ConnectionRequest());
		self::registerPacket(new ConnectionRequestAccepted());
		self::registerPacket(new DisconnectionNotification());
		self::registerPacket(new IncompatibleProtocolVersion());
		self::registerPacket(new NACK());
		self::registerPacket(new NewIncomingConnection());
		self::registerPacket(new OpenConnectionReply1());
		self::registerPacket(new OpenConnectionReply2());
		self::registerPacket(new OpenConnectionRequest1());
		self::registerPacket(new OpenConnectionRequest2());
		self::registerPacket(new UnconnectedPing());
		self::registerPacket(new UnconnectedPingOpenConnections());
		self::registerPacket(new UnconnectedPong());
	}

	/**
	 * @param string $buffer
	 *
	 * @return \raklib\protocol\Packet|null
	 */
	public static function getPacket(string $buffer) : ?Packet {
		if(($pk = static::getPacketById(ord($buffer{0}))) === null) {
			return null;
		}

		$pk->setBuffer($buffer);

		return $pk;
	}

	/**
	 * @param int $pid
	 *
	 * @return \raklib\protocol\Packet
	 */
	public static function getPacketById(int $pid) : ?Packet {
		return isset(static::$pool[$pid]) ? clone static::$pool[$pid] : null;
	}

	/**
	 * @param \raklib\protocol\Packet $packet
	 */
	public static function registerPacket(Packet $packet) {
		static::$pool[$packet::$ID] = clone $packet;
	}
}