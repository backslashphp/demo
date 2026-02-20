<?php

declare(strict_types=1);

namespace Demo\Feature\StudentView\Projection;

use Backslash\Projection\ProjectionInterface;
use JsonSerializable;

class StudentProjection implements ProjectionInterface, JsonSerializable
{
    private string $studentId;

    private string $name;

    private array $enrollments = [];

    public function __construct(string $studentId, string $name)
    {
        $this->studentId = $studentId;
        $this->name = $name;
    }

    public static function id(string $studentId): string
    {
        return 'student-' . $studentId;
    }

    public function getId(): string
    {
        return self::id($this->studentId);
    }

    public function subscribe(string $courseId, string $courseName): void
    {
        $this->enrollments[$courseId] = [
            'courseId' => $courseId,
            'courseName' => $courseName,
        ];
    }

    public function unsubscribe(string $courseId): void
    {
        unset($this->enrollments[$courseId]);
    }

    public function jsonSerialize(): array
    {
        return [
            'studentId' => $this->studentId,
            'name' => $this->name,
            'enrollments' => array_values($this->enrollments),
        ];
    }
}
