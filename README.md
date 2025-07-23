### FireworksLib: Advanced Fireworks Management for PocketMine-MP
A high-performance library for creating and controlling customizable fireworks displays in PocketMine-MP servers.

---

## 📦 Installation
1. Install via [Composer](https://getcomposer.org):
```bash
composer require jorgebyte/fireworkslib
```  
2. Register in your plugin:
```php
use Jorgebyte\FireworksLib\FireworksLib;

public function onEnable(): void {
    FireworksLib::register();
}
```

---

## ✨ Key Features
- **Synchronous & Asynchronous Launching**
- **Customizable Explosions** (colors, shapes, effects)
- **Batch Processing** with configurable delays
- **Particle/Sound Synchronization**
- **Mathematical Trajectory Control** (radial distribution, angles)

---

## 🚀 Usage Examples

### 1. Basic Firework Launch
```php
use Jorgebyte\FireworksLib\FireworksManager;
use Jorgebyte\FireworksLib\FireworksRegistry;
use pocketmine\world\Position;

$center = new Position($x, $y, $z, $world);
$firework = FireworksRegistry::FIREWORKS();
FireworksManager::launch($center->asLocation(), $firework);
```

### 2. Customized Firework Item
```php
use Jorgebyte\FireworksLib\FireworkType;
use pocketmine\color\Color;

$customFirework = FireworksRegistry::FIREWORKS();
$customFirework->setFlightDuration(3); // 3-second fuse
$customFirework->addExplosion(
    type: FireworkType::STAR,
    color: new Color(255, 0, 0), // Red
    fade: new Color(0, 0, 255),  // Blue fade
    flicker: true,
    trail: true
);
```

### 3. Asynchronous Batch Launch (Radial Pattern)
```php
use SOFe\AwaitStd\AwaitStd;

Await::f2c(function () use ($center, $std) {
    yield from FireworksManager::launchMultipleAsync(
        std: $std,
        center: $center,
        firework: FireworksRegistry::FIREWORKS(),
        count: 30,
        radius: 5.0,
        delayBetweenBatches: 0.2
    )->asGenerator();
});
```

---

## 🧩 API Reference

### 🔧 `FireworksManager`
| Method | Parameters | Description |
|--------|------------|-------------|
| `launch` | `Location $location`, `FireworksItem $firework` | Sync single firework launch |
| `launchAsync` | `Location $location`, `FireworksItem $firework` | Async generator (Traverser) |
| `launchMultipleAsync` | `AwaitStd $std`, `Location $center`, `FireworksItem $firework`, `int $count`, `float $radius=3.0`, `float $delayBetweenBatches=0.5` | Batch launch in radial pattern |

### 🧪 `FireworksItem` Methods
```php
// Set flight duration (1-3 recommended)
$item->setFlightDuration(int $duration);

// Add explosion effect:
$item->addExplosion(
    int $type,          // FireworkType::CONSTANT
    Color $color,       // Primary RGB
    ?Color $fade = null,// Fade color
    bool $flicker = false,
    bool $trail = false
);
```

### 💥 `FireworkType` Constants
| Constant | Effect |
|----------|--------|
| `SMALL_SPHERE` | Standard spherical burst |
| `HUGE_SPHERE` | Large-scale sphere |
| `STAR` | Star-shaped explosion |
| `CREEPER_HEAD` | Creeper-face pattern |
| `BURST` | Crackle-effect explosion |

---

## 🎯 Benefits
1. **Performance Optimization**
    - Batch processing with `BATCH_SIZE` and tick delays
    - Async coroutines prevent server lag
2. **Precision Control**
    - Radial coordinate calculation via `$angleStep = (2 * M_PI) / $count`
    - Customizable delay intervals between batches
3. **Extensibility**
    - Implement custom trajectories by extending `FireworksRocket`
    - Modify `FireworkParticleAnimation` for custom effects

---

## 🧪 Advanced Implementation
### Extending FireworksRocket
```php
class CustomRocket extends FireworksRocket {
    protected function explode(): void {
        // Custom explosion logic
        parent::explode(); // Call default behavior
    }
}
```

### Direct NBT Manipulation
```php
$nbt = $firework->getNamedTag();
$nbt->getCompoundTag("Fireworks")?->setByte("Flight", 2);
```

---

## 🤝 Contribution
1. Fork the repository
2. Adhere to PER-CS2.0 coding standards
3. Submit pull requests with detailed descriptions

**Supported by:**  
[Await Generator](https://github.com/SOF3/await-generator)

[Await Std](https://github.com/ACM-PocketMine-MP/await-std)

---

> Transform your server events with mathematically precise firework displays! 🎆  
> *Developed by Jorgebyte*
