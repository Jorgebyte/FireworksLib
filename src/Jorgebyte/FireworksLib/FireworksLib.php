<?php

declare(strict_types=1);

/**
 * @package   FireworksLib
 * @author    Jorgebyte
 * @version   1.0.0
 * @api       5.0.0
 * @copyright (c) 2024 Jorgebyte. All rights reserved under the license.
 * @license   GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 */

namespace Jorgebyte\FireworksLib;

use Jorgebyte\FireworksLib\entity\FireworksRocket;
use Jorgebyte\FireworksLib\item\FireworksRegistry;
use pocketmine\data\bedrock\item\ItemTypeNames;
use pocketmine\data\bedrock\item\SavedItemData;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\format\io\GlobalItemDataHandlers;
use pocketmine\world\World;

final class FireworksLib
{
    public static function register(): void
    {
        $fireworks = FireworksRegistry::FIREWORKS();

        $deserializer = GlobalItemDataHandlers::getDeserializer();
        $serializer = GlobalItemDataHandlers::getSerializer();
        $parser = StringToItemParser::getInstance();

        $deserializer->map(ItemTypeNames::FIREWORK_ROCKET, static fn() => clone $fireworks);
        $serializer->map($fireworks, static fn() => new SavedItemData(ItemTypeNames::FIREWORK_ROCKET));
        $parser->register('firework_rocket', static fn() => clone $fireworks);

        EntityFactory::getInstance()->register(
            FireworksRocket::class,
            function (World $world, CompoundTag $nbt): FireworksRocket {
                $itemTag = $nbt->getCompoundTag('Item');
                $item = $itemTag !== null ?
                    Item::nbtDeserialize($itemTag) :
                    FireworksRegistry::FIREWORKS();

                return new FireworksRocket(
                    EntityDataHelper::parseLocation($nbt, $world),
                    $item,
                );
            },
            ['FireworksRocket', 'minecraft:fireworks_rocket'],
        );
    }
}
