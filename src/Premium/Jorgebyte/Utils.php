<?php

namespace Premium\Jorgebyte;

use pocketmine\item\Item;
use pocketmine\item\ItemTypeIds;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class Utils
{

    const PREFIX = TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::AQUA . "Premium" . TextFormat::GRAY . "] " . TextFormat::RESET;
    const NO_PERMS = self::PREFIX . TextFormat::RED . "You do not have enough permissions to do this";

    public static function addSound(Player $player, string $sound, $volume = 1, $pitch = 1): void
    {
        $packet = new PlaySoundPacket();
        $packet->x = $player->getPosition()->getX();
        $packet->y = $player->getPosition()->getY();
        $packet->z = $player->getPosition()->getZ();
        $packet->soundName = $sound;
        $packet->volume = 1;
        $packet->pitch = 1;
        $player->getNetworkSession()->sendDataPacket($packet);
    }

    public static function getItemActionSize(Item $item): ?callable
    {
        $actions = [
            ItemTypeIds::EGG => function (Player $player) {
                $player->setScale(0.5);
                $player->removeCurrentWindow();
                $player->sendMessage(Utils::PREFIX . TextFormat::GRAY . "Now you are a small size");
                Utils::addSound($player, "bubble.pop");
            },
            ItemTypeIds::TOTEM => function (Player $player) {
                $player->setScale(1.0);
                $player->removeCurrentWindow();
                $player->sendMessage(Utils::PREFIX . TextFormat::GRAY . "You are now a normal size");
                Utils::addSound($player, "bubble.pop");
            },
            ItemTypeIds::SLIMEBALL => function (Player $player) {
                $player->setScale(1.5);
                $player->removeCurrentWindow();
                $player->sendMessage(Utils::PREFIX . TextFormat::GRAY . "You are now a medium size");
                Utils::addSound($player, "bubble.pop");
            },
            ItemTypeIds::BLAZE_POWDER => function (Player $player) {
                $player->setScale(2.0);
                $player->removeCurrentWindow();
                $player->sendMessage(Utils::PREFIX . TextFormat::GRAY . "You are now a big size");
                Utils::addSound($player, "bubble.pop");
            },
        ];
        return $actions[$item->getTypeId()] ?? null;
    }

}