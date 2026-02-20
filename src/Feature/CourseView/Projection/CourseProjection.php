<?php

declare(strict_types=1);

namespace Demo\Feature\CourseView\Projection;

use Backslash\Projection\ProjectionInterface;
use JsonSerializable;

class CourseProjection implements ProjectionInterface, JsonSerializable
{
    private string $courseId;

    private string $name;

    private int $capacity;

    private array $subscriptions = [];

    public function __construct(string $courseId, string $name, int $capacity)
    {
        $this->courseId = $courseId;
        $this->name = $name;
        $this->capacity = $capacity;
    }

    public static function id(string $courseId): string
    {
        return 'course-' . $courseId;
    }

    public function getId(): string
    {
        return self::id($this->courseId);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function subscribe(string $studentId): void
    {
        $this->subscriptions[$studentId] = $studentId;
    }

    public function unsubscribe(string $studentId): void
    {
        unset($this->subscriptions[$studentId]);
    }

    public function changeCapacity(int $capacity): void
    {
        $this->capacity = $capacity;
    }

    public function jsonSerialize(): array
    {
        return [
            'courseId' => $this->courseId,
            'name' => $this->name,
            'capacity' => $this->capacity,
            'subscriptions' => array_values($this->subscriptions),
        ];
    }
}
