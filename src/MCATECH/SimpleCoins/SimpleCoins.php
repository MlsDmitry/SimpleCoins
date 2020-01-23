<?php

declare(strict_types=1);

namespace MCATECH\SimpleCoins;

use pocketmine\plugin\PluginBase;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;

use pocketmine\utils\TextFormat as T;

use pocketmine\utils\Config;

use pocketmine\Player;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;

use MCATECH\SimpleCoins\commands\PayCommand;
use MCATECH\SimpleCoins\commands\AddCoinsCommand;
use MCATECH\SimpleCoins\commands\TopCommand;

class SimpleCoins extends PluginBase{
	
	protected static $instance;
	
	public $SimpleCoins;
	
	public $prefix = "§eSimpleCoins - ";
	
	public static function getInstance() : SimpleCoins{
		return self::$instance;
	}

	public function onEnable() : void{
		self::$instance = $this;
		$this->regCommands();
		$this->coins = new Config($this->getDataFolder() . "coins.yml", Config::YAML, array());
			if(!is_dir($this->getDataFolder())) mkdir($this->getDataFolder());
			$this->saveDefaultConfig();
	}
	/**
	** !! Important Api for plugin !! Do not edit unless you know what your doing !!
	**/
	
	public function addPlayer($player){
        $this->coins->setNested(strtolower($player->getName()).".coins", "0");
        $this->coins->setNested(strtolower($player->getName()).".rarecoins", "0");
        $this->coins->save();
    }
	
	public function regCommands() : void{
		$server = $this->getServer();
		$server->getCommandMap()->register("pay", new PayCommand($this));
		$server->getCommandMap()->register("addcoins", new AddCoinsCommand($this));
		$server->getCommandMap()->register("topcoins", new TopCommand($this));
	}
	
	public function getCoins($player){
        return $this->coins->getAll()[strtolower($player->getName())]["coins"];
    }
	
	public function getRareCoins($player){
        return $this->coins->getAll()[strtolower($player->getName())]["rarecoins"];
    }
	
	public function addCoins($player, $coins){
		if($player instanceof Player) {
			if($coins < 0){
				return self;
			}
			$this->coins->setNested(strtolower($player->getName()).".coins", $this->coins->getAll()[strtolower($player->getName())]["coins"] + $coins);
			$this->coins->save();
		}
    }
	
	public function remCoins($player, $coins){
		$this->coins->setNested(strtolower($player->getName()).".coins", $this->coins->getAll()[strtolower($player->getName())]["coins"] - $coins);
        $this->coins->save();
		return true;
	}
	
	public function setCoins($player, $coins){
		$this->coins->setNested(strtolower($player->getName()).".coins", $this->coins->getAll()[strtolower($player->getName())]["coins"] = $coins);
        $this->coins->save();
		return true;
	}
	
	public function resetCoins($player){
		$this->coins->setNested(strtolower($player->getName()).".coins", $this->coins->getAll()[strtolower($player->getName())]["coins"] = 0);
        $this->coins->save();
		return true;
	}
	
	public function getAllCoins() : string{
		$coins = $this->coins->getAll();
		$message = "§7Top Coins: ";
        arsort($coins);
        $pos = 1;
        foreach ($coins as $name => $coins){
            if ($pos === 6) break;
			$topcoins = $this->coins->getAll()[strtolower($name)]["coins"];
            $message .= "§f\n$pos. §b$name §7with:§b $topcoins §7coins.";
            $pos++;
        }
		return $message;
	}
	
	public function checkLogin(PlayerPreLoginEvent $event){
		$player = $event->getPlayer();
		if(!$this->coins->exists(strtolower($player->getName()))){
			$this->addPlayer($player);
		}
	}
	
	public function checkProfile($player){
		if(!$this->coins->exists(strtolower($player->getName()))){
			if ($player instanceof Player) {
				$this->addPlayer($player);
			}
		}
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		switch($command->getName()){
			case "coins":
				if(!$this->coins->exists(strtolower($sender->getName()))){
					if ($sender instanceof Player) {
					$this->addPlayer($sender);
					$sender->sendMessage($this->prefix . "You have been added to the coin system. Use /coins to see your balance.");
				}
				}else{
				$sender->sendMessage($this->prefix . "§7You have:§e " . $this->getCoins($sender) . " §7coins.");
				$others = array_shift($args);
					if($others == 'info'){
						$messages = [
						'§e--- §d' . $this->prefix .  'Information §e---',
						'§6Authors: §d{author}',
						'§6Supported API versions: §d{apis}',
						'§6Plugin Version: §d{full_name}',
						'§e--- §d' . $this->prefix .  'Information §e---'
						];
						$values = [
							'{full_name}' => $this->getDescription()->getFullName(),
							'{author}' => implode(', ', $this->getDescription()->getAuthors()),
							'{apis}' => implode(', ', $this->getDescription()->getCompatibleApis()),
						];
						$sender->sendMessage(str_replace(array_keys($values), array_values($values), implode(T::RESET."\n", $messages)));
					}else{
						return true;
					}
				}
				return true;
				break;
			case "rarecoins":
				$sender->sendMessage($this->prefix . "§7You have:§e " . $this->getRareCoins($sender) . " §7Rare coins.");
				return true;
			case "remcoins":
				$target = array_shift($args);
				$coins = array_shift($args);
                    if (is_null($target) or is_null($coins)) {
                        $sender->sendMessage($this->prefix . "§eUsage: §7/remcoins {player} {amount}");
                        break;
                        }
						if (($player = $this->getServer()->getPlayer($target)) instanceof Player) {
                            $sender->sendMessage($this->prefix . "§aYou removed the coins successfully!");
                            $this->remCoins($player, $coins);
                        }
                        break;
				return true;
				break;
			case "setcoins":
				$target = array_shift($args);
				$coins = array_shift($args);
                    if (is_null($target) or is_null($coins)) {
						$sender->sendMessage($this->prefix . "§eUsage: §7/setcoins {player} {amount}");
                        break;
                        }
						if (($player = $this->getServer()->getPlayer($target)) instanceof Player) {
							if ($this->getCoins = $coins){
								$sender->sendMessage($this->prefix . "You cant set your coins to the same amount? silly!");
							}else{
                            $sender->sendMessage($this->prefix . "§aYou set the coins successfully!");
                            $this->setCoins($target, $coins);
							}
                        }
                        break;
				return true;
				break;
			case "resetcoins":
				$target = array_shift($args);
                    if (is_null($target)) {
						$sender->sendMessage($this->prefix . "§eUsage: §7/resetcoins {player}");
                        break;
                        }
						if (($player = $this->getServer()->getPlayer($target)) instanceof Player) {
                            $sender->sendMessage($this->prefix . "§aYou set the coins successfully!");
                            $this->resetCoins($player);
                        }
                        break;
				return true;
				break;
			default:
				return false;
		}
		return true;
	}
}
