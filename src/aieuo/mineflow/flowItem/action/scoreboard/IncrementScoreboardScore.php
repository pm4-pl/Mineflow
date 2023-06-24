<?php

declare(strict_types=1);

namespace aieuo\mineflow\flowItem\action\scoreboard;

use aieuo\mineflow\flowItem\base\ActionNameWithMineflowLanguage;
use aieuo\mineflow\flowItem\FlowItem;
use aieuo\mineflow\flowItem\FlowItemCategory;
use aieuo\mineflow\flowItem\FlowItemExecutor;
use aieuo\mineflow\flowItem\form\HasSimpleEditForm;
use aieuo\mineflow\flowItem\form\SimpleEditFormBuilder;
use aieuo\mineflow\flowItem\argument\ScoreboardArgument;
use aieuo\mineflow\formAPI\element\mineflow\ExampleInput;
use aieuo\mineflow\formAPI\element\mineflow\ExampleNumberInput;
use SOFe\AwaitGenerator\Await;

class IncrementScoreboardScore extends FlowItem {
    use ActionNameWithMineflowLanguage;
    use HasSimpleEditForm;

    private ScoreboardArgument $scoreboard;

    public function __construct(
        string         $scoreboard = "",
        private string $scoreName = "",
        private string $score = ""
    ) {
        parent::__construct(self::INCREMENT_SCOREBOARD_SCORE, FlowItemCategory::SCOREBOARD);

        $this->scoreboard = new ScoreboardArgument("scoreboard", $scoreboard);
    }

    public function getDetailDefaultReplaces(): array {
        return [$this->scoreboard->getName(), "name", "score"];
    }

    public function getDetailReplaces(): array {
        return [$this->scoreboard->get(), $this->getScoreName(), $this->getScore()];
    }

    public function getScoreboard(): ScoreboardArgument {
        return $this->scoreboard;
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
        return $this->scoreboard->isNotEmpty() and $this->getScore() !== "";
    }

    protected function onExecute(FlowItemExecutor $source): \Generator {
        $name = $source->replaceVariables($this->getScoreName());
        $score = $this->getInt($source->replaceVariables($this->getScore()));
        $board = $this->scoreboard->getScoreboard($source);

        $board->setScore($name, ($board->getScore($name) ?? 0) + $score);

        yield Await::ALL;
    }

    public function buildEditForm(SimpleEditFormBuilder $builder, array $variables): void {
        $builder->elements([
            $this->scoreboard->createFormElement($variables),
            new ExampleInput("@action.setScore.form.name", "aieuo", $this->getScoreName(), false),
            new ExampleNumberInput("@action.setScore.form.score", "100", $this->getScore(), true),
        ]);
    }

    public function loadSaveData(array $content): void {
        $this->scoreboard->set($content[0]);
        $this->setScoreName($content[1]);
        $this->setScore($content[2]);
    }

    public function serializeContents(): array {
        return [$this->scoreboard->get(), $this->getScoreName(), $this->getScore()];
    }
}
