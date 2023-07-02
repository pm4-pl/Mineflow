<?php

declare(strict_types=1);

namespace aieuo\mineflow\flowItem\action\config;

use aieuo\mineflow\flowItem\base\ActionNameWithMineflowLanguage;
use aieuo\mineflow\flowItem\base\ConfigFileFlowItem;
use aieuo\mineflow\flowItem\base\ConfigFileFlowItemTrait;
use aieuo\mineflow\flowItem\FlowItem;
use aieuo\mineflow\flowItem\FlowItemCategory;
use aieuo\mineflow\flowItem\FlowItemExecutor;
use aieuo\mineflow\flowItem\FlowItemPermission;
use aieuo\mineflow\flowItem\form\HasSimpleEditForm;
use aieuo\mineflow\flowItem\form\SimpleEditFormBuilder;
use aieuo\mineflow\formAPI\element\mineflow\ConfigVariableDropdown;
use SOFe\AwaitGenerator\Await;

class SaveConfigFile extends FlowItem implements ConfigFileFlowItem {
    use ConfigFileFlowItemTrait;
    use ActionNameWithMineflowLanguage;
    use HasSimpleEditForm;

    public function __construct(string $config = "") {
        parent::__construct(self::SAVE_CONFIG_FILE, FlowItemCategory::CONFIG, [FlowItemPermission::CONFIG]);

        $this->setConfigVariableName($config);
    }

    public function getDetailDefaultReplaces(): array {
        return ["config"];
    }

    public function getDetailReplaces(): array {
        return [$this->getConfigVariableName()];
    }

    public function isDataValid(): bool {
        return $this->getConfigVariableName() !== "";
    }

    protected function onExecute(FlowItemExecutor $source): \Generator {
        $config = $this->getConfig($source);
        $config->save();

        yield Await::ALL;
    }

    public function buildEditForm(SimpleEditFormBuilder $builder, array $variables): void {
        $builder->elements([
            new ConfigVariableDropdown($variables, $this->getConfigVariableName()),
        ]);
    }

    public function loadSaveData(array $content): void {
        $this->setConfigVariableName($content[0]);
    }

    public function serializeContents(): array {
        return [$this->getConfigVariableName()];
    }
}
