<?php

declare(strict_types=1);

namespace aieuo\mineflow\flowItem\action\player;

use aieuo\mineflow\flowItem\base\PlayerFlowItem;
use aieuo\mineflow\flowItem\base\PlayerFlowItemTrait;
use aieuo\mineflow\flowItem\base\ScoreboardFlowItem;
use aieuo\mineflow\flowItem\base\ScoreboardFlowItemTrait;
use aieuo\mineflow\flowItem\FlowItem;
use aieuo\mineflow\flowItem\FlowItemExecutor;
use aieuo\mineflow\formAPI\element\mineflow\PlayerVariableDropdown;
use aieuo\mineflow\formAPI\element\mineflow\ScoreboardVariableDropdown;
use aieuo\mineflow\utils\Category;
use aieuo\mineflow\utils\Language;

class HideScoreboard extends FlowItem implements PlayerFlowItem, ScoreboardFlowItem {
    use PlayerFlowItemTrait, ScoreboardFlowItemTrait;

    protected string $id = self::HIDE_SCOREBOARD;

    protected string $name = "action.hideScoreboard.name";
    protected string $detail = "action.hideScoreboard.detail";
    protected array $detailDefaultReplace = ["player", "scoreboard"];

    protected string $category = Category::PLAYER;

    public function __construct(string $player = "", string $scoreboard = "") {
        $this->setPlayerVariableName($player);
        $this->setScoreboardVariableName($scoreboard);
    }

    public function getDetail(): string {
        return Language::get($this->detail, [$this->getPlayerVariableName(), $this->getScoreboardVariableName()]);
    }

    public function isDataValid(): bool {
        return $this->getPlayerVariableName() !== "" and $this->getScoreboardVariableName() !== "";
    }

    public function execute(FlowItemExecutor $source): \Generator {
        $this->throwIfCannotExecute();

        $player = $this->getPlayer($source);
        $this->throwIfInvalidPlayer($player);

        $board = $this->getScoreboard($source);

        $board->hide($player);
        yield FlowItemExecutor::CONTINUE;
    }

    public function getEditFormElements(array $variables): array {
        return [
            new PlayerVariableDropdown($variables, $this->getPlayerVariableName()),
            new ScoreboardVariableDropdown($variables, $this->getScoreboardVariableName()),
        ];
    }

    public function loadSaveData(array $content): FlowItem {
        $this->setPlayerVariableName($content[0]);
        $this->setScoreboardVariableName($content[1]);
        return $this;
    }

    public function serializeContents(): array {
        return [$this->getPlayerVariableName(), $this->getScoreboardVariableName()];
    }
}
