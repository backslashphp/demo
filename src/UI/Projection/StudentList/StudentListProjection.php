<?php

declare(strict_types=1);

namespace Demo\UI\Projection\StudentList;

use Backslash\Projection\ProjectionInterface;
use Countable;
use Stringable;

class StudentListProjection implements ProjectionInterface, Stringable, Countable
{
    public const ID = __CLASS__;

    private array $students = [];

    private array $courses = [];

    public function __toString(): string
    {
        $string = '----- STUDENTS -----' . PHP_EOL;
        if (empty($this->students)) {
            $string .= 'Empty' . PHP_EOL;
        }
        foreach ($this->students as $student) {
            $courses = array_map(fn (string $courseId) => $this->courses[$courseId], $student['enrollments']);
            $string .= sprintf(
                '[%s] %s%s',
                $student['studentId'],
                $student['name'],
                empty($courses) ? '' : ' (' . implode(', ', $courses) . ')',
            ) . PHP_EOL;
        }
        return $string;
    }

    public function getId(): string
    {
        return self::ID;
    }

    public function count(): int
    {
        return count($this->students);
    }

    public function registerStudent(string $studentId, string $name): void
    {
        $this->students[$studentId] = [
            'studentId' => $studentId,
            'name' => $name,
            'enrollments' => [],
        ];
    }

    public function subscribe(string $studentId, string $courseId): void
    {
        $this->students[$studentId]['enrollments'][$courseId] = $courseId;
    }

    public function unsubscribe(string $studentId, string $courseId): void
    {
        unset($this->students[$studentId]['enrollments'][$courseId]);
    }

    public function defineCourse(string $courseId, string $name): void
    {
        $this->courses[$courseId] = $name;
    }

    public function cancelCourse(string $courseId): void
    {
        unset($this->courses[$courseId]);
        foreach ($this->students as $studentId => $student) {
            unset($this->students[$studentId]['enrollments'][$courseId]);
        }
    }
}
