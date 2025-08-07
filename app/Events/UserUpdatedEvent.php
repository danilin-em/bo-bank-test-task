<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserUpdatedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly array $originalData,
        public readonly array $updatedData
    ) {
    }

    public function getChangedFields(): array
    {
        $changes = [];

        foreach ($this->updatedData as $field => $newValue) {
            if ($newValue !== null && isset($this->originalData[$field])) {
                $originalValue = $this->originalData[$field];

                // Handle Email value object comparison
                if (is_object($originalValue) && method_exists($originalValue, 'value')) {
                    $originalValue = $originalValue->value();
                }

                if ($originalValue !== $newValue) {
                    $changes[$field] = [
                        'from' => $originalValue,
                        'to' => $newValue,
                    ];
                }
            }
        }

        return $changes;
    }
}
