<?php

declare(strict_types=1);

namespace Demo\Feature\CourseSubscription\Model;

use Backslash\EventStore\Query\EventClass;
use Backslash\EventStore\Query\Identifier;
use Backslash\EventStore\Query\QueryInterface;
use Backslash\Model\AbstractModel;
use Demo\Feature\CourseCapacity\Event\CourseCapacityChangedEvent;
use Demo\Feature\CourseCreation\Event\CourseDefinedEvent;
use Demo\Feature\CourseCreation\Exception\CourseNotDefinedException;
use Demo\Feature\CourseSubscription\Event\StudentSubscribedToCourseEvent;
use Demo\Feature\CourseSubscription\Event\StudentUnsubscribedFromCourseEvent;
use Demo\Feature\CourseSubscription\Exception\CourseAtFullCapacityException;
use Demo\Feature\CourseSubscription\Exception\StudentAlreadySubscribedToCourseException;
use Demo\Feature\CourseSubscription\Exception\StudentMaximumSubscriptionsReachedException;
use Demo\Feature\CourseSubscription\Exception\StudentNotSubscribedToCourseException;
use Demo\Feature\StudentRegistration\Event\StudentRegisteredEvent;
use Demo\Feature\StudentRegistration\Exception\StudentNotRegisteredException;

class CourseSubscriptionModel extends AbstractModel
{
    private ?string $courseId = null;

    private bool $courseDefined = false;

    private int $courseCapacity = 0;

    private int $courseSubscriptionsCount = 0;

    private ?string $studentId = null;

    private bool $studentRegistered = false;

    private array $studentSubscriptions = [];

    public static function buildQuery(string $studentId, string $courseId): QueryInterface
    {
        $eventForThisCourseLifecycle = EventClass::in(
            CourseCapacityChangedEvent::class,
            CourseDefinedEvent::class,
        )->and(Identifier::is('courseId', $courseId));

        $eventsForThisStudentLifecycle = EventClass::is(
            StudentRegisteredEvent::class,
        )->and(Identifier::is('studentId', $studentId));

        $eventsForThisStudentSubscriptions = EventClass::in(
            StudentUnsubscribedFromCourseEvent::class,
            StudentSubscribedToCourseEvent::class,
        )->and(Identifier::is('studentId', $studentId));

        $eventsForSubscriptionsToThisCourse = EventClass::in(
            StudentSubscribedToCourseEvent::class,
            StudentUnsubscribedFromCourseEvent::class,
        )->and(Identifier::is('courseId', $courseId));

        return $eventForThisCourseLifecycle
            ->or($eventsForThisStudentLifecycle)
            ->or($eventsForThisStudentSubscriptions)
            ->or($eventsForSubscriptionsToThisCourse);
    }

    public function subscribe(string $studentId, string $courseId): void
    {
        $this->assertCourseIsDefined();
        $this->assertStudentIsRegistered();
        $this->assertStudentIsNotSubscribedToCourse();
        $this->assertCourseIsNotAtFullCapacity();
        $this->assertStudentHasNotReachedMaxSubscriptionCount();

        $this->record(new StudentSubscribedToCourseEvent($studentId, $courseId));
    }

    public function unsubscribe(string $studentId, string $courseId): void
    {
        $this->assertCourseIsDefined();
        $this->assertStudentIsRegistered();
        $this->assertStudentIsSubscribedToCourse();

        $this->record(new StudentUnsubscribedFromCourseEvent($studentId, $courseId));
    }

    protected function applyCourseDefinedEvent(CourseDefinedEvent $event): void
    {
        $this->courseId = $event->courseId;
        $this->courseDefined = true;
        $this->courseCapacity = $event->capacity;
    }

    protected function applyCourseCapacityChangedEvent(CourseCapacityChangedEvent $event): void
    {
        $this->courseCapacity = $event->new;
    }

    protected function applyStudentSubscribedToCourseEvent(StudentSubscribedToCourseEvent $event): void
    {
        if ($event->courseId === $this->courseId) {
            $this->courseSubscriptionsCount++;
        }
        if ($event->studentId === $this->studentId) {
            $this->studentSubscriptions[$event->courseId] = $event->courseId;
        }
    }

    protected function applyStudentUnsubscribedFromCourseEvent(StudentUnsubscribedFromCourseEvent $event): void
    {
        if ($event->courseId === $this->courseId) {
            $this->courseSubscriptionsCount--;
        }
        if ($event->studentId === $this->studentId) {
            unset($this->studentSubscriptions[$event->courseId]);
        }
    }

    protected function applyStudentRegisteredEvent(StudentRegisteredEvent $event): void
    {
        $this->studentId = $event->studentId;
        $this->studentRegistered = true;
    }

    private function assertStudentIsRegistered(): void
    {
        if (!$this->studentRegistered) {
            throw new StudentNotRegisteredException();
        }
    }

    private function assertCourseIsDefined(): void
    {
        if (!$this->courseDefined) {
            throw new CourseNotDefinedException();
        }
    }

    private function assertCourseIsNotAtFullCapacity(): void
    {
        if ($this->courseCapacity <= $this->courseSubscriptionsCount) {
            throw new CourseAtFullCapacityException();
        }
    }

    private function assertStudentHasNotReachedMaxSubscriptionCount(): void
    {
        if (count($this->studentSubscriptions) >= 3) {
            throw new StudentMaximumSubscriptionsReachedException();
        }
    }

    private function assertStudentIsSubscribedToCourse(): void
    {
        if (!array_key_exists($this->courseId, $this->studentSubscriptions)) {
            throw new StudentNotSubscribedToCourseException();
        }
    }

    private function assertStudentIsNotSubscribedToCourse(): void
    {
        if (array_key_exists($this->courseId, $this->studentSubscriptions)) {
            throw new StudentAlreadySubscribedToCourseException();
        }
    }
}
