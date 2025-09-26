<?php

declare(strict_types=1);

namespace Demo\Domain\Model;

use Backslash\Model\AbstractModel;
use Backslash\EventStore\Query\EventClass;
use Backslash\EventStore\Query\Identifier;
use Backslash\EventStore\Query\QueryInterface;
use Demo\Domain\Event\StudentRegisteredEvent;
use Demo\Domain\Exception\IdAlreadyUsedException;
use Demo\Domain\Exception\InvalidIdException;

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
            throw new IdAlreadyUsedException();
        }
        if (!Util::isNumber($studentId)) {
            throw new InvalidIdException();
        }
        $this->record(new StudentRegisteredEvent($studentId, $name));
    }

    protected function applyStudentRegisteredEvent(StudentRegisteredEvent $event): void
    {
        $this->registered = true;
    }
}
