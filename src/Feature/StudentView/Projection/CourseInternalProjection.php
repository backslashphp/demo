<?php

declare(strict_types=1);

namespace Demo\Feature\StudentView\Projection;

use Backslash\Projection\ProjectionInterface;
use JsonSerializable;

class CourseInternalProjection implements ProjectionInterface, JsonSerializable
{
    private string $courseId;

    private string $name;

    public function __construct(string $courseId, string $name)
    {
        $this->courseId = $courseId;
        $this->name = $name;
    }

    public function getId(): string
    {
        return $this->courseId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function jsonSerialize(): array
    {
        return [
            'courseId' => $this->courseId,
            'name' => $this->name,
        ];
    }
}
