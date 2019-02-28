<?php

/**
 * MinecraftPacketDebugger.php – MinecraftPacketDebugger
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

namespace jacknoordhuis\minecraftpacketdebugger\lib;

use jacknoordhuis\minecraftpacketdebugger\lib\network\raknet\RakNetInterface;
use jacknoordhuis\minecraftpacketdebugger\lib\network\raknet\RakNetLogger;
use pocketmine\network\mcpe\protocol\PacketPool as MinecraftPacketPool;
use pocketmine\snooze\SleeperHandler;
use raklib\utils\InternetAddress;
use function microtime;

class MinecraftPacketDebugger {

	/** @var \raklib\utils\InternetAddress */
	private $serverAddress;
	/** @var \raklib\utils\InternetAddress */
	private $bindAddress;
	/** @var \pocketmine\snooze\SleeperHandler */
	private $tickSleeper;
	/** @var \jacknoordhuis\minecraftpacketdebugger\lib\network\raknet\RakNetInterface */
	private $interface;
	/** @var bool */
	private $shutdown = false;
	private const TPS = 100;
	private const TIME_PER_TICK = 1 / self::TPS;
	private const HALF_TICK_TIME = self::TIME_PER_TICK / 2;

	public function __construct(InternetAddress $server, InternetAddress $bind) {
		MinecraftPacketPool::init();

		$this->tickSleeper = new SleeperHandler();

		$this->serverAddress = $server;
		$this->bindAddress = $bind;
	}

	/**
	 * @return \raklib\utils\InternetAddress
	 */
	public function getServerAddress() : InternetAddress {
		return clone $this->serverAddress;
	}

	/**
	 * @return \raklib\utils\InternetAddress
	 */
	public function getBindAddress() : InternetAddress {
		return clone $this->bindAddress;
	}

	/**
	 * Tell the server to start processing packets.
	 *
	 * @param \jacknoordhuis\minecraftpacketdebugger\lib\network\raknet\RakNetLogger $logger
	 */
	public function start(RakNetLogger $logger) : void {
		$this->interface = new RakNetInterface($this, $logger);

		$this->run();
	}

	/**
	 * Start the server ticking mechanism, will run until shutdown is called.
	 */
	private function run() : void {
		$nextTick = microtime(true);

		while(!$this->shutdown) {
			$tickTime = microtime(true);
			if(($tickTime - $nextTick) < -self::HALF_TICK_TIME) { //Allow half a tick of diff
				return;
			}

			$this->tick();

			if(($nextTick - $tickTime) < -1) {
				$nextTick = $tickTime;
			} else {
				$nextTick += self::TIME_PER_TICK;
			}

			$this->tickSleeper->sleepUntil($nextTick); //Sleeps are self-correcting - if we undersleep 1ms on this tick, we'll sleep an extra ms on the next tick
		}
	}

	/**
	 * Do all the things that must be ticked!
	 */
	protected function tick() : void {
		$this->interface->tick();
	}

	/**
	 * Mark the packet debugger instance as shutdown.
	 */
	public function shutdown() : void {
		$this->shutdown = true;
	}

}