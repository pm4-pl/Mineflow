<?php

declare(strict_types=1);

namespace aieuo\mineflow\flowItem\action\scoreboard;

use aieuo\mineflow\flowItem\base\ActionNameWithMineflowLanguage;
use aieuo\mineflow\flowItem\base\ScoreboardFlowItem;
use aieuo\mineflow\flowItem\base\ScoreboardFlowItemTrait;
use aieuo\mineflow\flowItem\FlowItem;
use aieuo\mineflow\flowItem\FlowItemCategory;
use aieuo\mineflow\flowItem\FlowItemExecutor;
use aieuo\mineflow\flowItem\form\HasSimpleEditForm;
use aieuo\mineflow\flowItem\form\SimpleEditFormBuilder;
use aieuo\mineflow\formAPI\element\mineflow\ExampleInput;
use aieuo\mineflow\formAPI\element\mineflow\ExampleNumberInput;
use aieuo\mineflow\formAPI\element\mineflow\ScoreboardVariableDropdown;
use SOFe\AwaitGenerator\Await;

class SetScoreboardScore extends FlowItem implements ScoreboardFlowItem {
    use ScoreboardFlowItemTrait;
    use ActionNameWithMineflowLanguage;
    use HasSimpleEditForm;

    public function __construct(
        string         $scoreboard = "",
        private string $scoreName = "",
        private string $score = ""
    ) {
        parent::__construct(self::SET_SCOREBOARD_SCORE, FlowItemCategory::SCOREBOARD);

        $this->setScoreboardVariableName($scoreboard);
    }

    public function getDetailDefaultReplaces(): array {
        return ["scoreboard", "name", "score"];
    }

    public function getDetailReplaces(): array {
        return [$this->getScoreboardVariableName(), $this->getScoreName(), $this->getScore()];
    }

    public function getScoreName(): string {
        return $this->scoreName;
    }

    public function setScoreName(string $scoreName): void {
        $this->scoreName = $scoreName;
    }

    public function getScore(): string {
        return $this->score;
    }

    public function setScore(string $score): void {
        $this->score = $score;
    }

    public function isDataValid(): bool {
        return $this->getScoreboardVariableName() !== "" and $this->getScore() !== "";
    }

    protected function onExecute(FlowItemExecutor $source): \Generator {
        $name = $source->replaceVariables($this->getScoreName());
        $score = $this->getInt($source->replaceVariables($this->getScore()));
        $board = $this->getScoreboard($source);

        $board->setScore($name, $score);

        yield Await::ALL;
    }

    public function buildEditForm(SimpleEditFormBuilder $builder, array $variables): void {
        $builder->elements([
            new ScoreboardVariableDropdown($variables, $this->getScoreboardVariableName()),
            new ExampleInput("@action.setScore.form.name", "aieuo", $this->getScoreName(), false),
            new ExampleNumberInput("@action.setScore.form.score", "100", $this->getScore(), true),
        ]);
    }

    public function loadSaveData(array $content): void {
        $this->setScoreboardVariableName($content[0]);
        $this->setScoreName($content[1]);
        $this->setScore($content[2]);
    }

    public function serializeContents(): array {
        return [$this->getScoreboardVariableName(), $this->getScoreName(), $this->getScore()];
    }
}
