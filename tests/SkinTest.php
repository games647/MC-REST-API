<?php

use \App\Skin;
use \Mockery\Mock;

class SkinTest extends TestCase
{

    public function testEncodedSlimModel()
    {
        //just no cape
        $skin = new Skin();
        $skin->timestamp = 1466186939353;
        $skin->profile_id = "0aaa2c13922a411bb6559b8c08404695";
        $skin->profile_name = "games647";
        $skin->slim_model = 1;
        $skin->skin_url = "http://textures.minecraft.net/texture/"
                . "a2e6a3f8caea7913ab48237beea6d6a1a6f76936e3b71af4c7a08bb61c7870";

        $expected = "eyJ0aW1lc3RhbXAiOjE0NjYxODY5MzkzNTMsInByb2ZpbGVJZCI6IjBhYWEyYzEzOTIyYTQxMWJiNjU1OWI4YzA4NDA0Njk1Ii"
                . "wicHJvZmlsZU5hbWUiOiJnYW1lczY0NyIsInNpZ25hdHVyZVJlcXVpcmVkIjp0cnVlLCJ0ZXh0dXJlcyI6eyJTS0lOIjp7Im1ldG"
                . "FkYXRhIjp7Im1vZGVsIjoic2xpbSJ9LCJ1cmwiOiJodHRwOi8vdGV4dHVyZXMubWluZWNyYWZ0Lm5ldC90ZXh0dXJlL2EyZTZhM2"
                . "Y4Y2FlYTc5MTNhYjQ4MjM3YmVlYTZkNmExYTZmNzY5MzZlM2I3MWFmNGM3YTA4YmI2MWM3ODcwIn19fQ==";
        $this->assertEquals($expected, $skin->encoded_data);
    }

    public function testEncodedNoCape()
    {
        //just no cape
        $skin = new Skin();
        $skin->timestamp = 1466187347608;
        $skin->profile_id = "485957ec732a454587acfc9ccf086cf2";
        $skin->profile_name = "sgdc3";
        $skin->skin_url = "http://textures.minecraft.net/texture/"
                . "ecdc5f0acf41382555abab46aad5a50e20df8b373e484863d8e8b744dff55";

        $expected = "eyJ0aW1lc3RhbXAiOjE0NjYxODczNDc2MDgsInByb2ZpbGVJZCI6IjQ4NTk1N2VjNzMyYTQ1NDU4N2FjZmM5Y2NmMDg2Y2YyIi"
                . "wicHJvZmlsZU5hbWUiOiJzZ2RjMyIsInNpZ25hdHVyZVJlcXVpcmVkIjp0cnVlLCJ0ZXh0dXJlcyI6eyJTS0lOIjp7InVybCI6Im"
                . "h0dHA6Ly90ZXh0dXJlcy5taW5lY3JhZnQubmV0L3RleHR1cmUvZWNkYzVmMGFjZjQxMzgyNTU1YWJhYjQ2YWFkNWE1MGUyMGRmOG"
                . "IzNzNlNDg0ODYzZDhlOGI3NDRkZmY1NSJ9fX0=";
        $this->assertEquals($expected, $skin->encoded_data);
    }

    public function testEncodedNoSkin()
    {
        //no texture data at all
        $skin = new Skin();
        $skin->timestamp = 1466186141580;
        $skin->profile_id = "c06f89064c8a49119c29ea1dbd1aab82";
        $skin->profile_name = "MHF_Steve";

        $expected = "eyJ0aW1lc3RhbXAiOjE0NjYxODYxNDE1ODAsInByb2ZpbGVJZCI6ImMwNmY4OTA2NGM4YTQ5MTE5YzI5ZWExZGJkMWFhYjgyIi"
                . "wicHJvZmlsZU5hbWUiOiJNSEZfU3RldmUiLCJzaWduYXR1cmVSZXF1aXJlZCI6dHJ1ZSwidGV4dHVyZXMiOnt9fQ==";
        $this->assertEquals($expected, $skin->encoded_data);
    }

    public function testSignatureEncode() {
        $skin = new Skin();

        $signature = "IB3ku+sgFE+KmFQJyihjFSH+N1tPZgonXVEbTrvkIrA618ZJu2xu+jD29xHQQGAYyv236IclU1gpZ+5OywVLHa81Y00okdyId"
            . "4y8kZyW5WWO0Jmm/93bAvElsqUXDTc2RFTfU0WJsahxRUciePw+JeewcgkFOV+kISDXvkvSHc6eQqT2uOhkEgmwugigo1DAJ9MF1"
            . "2Oqk1AG+nUk/9ExTawzmP/a3ob0T9mypn5rc5Lkj+kYICRwk0y5jd8x0SQoCmAAfQGeC60lAZg7NqfU80SVEeU89H8NKelXAfxYn"
            . "Ev8T9yN+f3f1jkQ456SGIqh211ub9XanYIdxrE0SkncaEl+OkeQ1rQQH6ZcdIf4lHwq++ouX+6/W90GsYuj21TJ0KMViCTI1GBVH"
            . "znHmg1T9Rn3WVgwf2J/hBONdEpOhk0Rf6v8oiZQ1rvsmjvbOiSUm3TX5xdY1ZkmxXAu+6jaAEEiWHWmlJYSx3kb1/fLegbaQyp/x"
            . "+AletC8emg72LDc6Zdy4qfqVonr9JGIyftpJNWcQboB0fZNWraj6Nluhwbslwzdoz7dowOBwYioJ233M9ltwphKma3Hex1Lgx9f/"
            . "6vPIBWD3EZv1kNcLcdyvEQBmKl/liHeVswWdTD6e2yBLUQQHVsRp6/0QIYTcdEzXQzMRvaxTIBxB22qKBv0GHg=";
        $skin->signature = base64_decode($signature);
        $this->assertEquals($signature, $skin->encoded_signature);
    }

    public function testValidSignature()
    {
        $skin = new Skin();
        $skin->timestamp = 1466188017540;
        $skin->profile_id = "c06f89064c8a49119c29ea1dbd1aab82";
        $skin->profile_name = "MHF_Steve";

        $signature = "Q4VE47NsrZfzpXzDAMlTHQlrZeEiBDstlq1DraPGJr4fUEBK3E2/YRck/lmAWGTaPt4Wxt0jaCrS9GEFw9j2ZrGEuirBqMGhc"
                . "zaMVI7XcQoOyXvduCEXL6ZXHO8KQIVrujBKRnmY3oGniPABVhwJDeLBJsFxIZno9w6XpmjYnT1G3ZJNEXoMNmYSqxUcOidLAqg8A"
                . "h/JAyYrcGORdv83dwmLxduvfPK/wq4YUgsDRCrFZKFkTsAGmXlXcEca5QHcmg0m0/VFGH2PYU8AFTRmr4JVJQOhuTZYFuRoF9dXL"
                . "4bKqSuhIDUl6X3ZvArmZvHUPzuqK2ykGESc+dYL7bp25gT5/zw4+a2s0d1V4pbX+n0jvoDdKmvNIuDMw41Pmsr+DVyijr/AzaxCi"
                . "3iTeko4MBVm0C7e80Vw47lj6J3S2rTAKZXGPJfgLiXDegVFXwhu/TOYIMGFN/Do9pwjQuZHqNpvJ0zs91zHQvNafs6wYfeE/Db3I"
                . "BCbVMSUKbC5LYctVqpRjJdM7tDT9KjTassJ3YnDXkjkRmZ2DCHnHMk4URXdpynVlQSlc8dD1DSZWKRLQt32Za3HXKVzMCFgFcpQX"
                . "xGvA5ZpmgViuVTf87c2LSKT5BU2GuZ9avUiaMRHNsIsJCzON3RleLNO/yoDjUWJuvUEle7QQR0TX+gKjplWoz0=";

        $decoded_signature = base64_decode($signature);
        $skin->signature = $decoded_signature;

        $this->assertEquals(1, $skin->isSignatureValid());
    }
}
