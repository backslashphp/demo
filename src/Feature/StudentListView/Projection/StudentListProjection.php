<?php

declare(strict_types=1);

namespace Demo\Feature\StudentListView\Projection;

use Backslash\Projection\ProjectionInterface;
use JsonSerializable;

class StudentListProjection implements ProjectionInterface, JsonSerializable
{
    public const string ID = 'student-list';

    private array $studentIds = [];

    public function getId(): string
    {
        return self::ID;
    }

    public function addStudent(string $studentId): void
    {
        $this->studentIds[$studentId] = $studentId;
    }

    public function getStudentIds(): array
    {
        return array_values($this->studentIds);
    }

    public function jsonSerialize(): array
    {
        return [
            'studentIds' => array_values($this->studentIds),
        ];
    }
}
