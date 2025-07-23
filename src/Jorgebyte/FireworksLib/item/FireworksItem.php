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

use Jorgebyte\FireworksLib\entity\FireworksRocket;
use pocketmine\block\Block;
use pocketmine\color\Color;
use pocketmine\entity\Location;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\player\Player;

final class FireworksItem extends Item
{
    private const TAG_FIREWORKS = 'Fireworks';
    private const TAG_EXPLOSIONS = 'Explosions';
    private const TAG_FLIGHT = 'Flight';

    public function __construct(ItemIdentifier $identifier, string $name)
    {
        parent::__construct($identifier, $name);
        $this->initNbt();
    }

    private function initNbt(): void
    {
        $tag = $this->getNamedTag();
        $tag->setTag(
            self::TAG_FIREWORKS,
            CompoundTag::create()
                ->setByte(self::TAG_FLIGHT, 1)
                ->setTag(self::TAG_EXPLOSIONS, new ListTag([])),
        );
    }

    public function getFlightDuration(): int
    {
        return $this->getFireworksTag()->getByte(self::TAG_FLIGHT, 1);
    }

    public function setFlightDuration(int $duration): void
    {
        $tag = $this->getFireworksTag();
        $tag->setByte(self::TAG_FLIGHT, max(1, $duration));
        $this->setFireworksTag($tag);
    }

    public function addExplosion(
        int $type,
        Color $color,
        ?Color $fade = null,
        bool $flicker = false,
        bool $trail = false,
    ): void {
        $explosion = CompoundTag::create()
            ->setByte('FireworkType', $type)
            ->setByteArray('FireworkColor', $this->colorToBytes([$color]))
            ->setByteArray('FireworkFade', $fade ? $this->colorToBytes([$fade]) : '')
            ->setByte('FireworkFlicker', $flicker ? 1 : 0)
            ->setByte('FireworkTrail', $trail ? 1 : 0);

        $tag = $this->getFireworksTag();
        $explosions = $tag->getListTag(self::TAG_EXPLOSIONS) ?? new ListTag();
        $explosions->push($explosion);
        $tag->setTag(self::TAG_EXPLOSIONS, $explosions);
        $this->setFireworksTag($tag);
    }

    private function colorToBytes(array $colors): string
    {
        return implode('', array_map(
            fn(Color $c) => chr($c->getR()) . chr($c->getG()) . chr($c->getB()),
            $colors,
        ));
    }

    private function getFireworksTag(): CompoundTag
    {
        $tag = $this->getNamedTag()->getCompoundTag(self::TAG_FIREWORKS);
        if ($tag === null) {
            $this->initNbt();
            $tag = $this->getNamedTag()->getCompoundTag(self::TAG_FIREWORKS);
        }
        return $tag;
    }

    private function setFireworksTag(CompoundTag $tag): void
    {
        $this->getNamedTag()->setTag(self::TAG_FIREWORKS, $tag);
    }

    public function onInteractBlock(
        Player $player,
        Block $blockReplace,
        Block $blockClicked,
        int $face,
        Vector3 $clickVector,
        array &$returnedItems,
    ): ItemUseResult {
        if (!$blockReplace->canBeReplaced()) {
            return ItemUseResult::NONE();
        }

        $location = Location::fromObject(
            $blockReplace->getPosition()->add(0.5, 0, 0.5),
            $player->getWorld(),
            lcg_value() * 360,
            90,
        );

        $entity = new FireworksRocket($location, $this->pop());
        $entity->spawnToAll();

        return ItemUseResult::SUCCESS();
    }

    public function getRandomizedFlightDuration(): int
    {
        $flight = $this->getFlightDuration();
        return ($flight + 1) * 10 + mt_rand(0, 5) + mt_rand(0, 6);
    }

    public function getNamedTagCopy(): CompoundTag
    {
        return clone $this->getNamedTag();
    }
}
