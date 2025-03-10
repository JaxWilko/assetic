<?php

namespace Assetic\Asset;

use Assetic\AssetManager;
use Assetic\Contracts\Filter\FilterInterface;
use Assetic\Contracts\Asset\AssetInterface;

/**
 * A reference to an asset in the asset manager.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class AssetReference implements AssetInterface
{
    private $am;
    private $name;
    private $filters = [];
    private $clone = false;
    private $asset;

    public function __construct(AssetManager $am, $name)
    {
        $this->am = $am;
        $this->name = $name;
    }

    public function __clone()
    {
        $this->clone = true;

        if ($this->asset) {
            $this->asset = clone $this->asset;
        }
    }

    public function ensureFilter(FilterInterface $filter)
    {
        $this->filters[] = $filter;
    }

    public function getFilters()
    {
        $this->flushFilters();

        return $this->callAsset(__FUNCTION__);
    }

    public function clearFilters()
    {
        $this->filters = [];
        $this->callAsset(__FUNCTION__);
    }

    public function load(?FilterInterface $additionalFilter = null)
    {
        $this->flushFilters();

        return $this->callAsset(__FUNCTION__, array($additionalFilter));
    }

    public function dump(?FilterInterface $additionalFilter = null)
    {
        $this->flushFilters();

        return $this->callAsset(__FUNCTION__, array($additionalFilter));
    }

    public function getContent()
    {
        return $this->callAsset(__FUNCTION__);
    }

    public function setContent($content)
    {
        $this->callAsset(__FUNCTION__, array($content));
    }

    public function getSourceRoot()
    {
        return $this->callAsset(__FUNCTION__);
    }

    public function getSourcePath()
    {
        return $this->callAsset(__FUNCTION__);
    }

    public function getSourceDirectory()
    {
        return $this->callAsset(__FUNCTION__);
    }

    public function getTargetPath()
    {
        return $this->callAsset(__FUNCTION__);
    }

    public function setTargetPath($targetPath)
    {
        $this->callAsset(__FUNCTION__, array($targetPath));
    }

    public function getLastModified()
    {
        return $this->callAsset(__FUNCTION__);
    }

    public function getVars()
    {
        return $this->callAsset(__FUNCTION__);
    }

    public function getValues()
    {
        return $this->callAsset(__FUNCTION__);
    }

    public function setValues(array $values)
    {
        $this->callAsset(__FUNCTION__, array($values));
    }

    // private

    private function callAsset($method, $arguments = [])
    {
        $asset = $this->resolve();

        return call_user_func_array(array($asset, $method), $arguments);
    }

    private function flushFilters()
    {
        $asset = $this->resolve();

        while ($filter = array_shift($this->filters)) {
            $asset->ensureFilter($filter);
        }
    }

    private function resolve()
    {
        if ($this->asset) {
            return $this->asset;
        }

        $asset = $this->am->get($this->name);

        if ($this->clone) {
            $asset = $this->asset = clone $asset;
        }

        return $asset;
    }
}
