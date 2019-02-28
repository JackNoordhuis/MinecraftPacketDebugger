MinecraftPacketDebugger
===============
_PocketMine-MP based proxy for debugging minecraft network communication._

### Usage
Currently there is only a simple script to start a debugging server and it will log RakNet packets to the command line.
You must provide the ip and port for the target server (PocketMine, Nukkit, BDS, etc) and the ip and port for the debug
proxy to bind to:

```bash
$ php debugger --server 127.0.0.1:19132 --bind 0.0.0.0:19130
```

You will only be able to connect to servers with port checking disabled, so connecting to most external servers will not
work. This proxy is designed to debug local servers (or your own personal ones) so support for port checked servers is
not planned but feel free to add it yourself if you'd like.

__The content of this repo is licensed under the GNU Lesser General Public License v3. A full copy of the license is
available [here](LICENSE).__