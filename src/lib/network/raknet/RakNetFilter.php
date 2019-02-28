<?php

/**
 * RakNetFilter.php – MinecraftPacketDebugger
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

class RakNetFilter extends NetworkFilter {

	public const TYPE_ACK = 1; //connected message acknowledgements (ACK and NACK)
	public const TYPE_OFFLINE = 2; //offline messages (ping, pong and connection requests + replies)
	public const TYPE_RAW = 3; //raw message (used for status queries)
	public const TYPE_MINECRAFT = 4; //minecraft packet, normally only a batch

	public const TYPE_CONNECTED_OFFLINE = 5; //offline message in open session
	public const TYPE_OFFLINE_CONNECTED = 6; //offline message with no session
	public const TYPE_UNKNOWN_OFFLINE = 7; //unknown offline message
	public const TYPE_UNKNOWN_MINECRAFT = 8; //unknown minecraft packet

	/** @var int */
	public $flags = 0;

	/**
	 * Check if a filter flag is set.
	 *
	 * @param int $flag
	 *
	 * @return bool
	 */
	public function getFlag(int $flag) : bool {
		return ($this->flags & (1 << $flag)) > 0 or $this->flags === 0;
	}

	/**
	 * Set a filter flag.
	 *
	 * @param int  $flag
	 * @param bool $value
	 */
	public function setFlag(int $flag, bool $value) : void {
		if($this->getFlag($flag) !== $value) {
			$this->flags ^= 1 << $flag;
		}
	}

}