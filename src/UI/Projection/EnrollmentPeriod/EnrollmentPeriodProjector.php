<?php

declare(strict_types=1);

namespace Demo\UI\Projection\EnrollmentPeriod;

use Backslash\ProjectionStore\ProjectionNotFoundException;
use Backslash\ProjectionStore\ProjectionStoreInterface;
use Demo\Application\AbstractEventHandler;
use Demo\Domain\Event\EnrollmentPeriodClosedEvent;
use Demo\Domain\Event\EnrollmentPeriodOpenedEvent;

class EnrollmentPeriodProjector extends AbstractEventHandler
{
    private ProjectionStoreInterface $projections;

    public function __construct(ProjectionStoreInterface $projections)
    {
        $this->projections = $projections;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            EnrollmentPeriodClosedEvent::class,
            EnrollmentPeriodOpenedEvent::class,
        ];
    }

    protected function handleEnrollmentPeriodEndedEvent(EnrollmentPeriodClosedEvent $event): void
    {
        $period = $this->getPeriod();
        $period->close();
        $this->projections->store($period);
    }

    protected function handleEnrollmentPeriodOpenedEvent(EnrollmentPeriodOpenedEvent $event): void
    {
        $period = $this->getPeriod();
        $period->open();
        $this->projections->store($period);
    }

    private function getPeriod(): EnrollmentPeriodProjection
    {
        try {
            /** @var EnrollmentPeriodProjection $p */
            $p = $this->projections->find(EnrollmentPeriodProjection::ID, EnrollmentPeriodProjection::class);
        } catch (ProjectionNotFoundException) {
            $p = new EnrollmentPeriodProjection();
        }
        return $p;
    }
}
