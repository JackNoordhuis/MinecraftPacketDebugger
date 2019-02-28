<?php

/**
 * StartSimpleDebugProxy.php – MinecraftPacketDebugger
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

namespace jacknoordhuis\minecraftpacketdebugger\cli\commands;

use function explode;
use InvalidArgumentException;
use jacknoordhuis\minecraftpacketdebugger\cli\loggers\EchoLogger;
use jacknoordhuis\minecraftpacketdebugger\cli\loggers\FileLogger;
use jacknoordhuis\minecraftpacketdebugger\lib\MinecraftPacketDebugger;
use jacknoordhuis\minecraftpacketdebugger\lib\network\raknet\RakNetLogger;
use jacknoordhuis\minecraftpacketdebugger\lib\utils\Utils;
use raklib\utils\InternetAddress;
use RuntimeException;
use function str_repeat;
use function strtolower;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Starts a basic debug proxy server.
 */
class StartSimpleDebugProxy extends Command {

	protected function configure() {
		$this->setName("simple")
			->setDescription("Starts a basic debug proxy server.")
			->addOption("server", "s", InputOption::VALUE_REQUIRED, "The server that client packets will be forwarded to in the format 'ip:port' (192.168.0.6:19132).")
			->addOption("bind", "b", InputOption::VALUE_REQUIRED, "The address to start the proxy server on, in the format 'ip:port' (127.0.0.1:19132).")
			->addOption("logger", "l", InputOption::VALUE_REQUIRED, "Set the logger type (file, echo)", "echo")
			->addOption("log-file", "f", InputOption::VALUE_REQUIRED, "Set the file loggers log file.");
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$io = new SymfonyStyle($input, $output);

		$io->writeln("");

		$server = $bind = null;
		foreach(
			[
			"server",
			"bind"
			] as $option) {
			if(($error = $this->createAddress($input, $option, $address)) !== null) {
				$io->error($error);
				$io->writeln("<info>Usage: php debugger simple --bind x.x.x.x:port --server x.x.x.x:port</>");
				return;
			}

			$$option = $address;
		}

		if(($logger = $this->createLogger($input, $io)) === null) {
			return;
		}

		$io->writeln(str_repeat("-", 70));
		$io->writeln( "<info>Starting minecraft bedrock network debug proxy on {$bind->ip}:{$bind->port}...</>");
		$io->writeln("");

		(new MinecraftPacketDebugger($server, $bind))
			->start($logger);
	}

	/**
	 * Create an internet address from the ip:port format.
	 *
	 * @param \Symfony\Component\Console\Input\InputInterface $input
	 * @param string                                          $option
	 * @param \raklib\utils\InternetAddress|null              $output
	 *
	 * @return string|null
	 */
	private function createAddress(InputInterface $input, string $option, ?InternetAddress &$output) : ?string {
		if(($raw = $input->getOption($option)) === null) {
			return "Option '$option' must be provided.";
		}

		$parts = explode(":", $raw);

		if(count($parts) !== 2) {
			return "The $option parameter must be in the format ip:port, $raw given.";
		}

		try {
			$output = Utils::addressFromHostname($parts[0], (int) $parts[1]);
		} catch(InvalidArgumentException $e) {
			return "A port must be in the range of 0 to 65536, {$parts[1]} given.";
		}

		return null;
	}

	private function createLogger(InputInterface $input, SymfonyStyle $io) : ?RakNetLogger {
		$raw = $input->getOption("logger");

		switch(strtolower($raw)) {
			case "echo":
			case "e":
				$io->writeln("<comment>Using echo logger.</>");
				return new EchoLogger;
			case "file":
			case "f":
				$log_file = $input->getOption("log-file");
				if($log_file === null) {
					$io->error("A log file must be specified with the 'log-file' option when using the file logger.");
					$io->writeln("<info>Usage: php debugger simple --bind x.x.x.x:port --server x.x.x.x:port --logger file --log-file path/to/a/file.txt</>");
					return null;
				}

				$logger = null;
				try {
					$logger = new FileLogger($log_file);
					$io->writeln("<comment>Using file logger ($log_file).</>");
				} catch(RuntimeException $e) {
					$io->error($e->getMessage());
				}
				return $logger;
			default:
				$io->error("Unknown logger type specified '$raw'.");
				return null;
		}
	}

}