<?php
/**
 * Created by PhpStorm.
 * User: jwalter
 * Date: 18/11/2017
 * Time: 16:18
 */

namespace Wassa\ApiKeyAuthenticatorBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateApiKeyCommand extends ContainerAwareCommand
{
    public function configure()
    {
        $this
            ->setName('apikey-authenticator:create-key')
            ->addOption('size', 's', InputOption::VALUE_REQUIRED, 'Size of the API key')
            ->addArgument('apiKey', InputArgument::OPTIONAL, 'API key');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $apiKeyFile = 'var/private/api.key';
        $io = new SymfonyStyle($input, $output);
        $io->comment('Generating a new API key');

        $size = $input->getOption('size');
        $container = $this->getContainer();
        $kernel = $container->get('kernel');
        $rootDir = $kernel->getRootDir();
        $apiKeyFilePath = $rootDir . '/../' . $apiKeyFile;

        // If a key already exists, ask if we must continue
        if (file_exists($apiKeyFilePath)) {
            $choice = $io->confirm('An API key already exists. Overwrite? Current key will be backed-up in var/private/api.key.backup.', false);

            if ($choice != 'yes') {
                return;
            }

            // User choose yes, so we backup the existing API key
            file_put_contents($apiKeyFilePath . '.backup', file_get_contents($apiKeyFilePath));
        }

        $apiKey = $input->getArgument('apiKey');

        if (!$apiKey) {
            // If user did not provide an API key, use the configured generator to generate one
            $generator = $container->get($container->getParameter('apikey_authenticator.generator'));
            $apiKey = $generator->generate($size);
        }

        // Write the API key in the file api.key file
        file_put_contents($apiKeyFilePath, $apiKey);
        $io->success("API key created in $apiKeyFile");
    }
}