<?php

/**
 * NetworkFilter.php – MinecraftPacketDebugger
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

namespace jacknoordhuis\minecraftpacketdebugger\lib\network;

abstract class NetworkFilter {

	public const TYPE_ALL = 0;

	/** @var int */
	public $flags = self::TYPE_ALL;

	/**
	 * Check if a filter flag is set.
	 *
	 * @param int $flag
	 *
	 * @return bool
	 */
	public function getFlag(int $flag) : bool {
		return ($this->flags & (1 << $flag)) > 0 or $this->flags === self::TYPE_ALL;
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