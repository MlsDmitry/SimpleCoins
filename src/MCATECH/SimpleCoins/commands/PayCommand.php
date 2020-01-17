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

class PayCommand extends Command {
    
    /** @var SimpleCoins */
    private $plugin;
    
    /**
     * PayCommand constructor.
     * @param SimpleCoins $plugin
     */
    public function __construct(SimpleCoins $plugin) {
        $this->plugin = $plugin;
        parent::__construct("pay", "pay a certain amount to another player", "/pay <player> <amount>", [
            "donate",
			"givemoney",
			"sendmoney"
        ]);
    }
    
    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(!$sender instanceof Player) {
            $sender->sendMessage("Please, run this command in game!");
            return;
        }
		if($sender instanceof Player){
			$target = array_shift($args);
			$sendercoins = $this->plugin->getCoins($sender);
			$amount = array_shift($args);
			if (is_null($target) or is_null($amount)) {
				$sender->sendMessage("§eUsage: §7/pay {player} {amount}");
                return;
            }
			if($sendercoins < $amount){
				$sender->sendMessage("§7You don't have enough coins to send §b$amount §7coins!");
				return;
			}else{
				if (($player = $this->plugin->getServer()->getPlayer($target)) instanceof Player) {
					if($player != $sender){
                    $sender->sendMessage("§aYou sent $target $amount coins.");
					$this->plugin->remCoins($sender, $amount);
					$this->plugin->addCoins($player, $amount);
					$player->sendMessage("§7You have recieved:§b $amount §7coins from §a".$sender->getName().".");
					}else{
						$sender->sendMessage("§7You can't send money to yourself.");
					}
                }else{
					$sender->sendMessage("§7This player is not online.");
				}
			}
			
			
		}
		return true;
	}
}