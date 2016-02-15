<?php
namespace Dandomain\Command;

use Dandomain\Application\Application;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

abstract class Command extends SymfonyCommand
{
    /**
     * @return Application
     */
    public function getApplication() {
        return parent::getApplication();
    }

    /**
     * This is short for $this->getApplication()->getConfig()
     *
     * @return array
     */
    public function getConfig() {
        return $this->getApplication()->getConfig();
    }
}
