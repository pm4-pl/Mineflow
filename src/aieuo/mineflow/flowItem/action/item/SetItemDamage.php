<?php

declare(strict_types=1);

namespace aieuo\mineflow\flowItem\action\item;

use aieuo\mineflow\flowItem\argument\ItemArgument;
use aieuo\mineflow\flowItem\argument\NumberArgument;
use aieuo\mineflow\flowItem\base\ActionNameWithMineflowLanguage;
use aieuo\mineflow\flowItem\FlowItem;
use aieuo\mineflow\flowItem\FlowItemCategory;
use aieuo\mineflow\flowItem\FlowItemExecutor;
use aieuo\mineflow\flowItem\form\HasSimpleEditForm;
use aieuo\mineflow\flowItem\form\SimpleEditFormBuilder;
use pocketmine\world\format\io\GlobalItemDataHandlers;
use SOFe\AwaitGenerator\Await;

class SetItemDamage extends FlowItem {
    use ActionNameWithMineflowLanguage;
    use HasSimpleEditForm;

    protected string $returnValueType = self::RETURN_VARIABLE_NAME;

    private ItemArgument $item;
    private NumberArgument $damage;

    public function __construct(string $item = "", int $damage = null) {
        parent::__construct(self::SET_ITEM_DAMAGE, FlowItemCategory::ITEM);

        $this->item = new ItemArgument("item", $item);
        $this->damage = new NumberArgument("damage", $damage, example: "0", min: 0);
    }

    public function getDetailDefaultReplaces(): array {
        return [$this->item->getName(), "damage"];
    }

    public function getDetailReplaces(): array {
        return [$this->item->get(), $this->damage->get()];
    }

    public function getItem(): ItemArgument {
        return $this->item;
    }

    public function getDamage(): NumberArgument {
        return $this->damage;
    }

    public function isDataValid(): bool {
        return $this->item->isNotEmpty() and $this->damage->isNotEmpty();
    }

    protected function onExecute(FlowItemExecutor $source): \Generator {
        $damage = $this->damage->getInt($source);
        $item = $this->item->getItem($source);

        $itemType = GlobalItemDataHandlers::getSerializer()->serializeType($item);
        $itemStack = GlobalItemDataHandlers::getUpgrader()->upgradeItemTypeDataString($itemType->getName(), $damage, $item->getCount(), $item->getNamedTag());
        $newItem = GlobalItemDataHandlers::getDeserializer()->deserializeStack($itemStack);
        $this->item->getItemVariable($source)->setItem($newItem);

        yield Await::ALL;
        return $this->item->get();
    }

    public function buildEditForm(SimpleEditFormBuilder $builder, array $variables): void {
        $builder->elements([
            $this->item->createFormElement($variables),
            $this->damage->createFormElement($variables),
        ]);
    }

    public function loadSaveData(array $content): void {
        $this->item->set($content[0]);
        $this->damage->set($content[1]);
    }

    public function serializeContents(): array {
        return [$this->item->get(), $this->damage->get()];
    }
}
