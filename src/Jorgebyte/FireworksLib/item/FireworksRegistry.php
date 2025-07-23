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

namespace Jorgebyte\FireworksLib\item;

use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;
use pocketmine\utils\CloningRegistryTrait;

/**
 * @method static FireworksItem FIREWORKS()
 */
final class FireworksRegistry
{
    use CloningRegistryTrait {
        setup as protected _setup;
    }

    private function __construct()
    {}

    protected static function register(string $name, Item $item): void
    {
        self::_registryRegister($name, $item);
    }

    /**
     * @return Item[]
     */
    public static function getAll(): array
    {
        return self::_registryGetAll();
    }

    public static function setup(): void
    {
        self::_setup();
    }

    protected static function _setup(): void
    {
        $id = ItemTypeIds::newId();
        self::register(
            'fireworks',
            new FireworksItem(new ItemIdentifier($id, 0), 'Fireworks'),
        );
    }

    public static function getFireworksItem(): object
    {
        return self::_registryFromString('fireworks');
    }
}
