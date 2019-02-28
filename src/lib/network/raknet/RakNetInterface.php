<?php

/**
 * RakNetInterface.php – MinecraftPacketDebugger
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

use jacknoordhuis\minecraftpacketdebugger\lib\MinecraftPacketDebugger;
use pocketmine\network\mcpe\protocol\PacketPool as MinecraftPacketPool;
use raklib\protocol\ACK;
use raklib\protocol\Datagram;
use raklib\protocol\EncapsulatedPacket;
use raklib\protocol\NACK;
use raklib\protocol\OfflineMessage;
use raklib\protocol\OpenConnectionReply2;
use raklib\protocol\OpenConnectionRequest1;
use raklib\protocol\UnconnectedPing;
use raklib\server\UDPServerSocket;
use raklib\utils\InternetAddress;
use function bin2hex;
use function ord;
use const PHP_EOL;

class RakNetInterface {

	/** @var \jacknoordhuis\minecraftpacketdebugger\lib\MinecraftPacketDebugger */
	private $server;

	/** @var \raklib\server\UDPServerSocket */
	private $socket;

	/** @var InternetAddress */
	private $clientAddress = null;

	/** @var \jacknoordhuis\minecraftpacketdebugger\lib\network\raknet\RakNetLogger */
	private $logger;

	/** @var bool */
	private $sessionCreated = false;

	/** @var int */
	private $maxPerTick = 200;

	/**
	 * @param \jacknoordhuis\minecraftpacketdebugger\lib\MinecraftPacketDebugger $server
	 * @param int                                                                $maxPerTick Maximum number of packets to process per tick.
	 */
	public function __construct(MinecraftPacketDebugger $server, RakNetLogger $logger, int $maxPerTick = 200) {
		$this->server = $server;
		$this->logger = $logger;
		$this->maxPerTick = $maxPerTick;

		$this->socket = new UDPServerSocket($server->getBindAddress());
	}

	/**
	 * Read all the incoming packets from the socket.
	 */
	public function tick() : void {
		$processed = 0;

		while($processed <= $this->maxPerTick) {
			if($this->socket->readPacket($buffer, $source, $port) === false) {
				break; //exit loop, we didn't receive any packets
			}

			$this->updateSession($buffer, $address = new InternetAddress($source, $port, 4));
			$this->handlePacket($buffer, $address);
			$this->forwardPacket($buffer, $address);
		}
	}

	/**
	 * Update the connected clients address and check if a session has been opened.
	 *
	 * @param string                        $buffer
	 * @param \raklib\utils\InternetAddress $address
	 */
	private function updateSession(string $buffer, InternetAddress $address) : void {
		if(!$this->sessionCreated) { //we only want offline messages
			if(($pk = PacketPool::getPacket($buffer)) instanceof OfflineMessage) {
				if($pk instanceof UnconnectedPing or $pk instanceof OpenConnectionRequest1) { //update the current clients address
					$this->clientAddress = $address;
				} elseif($pk instanceof OpenConnectionReply2) { //the server accepted the clients connection
					$this->sessionCreated = true;
				}
			}
		}
	}

	/**
	 * Handle the raw buffer and perform all of our logging.
	 *
	 * @param string                        $buffer
	 * @param \raklib\utils\InternetAddress $address
	 */
	private function handlePacket(string $buffer, InternetAddress $address) : void {
		$pid = ord($buffer{0});
		$fromServer = $this->server->getServerAddress()->equals($address);

		if($this->sessionCreated) {
			if(($pid & Datagram::BITFLAG_VALID) !== 0) {
				if($pid & Datagram::BITFLAG_ACK) {
					$this->logger->logAcknowledgement(new ACK($buffer), $fromServer);
				} elseif($pid & Datagram::BITFLAG_NAK) {
					$this->logger->logAcknowledgement(new NACK($buffer), $fromServer);
				} else {
					$this->handleDatagram(new Datagram($buffer), $fromServer);
				}
			} else {
				if(($pk = PacketPool::getPacket($buffer)) instanceof OfflineMessage) {
					/** @var OfflineMessage $pk */
					$this->logger->logConnectedOffline($pk, $fromServer);
				} else {
					$this->logger->logUnknownOffline($buffer, $fromServer);
				}
			}
		} elseif(($pk = PacketPool::getPacket($buffer)) instanceof OfflineMessage) {
			/** @var OfflineMessage $pk */
			do {
				try {
					$pk->decode();
					if(!$pk->isValid()) {
						throw new \InvalidArgumentException("Packet magic is invalid");
					}
				} catch(\Throwable $e) {
					echo "Received garbage offline message from $address ({$e->getMessage()}): " . bin2hex($pk->buffer) . PHP_EOL;
					break;
				}

				$this->logger->logOffline($pk, $fromServer);
			} while(false);
		} elseif(($pid & Datagram::BITFLAG_VALID) !== 0 and ($pid & 0x03) === 0) {
			// Loose datagram, don't relay it as a raw packet
			// RakNet does not currently use the 0x02 or 0x01 bitflags on any datagram header, so we can use
			// this to identify the difference between loose datagrams and packets like Query.
			$this->logger->logOfflineConnected($buffer, $fromServer);
		} else {
			$this->logger->logRaw($buffer, $fromServer);
		}
	}

	private function handleDatagram(Datagram $datagram, bool $serverSide) : void {
		$datagram->decode();
		foreach($datagram->packets as $pk) {
			$this->handleEncapsulatedPacket($pk, $serverSide);
		}
	}

	private function handleEncapsulatedPacket(EncapsulatedPacket $packet, bool &$serverSide) : void {
		try {
			if($packet->buffer === "") {
				return;
			}

			$this->logger->logMinecraft(MinecraftPacketPool::getPacket($packet->buffer), $serverSide);
		} catch(\Throwable $e) {
			$this->logger->logUnknownMinecraft($packet, $serverSide);
		}
	}

	/**
	 * Forward the raw buffer to it's real destination.
	 *
	 * @param string                        $buffer
	 * @param \raklib\utils\InternetAddress $from
	 */
	private function forwardPacket(string $buffer, InternetAddress $from) : void {
		if($from->equals($this->server->getServerAddress())) {
			$this->sendToClient($buffer);
		} else {
			$this->sendToServer($buffer);
		}
	}

	private function sendToClient(string $buffer) : void {
		$this->socket->writePacket($buffer, $this->clientAddress->ip, $this->clientAddress->port);
	}

	private function sendToServer(string $buffer) : void {
		$address = $this->server->getServerAddress();
		$this->socket->writePacket($buffer, $address->ip, $address->port);
	}

}