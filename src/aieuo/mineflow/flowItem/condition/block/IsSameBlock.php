<?php
declare(strict_types=1);


namespace aieuo\mineflow\flowItem\condition\block;

use aieuo\mineflow\flowItem\argument\BlockArgument;
use aieuo\mineflow\flowItem\base\ConditionNameWithMineflowLanguage;
use aieuo\mineflow\flowItem\condition\Condition;
use aieuo\mineflow\flowItem\FlowItem;
use aieuo\mineflow\flowItem\FlowItemCategory;
use aieuo\mineflow\flowItem\FlowItemExecutor;
use aieuo\mineflow\flowItem\FlowItemIds;
use aieuo\mineflow\flowItem\form\HasSimpleEditForm;
use aieuo\mineflow\flowItem\form\SimpleEditFormBuilder;
use SOFe\AwaitGenerator\GeneratorUtil;

class IsSameBlock extends FlowItem implements Condition {
    use ConditionNameWithMineflowLanguage;
    use HasSimpleEditForm;

    private BlockArgument $block1;
    private BlockArgument $block2;

    public function __construct(string $block1 = "", string $block2 = "") {
        parent::__construct(FlowItemIds::IS_SAME_BLOCk, FlowItemCategory::BLOCK);

        $this->block1 = new BlockArgument("block1", $block1, "@action.form.target.block (1)");
        $this->block2 = new BlockArgument("block2", $block2, "@action.form.target.block (2)");
    }

    public function getDetailDefaultReplaces(): array {
        return [$this->block1->getName(), $this->block2->getName()];
    }

    public function getDetailReplaces(): array {
        return [$this->block1->get(), $this->block2->get()];
    }

    public function getBlock1(): BlockArgument {
        return $this->block1;
    }

    public function getBlock2(): BlockArgument {
        return $this->block2;
    }

    public function isDataValid(): bool {
        return $this->block1->isNotEmpty() and $this->block2->isNotEmpty();
    }

    protected function onExecute(FlowItemExecutor $source): \Generator {
        $block1 = $this->block1->getBlock($source);
        $block2 = $this->block2->getBlock($source);

        yield from GeneratorUtil::empty();

        return $block1->isSameState($block2);
    }

    public function buildEditForm(SimpleEditFormBuilder $builder, array $variables): void {
        $builder->elements([
            $this->block1->createFormElement($variables),
            $this->block2->createFormElement($variables),
        ]);
    }

    public function loadSaveData(array $content): void {
        $this->block1->set($content[0]);
        $this->block2->set($content[1]);
    }

    public function serializeContents(): array {
        return [$this->block1->get(), $this->block2->get()];
    }
}
