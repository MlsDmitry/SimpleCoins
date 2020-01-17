<?php

namespace MCATECH\SimpleCoins\commands;


use MCATECH\SimpleCoins\SimpleCoins;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\event\server;

class TopCommand extends Command {
    
    /** @var SimpleCoins */
    private $plugin;
    
    /**
     * TopCommand constructor.
     * @param SimpleCoins $plugin
     */
    public function __construct(SimpleCoins $plugin) {
        $this->plugin = $plugin;
        parent::__construct("topcoins", "gets the top players", "/top", []);
    }
    
    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(!$sender instanceof Player) {
            $sender->sendMessage("Please, run this command in game!");
            return;
        }
		if($sender instanceof Player){
			$sender->sendMessage($this->plugin->getAllCoins());
		}
		return true;
	}
}