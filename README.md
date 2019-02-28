MinecraftPacketDebugger
===============
_PocketMine-MP based proxy for debugging minecraft network communication._

### About
This a tool designed to debug communication between a minecraft bedrock edition client and a bedrock edition server. It
is not intended to be used as a traditional proxy and most normal servers will not allow you to connect using this tool
([see pitfalls](#pitfalls)).

**This tool was created to assist with debugging and testing a custom built proxy so it may not fulfil your use case without
modification.**

### Installation
There are currently no releases for this tool so you'll have to clone from github and install composer dependencies yourself:
```bash
$ git clone git@github.com:JackNoordhuis/MinecraftPacketDebugger.git && cd MinecraftPacketDebugger && composer install
```

You will most likely run into errors running `composer install` with a normal php binary, you should install and use a
[pre-compiled binary](https://pmmp.readthedocs.io/en/rtfd/links.html#prebuilt-php-binaries-and-related-packages) provided
by @pmmp as it comes with all the extensions required to run PocketMine.

### Usage
Currently there is only a simple script to start a debugging server and it will only log RakNet packets (minecraft level and filters coming soon).
You must provide the ip and port for the target server (PocketMine, Nukkit, BDS, etc) and the ip and port for the debug
proxy to bind to:

```bash
$ php debugger simple --server 127.0.0.1:19132 --bind 0.0.0.0:19130
```

Or using shortcut options:

```bash
$ php debugger simple -s 127.0.0.1:19132 -b 0.0.0.0:19130
```

##### Loggers

You can also specify which logging mechanism to use. Currently only echo (to command line) and file loggers are supported:

```bash
$ php debugger simple -s 127.0.0.1:19132 -b 0.0.0.0:19130 -l file -f ~/MinecraftNetworkDebugger/debug-1.log
```

The default logger is set to echo. If you use the file logger you *must* specify the full path to the log file.

### Pitfalls

__Encryption__

You cannot use this tool to debug an encrypted connection, it is designed for debugging communication over a network you
control, not for poking around someone else's. If you want to debug network traffic with a vanilla BDS server you should
use a mod [like this one](https://github.com/Frago9876543210/PacketStealer/tree/master/DisableEncryption) to disable encryption.

__Port checking__

You will only be able to connect to servers with port checking disabled, so connecting to most external servers will not
work. This proxy is designed to debug local servers (or your own personal ones) so support for port checked servers is
not planned but feel free to add it yourself if you'd like.

#

__The content of this repo is licensed under the GNU Lesser General Public License v3. A full copy of the license is
available [here](LICENSE).__
