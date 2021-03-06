<?php

namespace Innova\SelfBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class MaintenanceCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('self:maintenance')
            ->setDescription('Active ou désactive le mode maintenance')
            ->addArgument('enabled', InputArgument::REQUIRED, '0 or 1, depending if you wanna enable or disable it')
            ->addArgument('message', InputArgument::OPTIONAL, 'message you want to be displayed')
           ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $enabled = $input->getArgument('enabled');
        $message = $input->getArgument('message');

        if ($this->getContainer()->get("self.generalparams.manager")->setMaintenance($enabled, $message)) {
            $output->writeln("le mode maintenance est activé");
        } else {
            $output->writeln("le mode maintenance est désactivé");
        }
    }
}
