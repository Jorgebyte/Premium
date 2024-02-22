<?php

namespace Premium\Jorgebyte\menus;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\block\Wool;
use pocketmine\item\Item;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Terminal;
use pocketmine\utils\TextFormat;
use Premium\Jorgebyte\Main;
use Premium\Jorgebyte\Utils;

class MenuGui
{

    public $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function getMainMenu(Player $player): void
    {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
        $menu->setName("Premium");
        $inv = $menu->getInventory();

        $inv->setItem(49, VanillaItems::REDSTONE_DUST()
            ->setCustomName(TextFormat::RED . "Close")
            ->setLore([TextFormat::GRAY . "Click Close Menu"]));
                      // SIZE ITEM 1
        $inv->setItem(20, VanillaItems::TOTEM()
            ->setCustomName(TextFormat::BOLD . TextFormat::GREEN . "SIZE")
            ->setLore([TextFormat::GRAY . "Change Size"]));
                      // FLY ITEM 2
        $inv->setItem(22, VanillaItems::FEATHER()
            ->setCustomName(TextFormat::BOLD . TextFormat::MINECOIN_GOLD . "FLY")
            ->setLore([TextFormat::GRAY . "Have fun flying through the skies :)"]));
                      // ZONA VIP ITEM 3
        $inv->setItem(24, VanillaItems::DIAMOND()
            ->setCustomName(TextFormat::BOLD . TextFormat::BLUE . "ZONA" . TextFormat::YELLOW . "VIP")
            ->setLore([TextFormat::GRAY . "This area is only exclusive for people who have a pay range"]));
                     // MINA VIP ITEM 4
        $inv->setItem(30, VanillaItems::DIAMOND_PICKAXE()
            ->setCustomName(TextFormat::BOLD . TextFormat::BLUE . "MINA" . TextFormat::GREEN . "VIP")
            ->setLore([TextFormat::GRAY . "This mine you can get better minars of better quality and price"]));
                     // GLOBAL TEXT IETM 5
        $inv->setItem(32, VanillaItems::SPYGLASS()
            ->setCustomName(TextFormat::BOLD . TextFormat::AQUA . "GLOBAL" . TextFormat::MINECOIN_GOLD . "TEXT")
            ->setLore([TextFormat::GRAY . "Send a message to everyone so they know what you want"]));
                    // COLOR TAG ITEM 6
        $inv->setItem(40, VanillaBlocks::WOOL()
            ->asItem()
            ->setCustomName(TextFormat::BOLD . TextFormat::RED . "COLOR" . TextFormat::DARK_PURPLE . "TAG")
            ->setLore([TextFormat::GRAY . "Change the color of your name"]));


        // this is responsible for filling with an item except the variable $excludedSlots
        $excludedSlots = [10, 11, 12, 13, 14, 15, 16, 19, 20, 21, 22, 23, 24, 25, 28, 29,
            30, 31, 32, 33, 34, 37, 38, 39, 40, 41, 42, 43, 49];
        for ($i = 0; $i <= 53; $i++) {
            if (!in_array($i, $excludedSlots)) {
                $inv->setItem($i, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::CYAN())->asItem()->setCustomName("§§"));
            }
        }
        # ---------------------------------------------------------------------------------------------------------------------------
        $menu->setListener(function(InvMenuTransaction $transaction) : InvMenuTransactionResult {
            $player = $transaction->getPlayer();
            $menu = $transaction->getAction()->getInventory();
            $itemClicked = $transaction->getOut();
            return $transaction->discard()->then(function(Player $player) use($itemClicked) {
                if ($itemClicked->getTypeId() === ItemTypeIds::REDSTONE_DUST) {
                    $player->removeCurrentWindow();
                    Utils::addSound($player, "block.beehive.exit");
                }
                if ($itemClicked->getTypeId() === ItemTypeIds::TOTEM) {
                    $player->removeCurrentWindow();
                    $this->getSizeMenu($player);
                    Utils::addSound($player, "bubble.pop");
                }
                if ($itemClicked->getTypeId() === ItemTypeIds::FEATHER) {
                    $player->removeCurrentWindow();
                    $flyMenu = new MenuForm();
                    $flyMenu->getMenuFly($player);
                    Utils::addSound($player, "bubble.pop");
                }
                if ($itemClicked->getTypeId() === ItemTypeIds::DIAMOND) {
                    $zonavip = $this->plugin->getConfig()->get("world-zonavip");
                    if ($this->plugin->getServer()->getWorldManager()->isWorldGenerated($zonavip)) {
                        $player->teleport($this->plugin->getServer()->getWorldManager()->getWorldByName($zonavip)->getSafeSpawn());
                        $player->sendMessage(Utils::PREFIX . TextFormat::GRAY . "You have been taken to the VIP area");
                    } else{
                        $player->sendMessage(TextFormat::RED. "ERROR: The world is incorrect or has not been loaded");
                    }
                    $player->removeCurrentWindow();
                    Utils::addSound($player, "bubble.pop");
                }
                if ($itemClicked->getTypeId() === ItemTypeIds::DIAMOND_PICKAXE) {
                    $minavip = $this->plugin->getConfig()->get("world-minavip");
                    if ($this->plugin->getServer()->getWorldManager()->isWorldGenerated($minavip)) {
                        $player->teleport($this->plugin->getServer()->getWorldManager()->getWorldByName($minavip)->getSafeSpawn());
                        $player->sendMessage(Utils::PREFIX . TextFormat::GRAY . "You have gone to minavip");
                    } else{
                        $player->sendMessage(TextFormat::RED. "ERROR: The world is incorrect or has not been loaded");
                    }
                    $player->removeCurrentWindow();
                    Utils::addSound($player, "bubble.pop");
                }
                if ($itemClicked->getTypeId() === ItemTypeIds::SPYGLASS) {
                    $player->removeCurrentWindow();
                    $flyMenu = new MenuForm();
                    $flyMenu->getMenuGlobalText($player);
                    Utils::addSound($player, "bubble.pop");
                }
                if ($itemClicked->getCustomName() === TextFormat::BOLD . TextFormat::RED . "COLOR" . TextFormat::DARK_PURPLE . "TAG") {
                    $player->removeCurrentWindow();
                    $this->getMenuColorTag($player);
                    Utils::addSound($player, "bubble.pop");
                }
            });
        });
        $menu->send($player);
    }

    public function getSizeMenu(Player $player): void
    {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
        $menu->setName(TextFormat::colorize("&l&bPREMIUM&r&7: &aSize"));
        $inv = $menu->getInventory();

        $menuItems = [
            [
                "item" => VanillaItems::EGG(),
                "name" => TextFormat::BOLD . TextFormat::MINECOIN_GOLD . "LITTLE",
                "size" => 0.5,
                "lore" => [TextFormat::GRAY . "Size is 0.5"]
            ],
            [
                "item" => VanillaItems::TOTEM(),
                "name" => TextFormat::BOLD . TextFormat::MINECOIN_GOLD . "NORMAL",
                "size" => 1.0,
                "lore" => [TextFormat::GRAY . "Size is 1.0"]
            ],
            [
                "item" => VanillaItems::SLIMEBALL(),
                "name" => TextFormat::BOLD . TextFormat::MINECOIN_GOLD . "MEDIUM",
                "size" => 1.5,
                "lore" => [TextFormat::GRAY . "Size is 1.5"]
            ],
            [
                "item" => VanillaItems::BLAZE_POWDER(),
                "name" => TextFormat::BOLD . TextFormat::MINECOIN_GOLD . "BIG",
                "size" => 2.0,
                "lore" => [TextFormat::GRAY . "Size is 2.0"]
            ]
        ];
        foreach ($menuItems as $itemInfo) {
            $item = $itemInfo["item"];
            $name = $itemInfo["name"];
            $lore = $itemInfo["lore"];

            $inv->addItem($item->setCustomName($name)->setLore($lore)
            );
        }
        $menu->setListener(function(InvMenuTransaction $transaction) : InvMenuTransactionResult {
            $player = $transaction->getPlayer();
            $menu = $transaction->getAction()->getInventory();
            $itemClicked = $transaction->getOut();
            return $transaction->discard()->then(function(Player $player) use($itemClicked) {
                if (isset($itemClicked)) {
                    $action = Utils::getItemActionSize($itemClicked);
                    if ($action !== null) {
                        $action($player, $itemClicked);
                    }
                }
            });
        });
        $menu->send($player);
    }

    public function getMenuColorTag(Player $player): void
    {
        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $menu->setName(TextFormat::colorize("&l&bPREMIUM&r&7: &cCOLOR&9 TAG"));
        $inv = $menu->getInventory();

        $colors = [
            [
                "color" => TextFormat::GREEN,
                "name" => " GREEN",
                "lore" => [TextFormat::GRAY . "Your name will be colored: GREEN"]
            ],
            [
                "color" => TextFormat::RED,
                "name" => " RED",
                "lore" => [TextFormat::GRAY . "Your name will be colored: RED"]
            ],
            [
                "color" => TextFormat::DARK_PURPLE,
                "name" => " DARK PURPLE",
                "lore" => [TextFormat::GRAY . "Your name will be colored: PURPLE"]
            ],
            [
                "color" => TextFormat::YELLOW,
                "name" => " YELLOW",
                "lore" => [TextFormat::GRAY . "Your name will be colored: YELLOW"]
            ],
            [
                "color" => TextFormat::BLUE,
                "name" => " BLUE",
                "lore" => [TextFormat::GRAY . "Your name will be colored: BLUE"]
            ],
            [
                "color" => TextFormat::AQUA,
                "name" => " AQUA",
                "lore" => [TextFormat::GRAY . "Your name will be colored: AQUA"]
            ],
            [
                "color" => TextFormat::BLACK,
                "name" => " BLACK",
                "lore" => [TextFormat::GRAY . "Your name will be colored: BLACK"]
            ],
            [
                "color" => TextFormat::GRAY,
                "name" => " GRAY",
                "lore" => [TextFormat::GRAY . "Your name will be colored: GRAY"]
            ],
            [
                "color" => TextFormat::LIGHT_PURPLE,
                "name" => " PINK",
                "lore" => [TextFormat::GRAY . "Your name will be colored: PINK"]
            ],
            [
                "color" => TextFormat::WHITE,
                "name" => " PINK",
                "lore" => [TextFormat::GRAY . "Your name will be colored: WHITE"]
            ]
        ];

        foreach ($colors as $colorInfo) {
            $color = $colorInfo["color"];
            $name = $colorInfo["name"];
            $lore = $colorInfo["lore"];

            switch ($color) {
                case TextFormat::GREEN:
                    $dyeColor = DyeColor::GREEN;
                    break;
                case TextFormat::RED:
                    $dyeColor = DyeColor::RED;
                    break;
                case TextFormat::DARK_PURPLE:
                    $dyeColor = DyeColor::PURPLE;
                    break;
                case TextFormat::YELLOW:
                    $dyeColor = DyeColor::YELLOW;
                    break;
                case TextFormat::BLUE:
                    $dyeColor = DyeColor::BLUE;
                    break;
                case TextFormat::AQUA:
                    $dyeColor = DyeColor::CYAN;
                    break;
                case TextFormat::BLACK:
                    $dyeColor = DyeColor::BLACK;
                    break;
                case TextFormat::GRAY:
                    $dyeColor = DyeColor::GRAY;
                    break;
                case TextFormat::LIGHT_PURPLE:
                    $dyeColor = DyeColor::PINK;
                    break;
                case TextFormat::WHITE:
                    $dyeColor = DyeColor::WHITE;
                    break;
            }

            $inv->addItem(VanillaBlocks::WOOL()->setColor($dyeColor)
                ->asItem()
                ->setCustomName($color . $name)
                ->setLore($lore));
        }
        $menu->setListener(function(InvMenuTransaction $transaction) : InvMenuTransactionResult {
            $player = $transaction->getPlayer();
            $menu = $transaction->getAction()->getInventory();
            $itemClicked = $transaction->getOut();
            $color = explode(" ", $itemClicked->getCustomName())[0];

            $player->setDisplayName($color . $player->getName() . TextFormat::RESET);
            $player->setNameTag($color . $player->getName());
            Utils::addSound($player, "bubble.pop");
            $player->removeCurrentWindow();

            return $transaction->discard();
        });
        $menu->send($player);
    }
}