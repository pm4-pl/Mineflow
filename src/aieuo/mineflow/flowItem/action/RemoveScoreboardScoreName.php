<?php

namespace aieuo\mineflow\flowItem\action;

use aieuo\mineflow\flowItem\base\ScoreboardFlowItem;
use aieuo\mineflow\flowItem\base\ScoreboardFlowItemTrait;
use aieuo\mineflow\formAPI\CustomForm;
use aieuo\mineflow\formAPI\element\Input;
use aieuo\mineflow\formAPI\element\Label;
use aieuo\mineflow\formAPI\element\Toggle;
use aieuo\mineflow\formAPI\Form;
use aieuo\mineflow\Main;
use aieuo\mineflow\utils\Language;
use aieuo\mineflow\recipe\Recipe;
use aieuo\mineflow\utils\Category;

class RemoveScoreboardScoreName extends Action implements ScoreboardFlowItem {
    use ScoreboardFlowItemTrait;

    protected $id = self::REMOVE_SCOREBOARD_SCORE_NAME;

    protected $name = "action.removeScoreName.name";
    protected $detail = "action.removeScoreName.detail";
    protected $detailDefaultReplace = ["scoreboard", "score"];

    protected $category = Category::SCOREBOARD;

    protected $targetRequired = Recipe::TARGET_REQUIRED_NONE;

    /* @var string */
    private $score;

    public function __construct(string $scoreboard = "board", string $score = "") {
        $this->setScoreboardVariableName($scoreboard);
        $this->score = $score;
    }

    public function getScore(): string {
        return $this->score;
    }

    public function setScore(string $score): void {
        $this->score = $score;
    }

    public function getDetail(): string {
        if (!$this->isDataValid()) return $this->getName();
        return Language::get($this->detail, [$this->getScoreboardVariableName(), $this->getScore()]);
    }

    public function isDataValid(): bool {
        return $this->getScoreboardVariableName() !== "" and $this->getScore() !== "";
    }

    public function execute(Recipe $origin): bool {
        $this->throwIfCannotExecute();

        $score = $origin->replaceVariables($this->getScore());

        $this->throwIfInvalidNumber($score);

        $board = $this->getScoreboard($origin);
        $this->throwIfInvalidScoreboard($board);

        $board->removeScoreName((int)$score);
        return true;
    }

    public function getEditForm(array $default = [], array $errors = []): Form {
        return (new CustomForm($this->getName()))
            ->setContents([
                new Label($this->getDescription()),
                new Input("@flowItem.form.target.scoreboard", Language::get("form.example", ["board"]), $default[1] ?? $this->getScoreboardVariableName()),
                new Input("@action.setScore.form.score", Language::get("form.example", ["100"]), $default[3] ?? $this->getScore()),
                new Toggle("@form.cancelAndBack")
            ])->addErrors($errors);
    }

    public function parseFromFormData(array $data): array {
        $errors = [];
        if ($data[1] === "") $data[1] = "board";
        if ($data[2] === "") $errors[] = ["@form.insufficient", 2];
        if (!is_numeric($data[3]) and !Main::getVariableHelper()->containsVariable($data[2])) {
            $errors[] = ["@flowItem.error.notNumber", 2];
        }
        return ["contents" => [$data[1], $data[2]], "cancel" => $data[3], "errors" => []];
    }

    public function loadSaveData(array $content): Action {
        if (!isset($content[1])) throw new \OutOfBoundsException();
        $this->setScoreboardVariableName($content[0]);
        $this->setScore($content[1]);
        return $this;
    }

    public function serializeContents(): array {
        return [$this->getScoreboardVariableName(), $this->getScore()];
    }
}
