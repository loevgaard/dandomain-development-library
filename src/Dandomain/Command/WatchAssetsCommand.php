<?php
namespace Dandomain\Command;

use Dandomain\Asset\Handler;
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
        $handler = new Handler($config);

        $modification = function($e) use($handler) {
            $handler->handleChange($e->filename);
        };
        $modification->bindTo($this);

        $creation = function($e) use($handler) {
            $handler->handleChange($e->filename);
        };
        $creation->bindTo($this);

        /** @var BackendAbstract $watcher */
        $watcher = Watcher::create($config['assets']['watches']);
        $watcher->addListener(Modify::NAME, $modification);
        $watcher->addListener(Create::NAME, $creation);
        $watcher->start();
    }
}
