<?php

declare(strict_types=1);

namespace Demo\Domain\State;

use Backslash\Domain\AbstractState;
use Backslash\EventStore\Query\EventClass;
use Backslash\EventStore\Query\Identifier;
use Backslash\EventStore\Query\QueryInterface;
use Demo\Domain\Event\StudentRegisteredEvent;
use RuntimeException;
use UnexpectedValueException;

class StudentRegistrationState extends AbstractState
{
    private bool $registered = false;

    public static function getQuery(string $studentId): QueryInterface
    {
        return EventClass::is(StudentRegisteredEvent::class)
            ->and(
                Identifier::is('studentId', $studentId),
            );
    }

    public function register(string $studentId, string $name): void
    {
        if ($this->registered) {
            throw new RuntimeException('ID already used.');
        }
        if (!Util::isNumber($studentId)) {
            throw new UnexpectedValueException('ID must be an number.');
        }
        $this->apply(new StudentRegisteredEvent($studentId, $name));
    }

    protected function applyStudentRegisteredEvent(StudentRegisteredEvent $event): void
    {
        $this->registered = true;
    }
}
