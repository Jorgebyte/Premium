<?php

namespace Premium\Jorgebyte;

use muqsit\invmenu\InvMenuHandler;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use Premium\Jorgebyte\commands\PremiumCommand;

class Main extends PluginBase
{
    public function onEnable(): void
    {
        if (!InvMenuHandler::isRegistered())
        InvMenuHandler::register($this);

        $this->getLogger()->info(TextFormat::RED . "WARNING: Plugin in development there may be errors");
        $this->getServer()->getCommandMap()->register("PremiumCommand", new PremiumCommand($this));
    }
}