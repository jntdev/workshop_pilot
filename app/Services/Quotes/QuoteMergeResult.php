<?php

namespace App\Services\Quotes;

class QuoteMergeResult
{
    /**
     * @param  array<string, mixed>  $fields
     * @param  array<int, array<string, mixed>>  $lines
     * @param  array<int, array{path: string, base: mixed, mine: mixed, theirs: mixed}>  $fieldConflicts
     * @param  array<int, array{type: string, line_key: string, base: ?array<string, mixed>, mine: ?array<string, mixed>, theirs: ?array<string, mixed>}>  $lineConflicts
     */
    public function __construct(
        public readonly array $fields,
        public readonly array $lines,
        public readonly array $fieldConflicts,
        public readonly array $lineConflicts,
    ) {}

    public function hasUnresolvedConflicts(): bool
    {
        return $this->fieldConflicts !== [] || $this->lineConflicts !== [];
    }
}
