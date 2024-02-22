<?php

namespace Premium\Jorgebyte\commands;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Premium\Jorgebyte\Main;
use Premium\Jorgebyte\menus\MenuGui;
use Premium\Jorgebyte\Utils;

class PremiumCommand extends Command
{

    public $plugin;
    public function __construct(Main $plugin)
    {
        parent::__construct("premium", "Premium Command", null, ["vip", "vips", "premiums"]);
        $this->setPermission("premium.command");
        $this->setPermissionMessage(Utils::NO_PERMS);
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player)return;
        $menuGui = new MenuGui($this->plugin);
        $menuGui->getMainMenu($sender);
    }
}