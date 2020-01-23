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

class AddCoinsCommand extends Command {
    
    /** @var SimpleCoins */
    private $plugin;
    
    /**
     * PayCommand constructor.
     * @param SimpleCoins $plugin
     */
    public function __construct(SimpleCoins $plugin) {
        $this->plugin = $plugin;
        parent::__construct("addcoins", "add coins to another player", "/addcoins <player> <amount>", []);
    }
    
    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(!$sender instanceof Player) {
            $sender->sendMessage("Please, run this command in game!");
            return;
        }
		if($sender instanceof Player){
			$target = array_shift($args);
			$amount = array_shift($args);
			if (is_null($target) or is_null($amount)) {
				$sender->sendMessage("§eUsage: §7/addcoins <player> <amount>");
                return;
            }
			if(!$sender->isOp()){
				$sender->sendMessage("§7You don't have permission to use this command!");
				return;
			}else{
				if (($player = $this->plugin->getServer()->getPlayer($target)) instanceof Player) {
                    $sender->sendMessage("§aYou added the coins successfully!");
                    $this->plugin->addCoins($player, $amount);
                }
				return true;
			}
			
			
		}
		return true;
	}
}