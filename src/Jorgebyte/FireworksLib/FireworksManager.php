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
use Jorgebyte\FireworksLib\item\FireworksItem;
use pocketmine\entity\Location;
use SOFe\AwaitGenerator\Await;
use SOFe\AwaitGenerator\Traverser;
use SOFe\AwaitStd\AwaitStd;

final class FireworksManager
{
    private const BATCH_SIZE = 15;
    private const DELAY_TICKS = 1;

    public static function launch(Location $location, FireworksItem $firework): void
    {
        $rocket = new FireworksRocket($location, $firework);
        $rocket->spawnToAll();
    }

    public static function launchAsync(Location $location, FireworksItem $firework): Traverser
    {
        return Traverser::fromClosure(function () use ($location, $firework) {
            self::launch($location, $firework);
            yield Await::ONCE;
        });
    }

    public static function launchMultipleAsync(
        AwaitStd $std,
        Location $center,
        FireworksItem $firework,
        int $count,
        float $radius = 3.0,
        float $delayBetweenBatches = 0.5,
    ): Traverser {
        return Traverser::fromClosure(function () use ($std, $center, $firework, $count, $radius, $delayBetweenBatches) {
            $batch = [];
            $angleStep = (2 * M_PI) / max($count, 1);
            $world = $center->getWorld();

            for ($i = 0; $i < $count; ++$i) {
                $angle = $i * $angleStep;
                $x = $center->x + $radius * cos($angle);
                $z = $center->z + $radius * sin($angle);
                $location = new Location($x, $center->y, $z, $world, $center->yaw, $center->pitch);

                $batch[] = $location;

                if (count($batch) >= self::BATCH_SIZE) {
                    yield from self::processBatch($std, $batch, $firework)->asGenerator();
                    $batch = [];

                    if ($delayBetweenBatches > 0) {
                        yield from $std->sleep((int) ($delayBetweenBatches * 20));
                    }
                }
            }

            if (!empty($batch)) {
                yield from self::processBatch($std, $batch, $firework)->asGenerator();
            }
        });
    }

    private static function processBatch(AwaitStd $std, array $locations, FireworksItem $firework): Traverser
    {
        return Traverser::fromClosure(function () use ($std, $locations, $firework) {
            foreach ($locations as $location) {
                self::launch($location, clone $firework);
            }
            yield from $std->sleep(self::DELAY_TICKS);
        });
    }
}
