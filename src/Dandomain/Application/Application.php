<?php
namespace Dandomain\Application;

use Dandomain\Command\WatchAssetsCommand;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;

class Application extends SymfonyApplication {
    const VERSION = '1.0.0';

    protected $config;

    public function __construct() {
        parent::__construct('Dandomain Development Library', static::VERSION);

        $this->add(new WatchAssetsCommand());
    }

    public function run(InputInterface $input = null, OutputInterface $output = null) {
        if(!$this->config) {
            $this->setConfigFiles(['config/config.yml', 'config/config.local.yml']);
        }
        parent::run($input, $output);
    }

    public function setConfigFiles($files) {
        if(is_string($files)) {
            $files = array($files);
        }
        foreach($files as $file) {
            if (!file_exists($file)) {
                throw new \RuntimeException("$file does not exist");
            }
        }

        $yaml = new Parser();
        $config = [];
        foreach($files as $file) {
            $config = array_merge($config, $yaml->parse(file_get_contents($file)));
        }

        $this->setConfig($config);
    }

    public function setConfig(array $config) {
        echo "Config\n";
        print_r($config);
        $this->config = $config;
    }

    public function getConfig() {
        return $this->config;
    }
}