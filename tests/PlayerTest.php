<?php

use \App\Player;

class PlayerTest extends TestCase
{
    public function testOfflineUUID()
    {
        $player = new Player;
        $player->name = "Notch";

        //based on the java implementation
        //UUID.nameUUIDFromBytes(("OfflinePlayer:" + name).getBytes(Charsets.UTF_8))
        $this->assertEquals("b50ad385829d3141a2167e7d7539ba7f", $player->offline_uuid);
    }
}
