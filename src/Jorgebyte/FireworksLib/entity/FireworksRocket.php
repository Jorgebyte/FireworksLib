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

namespace Jorgebyte\FireworksLib\entity;

use Jorgebyte\FireworksLib\FireworkType;
use Jorgebyte\FireworksLib\item\FireworksItem;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;

final class FireworksRocket extends Entity
{
    public static function getNetworkTypeId(): string
    {
        return EntityIds::FIREWORKS_ROCKET;
    }

    private int $lifeTime;
    private FireworksItem $fireworkItem;

    public function __construct(Location $location, FireworksItem $fireworkItem, ?int $lifeTime = null)
    {
        parent::__construct($location);

        $this->fireworkItem = $fireworkItem;
        $this->setMotion(new Vector3(0.001, 0.05, 0.001));

        $this->lifeTime = $lifeTime ?? $this->fireworkItem->getRandomizedFlightDuration();

        $pk = LevelSoundEventPacket::nonActorSound(
            LevelSoundEvent::LAUNCH,
            $this->location->asVector3(),
            false,
        );
        $this->location->getWorld()->broadcastPacketToViewers($this->location, $pk);
    }

    protected function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo(0.25, 0.25);
    }

    protected function getInitialDragMultiplier(): float
    {
        return 0.99;
    }

    protected function getInitialGravity(): float
    {
        return 0.05;
    }

    public function canSaveWithChunk(): bool
    {
        return false;
    }

    public function entityBaseTick(int $tickDiff = 1): bool
    {
        if ($this->isClosed()) {
            return false;
        }

        parent::entityBaseTick($tickDiff);

        $this->motion->x *= 1.15;
        $this->motion->y += 0.04;
        $this->motion->z *= 1.15;

        if (--$this->lifeTime <= 0 && !$this->isFlaggedForDespawn()) {
            $this->explode();
        }
        return true;
    }

    private function explode(): void
    {
        $this->broadcastAnimation(new FireworkParticleAnimation($this));
        $this->playSounds();

        $this->flagForDespawn();
    }

    private function playSounds(): void
    {
        $fireworksTag = $this->fireworkItem->getNamedTag()->getCompoundTag('Fireworks');
        if ($fireworksTag === null) {
            return;
        }

        $explosionsTag = $fireworksTag->getListTag('Explosions');
        if ($explosionsTag === null) {
            return;
        }

        foreach ($explosionsTag as $explosionNbt) {
            if ($explosionNbt instanceof CompoundTag) {
                $type = $explosionNbt->getByte('FireworkType', FireworkType::SMALL_SPHERE);
                $flicker = $explosionNbt->getByte('FireworkFlicker', 0) === 1;

                $soundId = ($type === FireworkType::HUGE_SPHERE) ? LevelSoundEvent::LARGE_BLAST : LevelSoundEvent::BLAST;
                $pk = LevelSoundEventPacket::nonActorSound($soundId, $this->location->asVector3(), false);
                $this->location->getWorld()->broadcastPacketToViewers($this->location, $pk);

                if ($flicker) {
                    $pkFlicker = LevelSoundEventPacket::nonActorSound(LevelSoundEvent::TWINKLE, $this->location->asVector3(), false);
                    $this->location->getWorld()->broadcastPacketToViewers($this->location, $pkFlicker);
                }
            }
        }
    }

    public function syncNetworkData(EntityMetadataCollection $properties): void
    {
        parent::syncNetworkData($properties);
        $properties->setCompoundTag(
            EntityMetadataProperties::FIREWORK_ITEM,
            new CacheableNbt($this->fireworkItem->getNamedTagCopy()),
        );
    }

    public function saveNBT(): CompoundTag
    {
        $nbt = parent::saveNBT();
        $nbt->setTag('Item', $this->fireworkItem->nbtSerialize());
        return $nbt;
    }
}
