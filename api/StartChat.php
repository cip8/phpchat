<?php

namespace App;

use Dotenv\Dotenv;

use App\Socket\ChatSocket;

use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartChat extends SymfonyCommand
{
    /**
     * Configures the command
     */
    public function configure()
    {
        $this->setName('start')
            ->setDescription('<comment>Starts the php chat server</comment>');
    }
    /**
     * Executes the command
     *
     * @param   InputInterface      $input
     * @param   OutputInterface     $output
     * @return  void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        // Loads env vars
        $this->loadEnvVars();

        // Builds front-end
        $output->writeln('<comment> 📺 - Building front end...</comment>');
        $output->writeln('<info>' . $this->buildFrontEnd() . '</info>');

        // Starts the server
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new ChatSocket()
                )
            ),
            7070
        );

        $output->writeln('<comment> 💬 - Chat server running...</comment>');

        $server->run();
    }

    /**
     * Loads env variables
     *
     * @return void
     */
    private function loadEnvVars()
    {
        $dotenv = Dotenv::create(__DIR__);
        $dotenv->load();
        $dotenv->required(['DB_CONNECTION', 'DB_NAME'])->notEmpty();
    }

    /**
     * Builds front-end files
     *
     * @return  void
     */
    private function buildFrontEnd()
    {
        return shell_exec('cd front && yarn build'); 
    }
}
