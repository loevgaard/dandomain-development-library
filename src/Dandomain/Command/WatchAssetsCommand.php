<?php
namespace Dandomain\Command;

use Kwf\FileWatcher\Backend\BackendAbstract;
use Kwf\FileWatcher\Event\Create;
use Kwf\FileWatcher\Event\Modify;
use Kwf\FileWatcher\Watcher;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WatchAssetsCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('dandomain:watch-assets')
            ->setDescription('This command will watch assets and do the necessary actions based on creations, changes etc.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->getConfig();

        $modification = function($e) {
            print_r($this->getConfig());
            echo "Modification\n";
            var_dump($e->filename);
            echo "\n\n";
        };
        $modification->bindTo($this);

        /** @var BackendAbstract $watcher */
        $watcher = Watcher::create($config['assets']['resources']);
        $watcher->addListener(Modify::NAME, $modification);
        $watcher->addListener(Create::NAME, function($e) {
            echo "Creation\n";
            var_dump($e->filename);
            echo "\n\n";
        });
        $watcher->start();
    }
}
