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

use pocketmine\entity\animation\Animation;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\network\mcpe\protocol\types\ActorEvent;

readonly class FireworkParticleAnimation implements Animation
{
    public function __construct(private FireworksRocket $firework) {}

    public function encode(): array
    {
        return [
            ActorEventPacket::create(
                $this->firework->getId(),
                ActorEvent::FIREWORK_PARTICLES,
                0,
            ),
        ];
    }
}
