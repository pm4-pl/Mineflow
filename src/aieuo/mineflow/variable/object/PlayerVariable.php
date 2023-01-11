<?php

declare(strict_types=1);

namespace aieuo\mineflow\variable\object;

use aieuo\mineflow\variable\BooleanVariable;
use aieuo\mineflow\variable\DummyVariable;
use aieuo\mineflow\variable\NumberVariable;
use aieuo\mineflow\variable\StringVariable;
use pocketmine\player\Player;

class PlayerVariable extends HumanVariable {

    public static function getTypeName(): string {
        return "player";
    }

    public function __construct(private Player $player) {
        parent::__construct($player);
    }

    public function getPlayer(): Player {
        return $this->player;
    }

    public function __toString(): string {
        return $this->getPlayer()->getName();
    }

    public static function registerProperties(string $class = self::class): void {
        HumanVariable::registerProperties($class);

        self::registerProperty(
            $class, "name", new DummyVariable(StringVariable::class),
            fn(Player $player) => new StringVariable($player->getName()),
        );
        self::registerProperty(
            $class, "display_name", new DummyVariable(StringVariable::class),
            fn(Player $player) => new StringVariable($player->getDisplayName()),
        );
        self::registerProperty(
            $class, "locale", new DummyVariable(StringVariable::class),
            fn(Player $player) => new StringVariable($player->getLocale()),
        );
        self::registerProperty(
            $class, "ping", new DummyVariable(NumberVariable::class),
            fn(Player $player) => new NumberVariable($player->getNetworkSession()->getPing()),
        );
        self::registerProperty(
            $class, "ip", new DummyVariable(StringVariable::class),
            fn(Player $player) => new StringVariable($player->getNetworkSession()->getIp()),
        );
        self::registerProperty(
            $class, "port", new DummyVariable(NumberVariable::class),
            fn(Player $player) => new NumberVariable($player->getNetworkSession()->getPort()),
        );
        self::registerProperty(
            $class, "uuid", new DummyVariable(StringVariable::class),
            fn(Player $player) => new StringVariable($player->getUniqueId()->toString()),
        );
        self::registerProperty(
            $class, "spawn_point", new DummyVariable(PositionVariable::class),
            fn(Player $player) => new PositionVariable($player->getSpawn()),
        );
        self::registerProperty(
            $class, "flying", new DummyVariable(BooleanVariable::class),
            fn(Player $player) => new BooleanVariable($player->isFlying()),
        );
        self::registerProperty(
            $class, "first_played", new DummyVariable(NumberVariable::class),
            fn(Player $player) => new NumberVariable($player->getFirstPlayed() ?? 0),
        );
        self::registerProperty(
            $class, "last_played", new DummyVariable(NumberVariable::class),
            fn(Player $player) => new NumberVariable($player->getLastPlayed() ?? 0),
        );
        self::registerProperty(
            $class, "data", new DummyVariable(CustomDataListVariable::class),
            fn(Player $player) => new CustomDataListVariable(PlayerVariable::getTypeName(), $player->getName()),
        );
    }
}
