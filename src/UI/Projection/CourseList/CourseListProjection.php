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
            $string .= sprintf(
                '[%s] %s (%d/%d)',
                $course['courseId'],
                $course['name'],
                count($course['subscriptions']),
                $course['capacity'],
            ) . PHP_EOL;
            foreach ($course['subscriptions'] as $studentId) {
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

    public function defineCourse(string $courseId, string $name, int $capacity): void
    {
        $this->courses[$courseId] = [
            'courseId' => $courseId,
            'name' => $name,
            'capacity' => $capacity,
            'subscriptions' => [],
            'canceled' => false,
        ];
    }

    public function registerStudent(string $studentId, string $name): void
    {
        $this->students[$studentId] = $name;
    }

    public function changeCapacity(string $courseId, int $capacity): void
    {
        $this->courses[$courseId]['capacity'] = $capacity;
    }

    public function subscribe(string $courseId, string $studentId): void
    {
        $this->courses[$courseId]['subscriptions'][$studentId] = $studentId;
    }

    public function unsubscribe(string $courseId, string $studentId): void
    {
        unset($this->courses[$courseId]['subscriptions'][$studentId]);
    }
}
