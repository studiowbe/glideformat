<?php

namespace Studiow\GlideFormat;

use Mockery;

class GlideFormatServerTest extends \PHPUnit_Framework_TestCase
{

    private $server;

    public function setUp()
    {

        $this->server = GlideFormatServer::createServer([
                    "source" => Mockery::mock('League\Flysystem\FilesystemInterface'),
                    "cache" => Mockery::mock('League\Flysystem\FilesystemInterface')
        ]);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf("\\Studiow\\GlideFormat\\GlideFormatServer", $this->server);
    }

    public function testCreateGlideInstance()
    {
        $this->assertInstanceOf("\\League\\Glide\\Server", $this->server->getGlideServer());
    }

    public function testAddRemovePreset()
    {
        $preset_name = "thumbnail";
        $preset_settings = ["w" => 100, "h" => 150, "fit" => "crop"];
        $this->server->addPreset($preset_name, $preset_settings);
        $this->assertEquals(true, $this->server->hasPreset("thumbnail", false));
        $this->assertArrayHasKey("w", $this->server->getPreset("thumbnail"));
        $this->assertArrayHasKey("h", $this->server->getPreset("thumbnail"));
        $this->assertArrayHasKey("fit", $this->server->getPreset("thumbnail"));


        $this->server->removePreset($preset_name);
        $this->assertEquals(false, $this->server->hasPreset("thumbnail", false));
    }

    public function testMissingPreset()
    {
        $this->assertEquals(false, $this->server->hasPreset("non_existing_preset", false));
    }

    /**
     * @expectedException Studiow\GlideFormat\Exception\PresetNotFoundException
     */
    public function testMissingPresetException()
    {

        $this->server->getPreset("non_existing_preset");
    }

    /**
     * @expectedException Studiow\GlideFormat\Exception\PresetExistsException
     */
    public function testExistingPresetException()
    {
        $preset_name = "thumbnail";
        $preset_settings = ["w" => 100, "h" => 150, "fit" => "crop"];
        $this->server->addPreset($preset_name, $preset_settings);
        $this->server->addPreset($preset_name, $preset_settings);
    }

}
