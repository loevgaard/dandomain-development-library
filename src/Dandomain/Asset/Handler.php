<?php
namespace Dandomain\Asset;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\AssetInterface;
use Assetic\Asset\FileAsset;
use Assetic\Asset\GlobAsset;
use Assetic\Asset\HttpAsset;
use Assetic\AssetWriter;
use Assetic\Filter\FilterInterface;

class Handler {
    protected $config;

    public function __construct(array $config) {
        $this->config = $config;
    }

    public function handleChange($file) {
        echo "Handle change in $file\n";

        foreach($this->config['assets']['resources'] as $resource) {
            $assetWriter = new AssetWriter(dirname($resource['target']));
            $assetCollection = new AssetCollection();

            foreach ($resource['files'] as $file) {
                $asset = $this->getAssetObjectFromFilename($file);
                $assetCollection->add($asset);
            }

            $assetCollection->setTargetPath(basename($resource['target']));
            if(isset($resource['filters'])) {
                foreach ($resource['filters'] as $filter) {
                    $filterConfig = $this->getFilterConfigFromName($filter);

                    if (!$filterConfig) {
                        throw new \RuntimeException("Filter '$filter' does not exist in the config");
                    }

                    $filterObj = $this->getFilterObjectFromConfig($filterConfig);
                    $assetCollection->ensureFilter($filterObj);
                }
            }
            $assetWriter->writeAsset($assetCollection);

            echo $resource['target'] . " created\n";
        }
    }

    /**
     * Returns an asset object based on the filename
     *
     * @param string $filename
     * @return AssetInterface
     */
    public function getAssetObjectFromFilename($filename) {
        if(strpos($filename, '//') !== false) {
            return new HttpAsset($filename);
        } elseif(substr($filename, -1) == '*') {
            return new GlobAsset($filename);
        } else {
            return new FileAsset($filename);
        }
    }

    protected function getFilterConfigFromName($name) {
        foreach($this->config['assetic']['filters'] as $filterName => $filterConfig) {
            if($name == $filterName) {
                return $filterConfig;
            }
        }

        return null;
    }

    /**
     * @param $filterConfig
     * @return FilterInterface
     */
    protected function getFilterObjectFromConfig($filterConfig) {
        $obj = new \ReflectionClass($filterConfig['class']);
        return $obj->newInstanceArgs($filterConfig['arguments']);
    }

    /**
     * This method is for future releases where we will only process assets that has been changed
     *
     * @param $file
     * @return array|null
     */
    protected function getResourceConfigFromFile($file) {
        if(!isset($this->config['assets']['resources'])) {
            throw new \RuntimeException('No assets.resources key in config');
        }

        foreach($this->config['assets']['resources'] as $resource) {
            foreach($resource['files'] as $resourceFile) {
                if($resourceFile == $file) {
                    return $resource;
                }
            }
        }

        return null;
    }
}