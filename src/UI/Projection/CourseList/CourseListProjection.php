<?php

declare(strict_types=1);

namespace Demo\UI\Projection\CourseList;

use Backslash\Projection\ProjectionInterface;
use Stringable;

class CourseListProjection implements ProjectionInterface, Stringable
{
    public const ID = __CLASS__;

    private array $courses = [];

    private array $students = [];

    public function __toString(): string
    {
        $string = '----- COURSES -----' . PHP_EOL;
        if (empty($this->courses)) {
            $string .= 'Empty' . PHP_EOL;
        }
        foreach ($this->courses as $course) {
            if ($course['canceled']) {
                $string .= sprintf(
                    '[%s] %s (CANCELED)',
                    $course['courseId'],
                    $course['name'],
                ) . PHP_EOL;
            } else {
                $string .= sprintf(
                    '[%s] %s (%d/%d)',
                    $course['courseId'],
                    $course['name'],
                    count($course['enrollments']),
                    $course['capacity'],
                ) . PHP_EOL;
            }
            foreach ($course['enrollments'] as $studentId) {
                $string .= sprintf(
                    '    - %s',
                    $this->students[$studentId],
                ) . PHP_EOL;
            }
        }
        return $string;
    }

    public function getId(): string
    {
        return self::ID;
    }

    public function addStudent(string $studentId, string $name): void
    {
        $this->students[$studentId] = $name;
    }

    public function addCourse(string $courseId, string $name, int $capacity): void
    {
        $this->courses[$courseId] = [
            'courseId' => $courseId,
            'name' => $name,
            'capacity' => $capacity,
            'enrollments' => [],
            'canceled' => false,
        ];
    }

    public function cancelCourse(string $courseId): void
    {
        $this->courses[$courseId]['canceled'] = true;
        $this->courses[$courseId]['enrollments'] = [];
    }

    public function changeCapacity(string $courseId, int $capacity): void
    {
        $this->courses[$courseId]['capacity'] = $capacity;
    }

    public function enroll(string $courseId, string $studentId): void
    {
        $this->courses[$courseId]['enrollments'][$studentId] = $studentId;
    }

    public function withdraw(string $courseId, string $studentId): void
    {
        unset($this->courses[$courseId]['enrollments'][$studentId]);
    }
}
