<?php
namespace App\Http\Resources\Api\V1\Audits;

use Illuminate\Http\Resources\Json\JsonResource;


class AuditResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'event'      => strtoupper($this->event),
            'user'       => $this->user_id ? [
                'id'   => $this->user_id,
                'name' => $this->user?->name ?? 'Sistema'
            ] : null,
            'changes'    => [
                'before' => $this->old_values,
                'after'  => $this->new_values,
            ],
            'metadata'   => [
                'ip'          => $this->ip_address,
                'created_at'  => format_date($this->created_at),
                'updated_at'  => format_date($this->updated_at),
            ],
        ];
    }
}
