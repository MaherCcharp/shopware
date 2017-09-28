<?php declare(strict_types=1);

namespace Shopware\Shop\Writer\Resource;

use Shopware\Context\Struct\TranslationContext;
use Shopware\Framework\Write\Field\BoolField;
use Shopware\Framework\Write\Field\FkField;
use Shopware\Framework\Write\Field\IntField;
use Shopware\Framework\Write\Field\ReferenceField;
use Shopware\Framework\Write\Field\StringField;
use Shopware\Framework\Write\Field\SubresourceField;
use Shopware\Framework\Write\Field\TranslatedField;
use Shopware\Framework\Write\Field\UuidField;
use Shopware\Framework\Write\Flag\Required;
use Shopware\Framework\Write\Resource;

class ShopFormFieldResource extends Resource
{
    protected const UUID_FIELD = 'uuid';
    protected const SHOP_FORM_ID_FIELD = 'shopFormId';
    protected const ERROR_MSG_FIELD = 'errorMsg';
    protected const NAME_FIELD = 'name';
    protected const NOTE_FIELD = 'note';
    protected const TYPE_FIELD = 'type';
    protected const REQUIRED_FIELD = 'required';
    protected const LABEL_FIELD = 'label';
    protected const CLASS_FIELD = 'class';
    protected const VALUE_FIELD = 'value';
    protected const POSITION_FIELD = 'position';
    protected const TICKET_TASK_FIELD = 'ticketTask';

    public function __construct()
    {
        parent::__construct('shop_form_field');

        $this->primaryKeyFields[self::UUID_FIELD] = (new UuidField('uuid'))->setFlags(new Required());
        $this->fields[self::SHOP_FORM_ID_FIELD] = (new IntField('shop_form_id'))->setFlags(new Required());
        $this->fields[self::ERROR_MSG_FIELD] = (new StringField('error_msg'))->setFlags(new Required());
        $this->fields[self::NAME_FIELD] = (new StringField('name'))->setFlags(new Required());
        $this->fields[self::NOTE_FIELD] = new StringField('note');
        $this->fields[self::TYPE_FIELD] = (new StringField('type'))->setFlags(new Required());
        $this->fields[self::REQUIRED_FIELD] = (new BoolField('required'))->setFlags(new Required());
        $this->fields[self::LABEL_FIELD] = (new StringField('label'))->setFlags(new Required());
        $this->fields[self::CLASS_FIELD] = (new StringField('class'))->setFlags(new Required());
        $this->fields[self::VALUE_FIELD] = (new StringField('value'))->setFlags(new Required());
        $this->fields[self::POSITION_FIELD] = (new IntField('position'))->setFlags(new Required());
        $this->fields[self::TICKET_TASK_FIELD] = (new StringField('ticket_task'))->setFlags(new Required());
        $this->fields['shopForm'] = new ReferenceField('shopFormUuid', 'uuid', \Shopware\Shop\Writer\Resource\ShopFormResource::class);
        $this->fields['shopFormUuid'] = (new FkField('shop_form_uuid', \Shopware\Shop\Writer\Resource\ShopFormResource::class, 'uuid'))->setFlags(new Required());
        $this->fields[self::NAME_FIELD] = new TranslatedField('name', \Shopware\Shop\Writer\Resource\ShopResource::class, 'uuid');
        $this->fields[self::NOTE_FIELD] = new TranslatedField('note', \Shopware\Shop\Writer\Resource\ShopResource::class, 'uuid');
        $this->fields[self::LABEL_FIELD] = new TranslatedField('label', \Shopware\Shop\Writer\Resource\ShopResource::class, 'uuid');
        $this->fields[self::VALUE_FIELD] = new TranslatedField('value', \Shopware\Shop\Writer\Resource\ShopResource::class, 'uuid');
        $this->fields['translations'] = (new SubresourceField(\Shopware\Shop\Writer\Resource\ShopFormFieldTranslationResource::class, 'languageUuid'))->setFlags(new Required());
    }

    public function getWriteOrder(): array
    {
        return [
            \Shopware\Shop\Writer\Resource\ShopFormResource::class,
            \Shopware\Shop\Writer\Resource\ShopFormFieldResource::class,
            \Shopware\Shop\Writer\Resource\ShopFormFieldTranslationResource::class,
        ];
    }

    public static function createWrittenEvent(array $updates, TranslationContext $context, array $errors = []): \Shopware\Shop\Event\ShopFormFieldWrittenEvent
    {
        $event = new \Shopware\Shop\Event\ShopFormFieldWrittenEvent($updates[self::class] ?? [], $context, $errors);

        unset($updates[self::class]);

        if (!empty($updates[\Shopware\Shop\Writer\Resource\ShopFormResource::class])) {
            $event->addEvent(\Shopware\Shop\Writer\Resource\ShopFormResource::createWrittenEvent($updates, $context));
        }

        if (!empty($updates[\Shopware\Shop\Writer\Resource\ShopFormFieldResource::class])) {
            $event->addEvent(\Shopware\Shop\Writer\Resource\ShopFormFieldResource::createWrittenEvent($updates, $context));
        }

        if (!empty($updates[\Shopware\Shop\Writer\Resource\ShopFormFieldTranslationResource::class])) {
            $event->addEvent(\Shopware\Shop\Writer\Resource\ShopFormFieldTranslationResource::createWrittenEvent($updates, $context));
        }

        return $event;
    }
}