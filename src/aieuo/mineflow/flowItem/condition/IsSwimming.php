<?php

namespace aieuo\mineflow\flowItem\condition;

use aieuo\mineflow\flowItem\base\PlayerFlowItem;
use aieuo\mineflow\flowItem\base\PlayerFlowItemTrait;
use aieuo\mineflow\flowItem\FlowItem;
use aieuo\mineflow\flowItem\FlowItemExecutor;
use aieuo\mineflow\formAPI\element\mineflow\PlayerVariableDropdown;
use aieuo\mineflow\utils\Category;
use aieuo\mineflow\utils\Language;

class IsSwimming extends IsFlying {

    protected string $id = self::IS_SWIMMING;

    protected string $name = "condition.isSwimming.name";
    protected string $detail = "condition.isSwimming.detail";

    public function execute(FlowItemExecutor $source): \Generator {
        $this->throwIfCannotExecute();

        $player = $this->getPlayer($source);
        $this->throwIfInvalidPlayer($player);

        yield true;
        return $player->isSwimming();
    }
}
