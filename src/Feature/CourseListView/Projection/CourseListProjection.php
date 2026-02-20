<?php

declare(strict_types=1);

namespace Demo\Feature\CourseListView\Projection;

use Backslash\Projection\ProjectionInterface;
use JsonSerializable;

class CourseListProjection implements ProjectionInterface, JsonSerializable
{
    public const string ID = 'course-list';

    private array $courseIds = [];

    public function getId(): string
    {
        return self::ID;
    }

    public function addCourse(string $courseId): void
    {
        $this->courseIds[$courseId] = $courseId;
    }

    public function getCourseIds(): array
    {
        return array_values($this->courseIds);
    }

    public function jsonSerialize(): array
    {
        return [
            'courseIds' => array_values($this->courseIds),
        ];
    }
}
