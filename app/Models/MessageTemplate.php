<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'title', 'category', 'content'])]
class MessageTemplate extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Replace template variables (e.g. {{empresa}}, {{cidade}}, {{categoria}}) with lead attributes.
     */
    public function parseForLead(array|Lead $lead): string
    {
        $name = is_array($lead) ? ($lead['name'] ?? '') : $lead->name;
        $city = is_array($lead) ? ($lead['city'] ?? '') : ($lead->city ?? '');
        $category = is_array($lead) ? ($lead['category'] ?? '') : ($lead->category ?? '');

        return str_replace(
            ['{{empresa}}', '{{cidade}}', '{{categoria}}'],
            [$name, $city, $category],
            $this->content
        );
    }
}
