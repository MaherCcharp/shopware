<?php declare(strict_types=1);

namespace Shopware\Framework\Event;

class OrderStateTranslationWrittenEvent extends NestedEvent
{
    const NAME = 'order_state_translation.written';

    /**
     * @var string[]
     */
    private $orderStateTranslationUuids;

    /**
     * @var NestedEventCollection
     */
    private $events;

    /**
     * @var array
     */
    private $errors;

    public function __construct(array $orderStateTranslationUuids, array $errors = [])
    {
        $this->orderStateTranslationUuids = $orderStateTranslationUuids;
        $this->events = new NestedEventCollection();
        $this->errors = $errors;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @return string[]
     */
    public function getOrderStateTranslationUuids(): array
    {
        return $this->orderStateTranslationUuids;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return count($this->errors) > 0;
    }

    public function addEvent(NestedEvent $event): void
    {
        $this->events->add($event);
    }

    public function getEvents(): NestedEventCollection
    {
        return $this->events;
    }
}