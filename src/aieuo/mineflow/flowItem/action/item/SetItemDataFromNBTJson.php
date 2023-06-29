<?php

declare(strict_types=1);

namespace aieuo\mineflow\flowItem\action\item;

use aieuo\mineflow\exception\InvalidFlowValueException;
use aieuo\mineflow\flowItem\argument\ItemArgument;
use aieuo\mineflow\flowItem\argument\StringArgument;
use aieuo\mineflow\flowItem\base\ActionNameWithMineflowLanguage;
use aieuo\mineflow\flowItem\FlowItem;
use aieuo\mineflow\flowItem\FlowItemCategory;
use aieuo\mineflow\flowItem\FlowItemExecutor;
use aieuo\mineflow\flowItem\form\HasSimpleEditForm;
use aieuo\mineflow\flowItem\form\SimpleEditFormBuilder;
use aieuo\mineflow\Main;
use aieuo\mineflow\Mineflow;
use aieuo\mineflow\utils\Language;
use pocketmine\nbt\JsonNbtParser;
use pocketmine\nbt\NbtException;
use SOFe\AwaitGenerator\Await;

class SetItemDataFromNBTJson extends FlowItem {
    use ActionNameWithMineflowLanguage;
    use HasSimpleEditForm;

    protected string $returnValueType = self::RETURN_VARIABLE_NAME;

    private ItemArgument $item;
    private StringArgument $json;

    public function __construct(string $item = "", string $json = "") {
        parent::__construct(self::SET_ITEM_DATA_FROM_NBT_JSON, FlowItemCategory::ITEM);

        $this->item = new ItemArgument("item", $item);
        $this->json = new StringArgument("json", $json, "@action.setItemData.form.value", example: "{display:{Lore:}");
    }

    public function getDetailDefaultReplaces(): array {
        return [$this->item->getName(), "json"];
    }

    public function getDetailReplaces(): array {
        return [$this->item->get(), $this->json->get()];
    }

    public function getItem(): ItemArgument {
        return $this->item;
    }

    public function getJson(): StringArgument {
        return $this->json;
    }

    public function isDataValid(): bool {
        return $this->item->isNotEmpty() and $this->json->isNotEmpty();
    }

    protected function onExecute(FlowItemExecutor $source): \Generator {
        $item = $this->item->getItem($source);
        $json = $this->json->getRawString();

        try {
            $tags = JsonNbtParser::parseJson($json);
            $item->setNamedTag($tags);
        } catch (\UnexpectedValueException|NbtException $e) {
            if (Mineflow::isDebug()) Main::getInstance()->getLogger()->logException($e);
            throw new InvalidFlowValueException(Language::get("variable.convert.nbt.failed", [$e->getMessage(), $json]));
        }

        yield Await::ALL;
        return $this->item->get();
    }

    public function buildEditForm(SimpleEditFormBuilder $builder, array $variables): void {
        $builder->elements([
            $this->item->createFormElement($variables),
            $this->json->createFormElement($variables),
        ]);
    }

    public function loadSaveData(array $content): void {
        $this->item->set($content[0]);
        $this->json->set($content[1]);
    }

    public function serializeContents(): array {
        return [$this->item->get(), $this->json->get()];
    }
}
