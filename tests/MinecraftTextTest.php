<?php

use \App\Server;

class MinecraftTextTest extends TestCase
{

    public function testLegacyConvert()
    {
        $json = ["text" => "Hello World"];
        $server = new Server();
        $server->motd = $json;
        $this->assertEquals("Hello World", $server->motd);
    }

    public function testLegacyConvertColor()
    {
        $json = ["text" => "Hello World", "color" => "red"];
        $server = new Server();
        $server->motd = $json;
        $this->assertEquals("§cHello World", $server->motd);
    }

    public function testLegacyConvertFormatting()
    {
        $json = [
            "text" => "Hello World",
            "strikethrough" => true,
            "obfuscated" => true,
            "underlined" => true,
            "italic" => true,
            "bold" => true];
        $server = new Server();
        $server->motd = $json;

        $this->assertTrue(str_contains($server->motd, "Hello World"));
        $this->assertTrue(str_contains($server->motd, "§k"));
        $this->assertTrue(str_contains($server->motd, "§l"));
        $this->assertTrue(str_contains($server->motd, "§m"));
        $this->assertTrue(str_contains($server->motd, "§n"));
        $this->assertTrue(str_contains($server->motd, "§o"));
    }

    public function testLegacyConvertMultiple()
    {
        $first_component = ["text" => "first "];
        $second_component = ["text" => "second ", "color" => "red", ""];
        $third_component = ["text" => "third ", "strikethrough" => true];
        $json = ["extra" => [$first_component, $second_component, $third_component]];
        $server = new Server();
        $server->motd = $json;
        $this->assertEquals("first §r§csecond §r§mthird §r", $server->motd);
    }

    public function testStripColor()
    {
        $server = new Server();
        $server->motd = "§cHello World §aTest";
        $this->assertEquals("Hello World Test", $server->plain_motd);
    }

    public function testStripColorNoColor()
    {
        $server = new Server();
        //§g is no used color code so it should be ignored
        $text = "§gHello";
        $server->motd = $text;
        $this->assertEquals($text, $server->plain_motd);
    }
}
