<?php

declare(strict_types=1);

namespace Demo\Domain\State;

use Backslash\Domain\AbstractState;
use Backslash\EventStore\Query\EventClass;
use Backslash\EventStore\Query\QueryInterface;
use Demo\Domain\Event\EnrollmentPeriodClosedEvent;
use Demo\Domain\Event\EnrollmentPeriodOpenedEvent;
use RuntimeException;

class EnrollmentPeriodState extends AbstractState
{
    private bool $open = false;

    public static function getQuery(): QueryInterface
    {
        return EventClass::in(
            EnrollmentPeriodOpenedEvent::class,
            EnrollmentPeriodClosedEvent::class,
        );
    }

    public function open(): void
    {
        if ($this->open) {
            throw new RuntimeException('Enrollment period is already open.');
        }
        $this->apply(new EnrollmentPeriodOpenedEvent());
    }

    public function close(): void
    {
        if (!$this->open) {
            throw new RuntimeException('Enrollment period is already close.');
        }
        $this->apply(new EnrollmentPeriodClosedEvent());
    }

    protected function applyEnrollmentPeriodOpenedEvent(EnrollmentPeriodOpenedEvent $event): void
    {
        $this->open = true;
    }

    protected function applyEnrollmentPeriodClosedEvent(EnrollmentPeriodClosedEvent $event): void
    {
        $this->open = false;
    }
}
