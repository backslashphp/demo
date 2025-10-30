<?php

declare(strict_types=1);

namespace Demo\Feature\StudentRegistration\Model;

use Backslash\EventStore\Query\EventClass;
use Backslash\EventStore\Query\Identifier;
use Backslash\EventStore\Query\QueryInterface;
use Backslash\Model\AbstractModel;
use Demo\Feature\StudentRegistration\Event\StudentRegisteredEvent;
use Demo\Feature\StudentRegistration\Exception\InvalidStudentIdException;
use Demo\Feature\StudentRegistration\Exception\StudentIdAlreadyUsedException;

class StudentRegistrationModel extends AbstractModel
{
    private bool $registered = false;

    public static function buildQuery(string $studentId): QueryInterface
    {
        return EventClass::is(StudentRegisteredEvent::class)
            ->and(
                Identifier::is('studentId', $studentId),
            );
    }

    public function register(string $studentId, string $name): void
    {
        if ($this->registered) {
            throw new StudentIdAlreadyUsedException();
        }
        if (!ctype_digit($studentId)) {
            throw new InvalidStudentIdException();
        }
        $this->record(new StudentRegisteredEvent($studentId, $name));
    }

    protected function applyStudentRegisteredEvent(StudentRegisteredEvent $event): void
    {
        $this->registered = true;
    }
}
