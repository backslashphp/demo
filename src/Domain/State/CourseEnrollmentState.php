<?php

declare(strict_types=1);

namespace Demo\Domain\State;

use Backslash\Domain\AbstractState;
use Backslash\EventStore\Query\EventClass;
use Backslash\EventStore\Query\Identifier;
use Backslash\EventStore\Query\QueryInterface;
use Demo\Domain\Event\CourseCanceledEvent;
use Demo\Domain\Event\CourseCapacityChangedEvent;
use Demo\Domain\Event\CourseCreatedEvent;
use Demo\Domain\Event\EnrollmentPeriodClosedEvent;
use Demo\Domain\Event\EnrollmentPeriodOpenedEvent;
use Demo\Domain\Event\StudentWithdrawnFromCourseEvent;
use Demo\Domain\Event\StudentEnrolledInCourseEvent;
use Demo\Domain\Event\StudentRegisteredEvent;
use Exception;

class CourseEnrollmentState extends AbstractState
{
    private ?string $studentId = null;

    private bool $studentExists = false;

    private array $studentEnrollments = [];

    private ?string $courseId = null;

    private bool $courseExists = false;

    private bool $courseCanceled = false;

    private int $courseEnrollmentCount = 0;

    private int $courseCapacity = 0;

    private bool $enrollmentPeriodOpened = false;

    public static function getQuery(string $studentId, string $courseId): QueryInterface
    {
        $eventsForEnrollmentPeriod = EventClass::in(
            EnrollmentPeriodOpenedEvent::class,
            EnrollmentPeriodClosedEvent::class,
        );

        $eventForThisCourseLifecycle = EventClass::in(
            CourseCanceledEvent::class,
            CourseCanceledEvent::class,
            CourseCapacityChangedEvent::class,
            CourseCreatedEvent::class,
        )->and(Identifier::is('courseId', $courseId));

        $eventsForThisStudentLifecycle = EventClass::is(
            StudentRegisteredEvent::class,
        )->and(Identifier::is('studentId', $studentId));

        $eventsForEnrollmentOfThisStudent = EventClass::in(
            StudentWithdrawnFromCourseEvent::class,
            StudentEnrolledInCourseEvent::class,
        )->and(Identifier::is('studentId', $studentId));

        $eventsForEnrollmentInThisCourse = EventClass::in(
            StudentWithdrawnFromCourseEvent::class,
            StudentEnrolledInCourseEvent::class,
        )->and(Identifier::is('courseId', $courseId));

        return $eventsForEnrollmentPeriod
            ->or($eventForThisCourseLifecycle)
            ->or($eventsForThisStudentLifecycle)
            ->or($eventsForEnrollmentOfThisStudent)
            ->or($eventsForEnrollmentInThisCourse);
    }

    public function enroll(string $studentId, string $courseId): void
    {
        $this->assertStudentExists();
        $this->assertCourseExists();
        $this->assertStudentNotEnrolledInCourse();
        $this->assertEnrollmentPeriodOpened();
        $this->assertCourseNotCanceled();
        $this->assertCourseCapacityNotExceeded();
        $this->assertStudentNotEnrolledInMaximumCourses();

        $this->apply(new StudentEnrolledInCourseEvent($studentId, $courseId));
    }

    public function withdraw(string $studentId, string $courseId): void
    {
        $this->assertStudentExists();
        $this->assertCourseExists();
        $this->assertStudentEnrolledInCourse();

        $this->apply(new StudentWithdrawnFromCourseEvent($studentId, $courseId));
    }

    protected function applyCourseCreatedEvent(CourseCreatedEvent $event): void
    {
        $this->courseId = $event->courseId;
        $this->courseExists = true;
        $this->courseCapacity = $event->capacity;
    }

    protected function applyCourseCanceledEvent(CourseCanceledEvent $event): void
    {
        $this->courseCanceled = true;
    }

    protected function applyCourseCapacityChangedEvent(CourseCapacityChangedEvent $event): void
    {
        $this->courseCapacity = $event->new;
    }

    protected function applyStudentWithdrawnFromCourseEvent(StudentWithdrawnFromCourseEvent $event): void
    {
        if ($event->courseId === $this->courseId) {
            $this->courseEnrollmentCount--;
        }
        if ($event->studentId === $this->studentId) {
            unset($this->studentEnrollments[$event->courseId]);
        }
    }

    protected function applyStudentEnrolledInCourseEvent(StudentEnrolledInCourseEvent $event): void
    {
        if ($event->courseId === $this->courseId) {
            $this->courseEnrollmentCount++;
        }
        if ($event->studentId === $this->studentId) {
            $this->studentEnrollments[$event->courseId] = $event->courseId;
        }
    }

    protected function applyStudentRegisteredEvent(StudentRegisteredEvent $event): void
    {
        $this->studentId = $event->studentId;
        $this->studentExists = true;
    }

    protected function applyEnrollmentPeriodEndedEvent(EnrollmentPeriodClosedEvent $event): void
    {
        $this->enrollmentPeriodOpened = false;
    }

    protected function applyEnrollmentPeriodOpenedEvent(EnrollmentPeriodOpenedEvent $event): void
    {
        $this->enrollmentPeriodOpened = true;
    }

    private function assertStudentExists(): void
    {
        if (!$this->studentExists) {
            throw new Exception('Student does not exist.');
        }
    }

    private function assertCourseExists(): void
    {
        if (!$this->courseExists) {
            throw new Exception('Course does not exist.');
        }
    }

    private function assertEnrollmentPeriodOpened(): void
    {
        if (!$this->enrollmentPeriodOpened) {
            throw new Exception('Enrollment period is close.');
        }
    }

    private function assertCourseNotCanceled(): void
    {
        if ($this->courseCanceled) {
            throw new Exception('Course is canceled.');
        }
    }

    private function assertCourseCapacityNotExceeded(): void
    {
        if ($this->courseCapacity <= $this->courseEnrollmentCount) {
            throw new Exception('Course cannot take more students than its capacity.');
        }
    }

    private function assertStudentNotEnrolledInMaximumCourses(): void
    {
        if (count($this->studentEnrollments) >= 3) {
            throw new Exception('Student cannot enroll in more than 3 courses.');
        }
    }

    private function assertStudentEnrolledInCourse(): void
    {
        if (!array_key_exists($this->courseId, $this->studentEnrollments)) {
            throw new Exception('Student is not enrolled in course.');
        }
    }

    private function assertStudentNotEnrolledInCourse(): void
    {
        if (array_key_exists($this->courseId, $this->studentEnrollments)) {
            throw new Exception('Student is already enrolled in course.');
        }
    }
}
