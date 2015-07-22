<?php

namespace Studiow\GlideFormat;

use League\Glide\Server;
use Studiow\GlideFormat\Exception\PresetExistsException;
use Studiow\GlideFormat\Exception\PresetNotFoundException;

class GlideFormatServer
{

    /**
     * The glide server
     * @var \League\Glide\Server 
     */
    private $server;

    /**
     * The presets
     * @var array 
     */
    private $presets = [];

    /**
     * constructor
     * @param \League\Glide\Server $server
     */
    public function __construct(Server $server)
    {
        $this->setGlideServer($server);
    }

    /**
     * Set the current Glide server
     * @param \League\Glide\Server $server
     * @return \Studiow\GlideFormat\GlideFormatServer
     */
    public function setGlideServer(Server $server)
    {
        $this->server = $server;
        return $this;
    }

    /**
     * Get the current Glide server
     * @return \League\Glide\Server
     */
    public function getGlideServer()
    {
        return $this->server;
    }

    /**
     * Export the presets
     * @return array
     */
    public function getPresets()
    {
        return $this->presets;
    }

    /**
     * Add multiple presets
     * @param array $presets
     * @param type $allowOverride
     * @return \Studiow\GlideFormat\GlideFormatServer
     */
    public function addPresets(array $presets, $allowOverride = false)
    {
        foreach ($presets as $name => $preset) {
            if ($allowOverride) {
                $this->overridePreset($presetName, $preset);
            } else {
                $this->addPreset($presetName, $preset);
            }
        }
        return $this;
    }

    /**
     * Add a new preset, checks if a preset with the same name already exits
     * @param string $presetName
     * @param array $settings
     * @return \Studiow\GlideFormat\GlideFormatServer
     * @throws \Studiow\GlideFormat\Exception\PresetExistsException
     */
    public function addPreset($presetName, array $settings)
    {
        if ($this->hasPreset($presetName, false)) {
            throw new PresetExistsException("Preset {$presetName} already exists");
        }
        $this->presets[$presetName] = $settings;
        return $this;
    }

    /**
     * Check if a preset exists
     * @param string $presetName
     * @param bool $exceptionOnFail
     * @return bool
     * @throws \Studiow\GlideFormat\Exception\ PresetNotFoundException
     */
    public function hasPreset($presetName, $exceptionOnFail = true)
    {
        if (array_key_exists($presetName, $this->presets)) {
            return true;
        }
        if ($exceptionOnFail) {
            throw new PresetNotFoundException("Preset {$presetName} does not exist");
        }
        return false;
    }

    /**
     * Remove a preset
     * @param string $presetName
     * @return \Studiow\GlideFormat\GlideFormatServer
     */
    public function removePreset($presetName, $exceptionOnFail = true)
    {
        $this->hasPreset($presetName, $exceptionOnFail);
        unset($this->presets[$presetName]);
        return $this;
    }

    /**
     * Get a preset
     * @param string $presetName
     * @return array
     */
    public function getPreset($presetName)
    {
        $this->hasPreset($presetName, true);
        return $this->presets[$presetName];
    }

    /**
     * Override an existing preset
     * @param string $presetName
     * @param array $settings
     * @return \Studiow\GlideFormat\GlideFormatServer
     */
    public function overridePreset($presetName, array $settings)
    {
        $this->presets[$presetName] = $settings;
        return $this;
    }

    /**
     * Generate manipulated image.
     * @see \League\Glide\Server::makeImage() 
     * @param string $path
     * @param string $presetName
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function makeImage($path, $presetName)
    {
        $preset = $this->getPreset($presetName);
        return $this->getGlideServer()->makeImage($path, $preset);
    }

    /**
     * Generate and output manipulated image.     
     * @see \League\Glide\Server::outputImage() 
     * @param string $path
     * @param string $presetName
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function outputImage($path, $presetName)
    {
        $preset = $this->getPreset($presetName);
        return $this->getGlideServer()->outputImage($path, $preset);
    }

    /**
     * Generate and return response object of manipulated image.     
     * @see \League\Glide\Server::getImageResponse()
     * @param string $path
     * @param string $presetName
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function getImageResponse($path, $presetName)
    {
        $preset = $this->getPreset($presetName);
        return $this->getGlideServer()->getImageResponse($path, $preset);
    }

    /**
     * Server factory
     * @param array $config
     * @return \Studiow\GlideFormat\GlideFormatServer
     */
    public static function createServer(array $config)
    {
        $server = \League\Glide\ServerFactory::create($config);
        return new GlideFormatServer($server);
    }

}
