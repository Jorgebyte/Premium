<?php

namespace Premium\Jorgebyte\menus;

use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use Premium\Jorgebyte\Utils;
use Vecnavium\FormsUI\CustomForm;
use Vecnavium\FormsUI\SimpleForm;

class MenuForm
{

    public function getMenuFly(Player $player): void
    {
        $form = new SimpleForm(function (Player $player, int $data = null) {
            if($data === null) {
                return true;
            }
            switch($data) {
                case 0:
                    Utils::addSound($player, "dig.snow");
                    $player->setAllowFlight(true);
                    $player->sendMessage(Utils::PREFIX . TextFormat::GRAY . "You have activated flight");
                    $player->sendPopup(TextFormat::GREEN . "Activate Fly");
                    break;
                case 1:
                    Utils::addSound($player, "dig.snow");
                    $player->setAllowFlight(false);
                    $player->setFlying(false);
                    $player->sendMessage(Utils::PREFIX . TextFormat::GRAY . "You have disabled flights");
                    $player->sendPopup(TextFormat::RED . "Disable Fly");
                    break;
                case 2:
                    Utils::addSound($player, "block.beehive.exit");
                    break;
            }
            return true;
        });
        $form->setTitle(TextFormat::colorize("&l&bPREMIUM&r&7: &eFLY"));
        $form->setContent(TextFormat::GRAY . "Choose whether you want to activate the flight or not, remember not to misuse it");
        $form->addButton("ACTIVATE FLY");
        $form->addButton("DISABLE FLY");
        $form->addButton(TextFormat::RED . "Close");
        $player->sendForm($form);
    }

    public function getMenuGlobalText(Player $player): void
    {
        $form = new CustomForm(function(Player $player, array $data = null) {
            if ($data === null) {
                return;
            }
            $message = $data[0];
            $message = Utils::PREFIX . TextFormat::GRAY . "[" . $player->getName() . "] " . $message;
            foreach (Server::getInstance()->getOnlinePlayers() as $player) $player->sendMessage($message);
        });
        $form->setTitle("Send a global message");
        $form->addInput("Message", "Write your message");
        $player->sendForm($form);
    }
}