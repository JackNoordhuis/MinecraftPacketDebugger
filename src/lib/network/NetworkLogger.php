<?php

/**
 * NetworkLogger.php – MinecraftPacketDebugger
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

abstract class NetworkLogger {

	/** @var \jacknoordhuis\minecraftpacketdebugger\lib\network\NetworkFilter */
	protected $filter;

	/**
	 * Get the filter for this logger.
	 *
	 * @return \jacknoordhuis\minecraftpacketdebugger\lib\network\NetworkFilter
	 */
	public function getFilter() : NetworkFilter {
		return $this->filter;
	}

	/**
	 * Retrieve the name/protocol name of the network interface being logged.
	 *
	 * @return string
	 */
	abstract public function getInterfaceName() : string;

	/**
	 * Log the message/data to the output destination.
	 *
	 * @param string $raw
	 * @param int    $filter_flag
	 */
	public function log(string $raw, int $filter_flag) : void {
		if($this->filter->getFlag($filter_flag)) {
			$this->doLog($raw);
		}
	}

	/**
	 * Perform the actual logging (file io, console message, etc).
	 *
	 * @param string $raw
	 */
	abstract protected function doLog(string $raw) : void;

}