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

class CoinCommand extends Command {
    
    /** @var SimpleCoins */
    private $plugin;
    
    /**
     * CoinCommand constructor.
     * @param SimpleCoins $plugin
     */
    public function __construct(SimpleCoins $plugin) {
        $this->plugin = $plugin;
        parent::__construct("coins", "Shows your coins or another players", "/coins <info|player>", []);
    }
    
    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(!$sender instanceof Player) {
            $sender->sendMessage("Please, run this command in game!");
            return;
        }
		if(!$this->plugin->coins->exists(strtolower($sender->getName()))){
			if ($sender instanceof Player) {
				$this->plugin->addPlayer($sender);
				$sender->sendMessage($this->plugin->prefix . "You have been added to the coin system. Use /coins to see your balance.");
			}
		}else{
			$others = array_shift($args);
			if (is_null($others)){
				$sender->sendMessage($this->plugin->prefix . "§7You have:§e " . $this->plugin->getCoins($sender) . " §7coins.");
				return;	
			}
			if($others == 'info'){
				$messages = [
					'§e--- §d' . $this->plugin->prefix .  'Information §e---',
					'§6Authors: §d{author}',
					'§6Supported API versions: §d{apis}',
					'§6Plugin Version: §d{full_name}',
					'§e--- §d' . $this->plugin->prefix .  'Information §e---'
					];
				$values = [
					'{full_name}' => $this->plugin->getDescription()->getFullName(),
					'{author}' => implode(', ', $this->plugin->getDescription()->getAuthors()),
					'{apis}' => implode(', ', $this->plugin->getDescription()->getCompatibleApis()),
				];
				$sender->sendMessage(str_replace(array_keys($values), array_values($values), implode(TextFormat::RESET."\n", $messages)));
			}else{
				if(($player = $this->plugin->getServer()->getPlayer($others)) instanceof Player) {
					$tcoins = $this->plugin->getCoins($player);
					if(is_null($tcoins)){
						$sender->sendMessage($this->plugin->prefix . "That Player is not registered with SimpleCoins.");
					}else{
						$sender->sendMessage($this->plugin->prefix . "§7" . $player->getName() . " has:§e $tcoins");
					}
				}else{
					return;
				}
			}
		}
		return true;
	}
}