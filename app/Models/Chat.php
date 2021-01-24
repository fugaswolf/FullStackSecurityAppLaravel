<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    public function participants()
    {
        return $this->belongsToMany(User::class, 'participants');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    // helper methods
    public function getLatestMessageAttribute()
    {
        return $this->messages()->latest()->first();
    }

    public function isUnreadForUser($userId)
    {
        return (bool)$this->messages()
                ->whereNull('last_read')
                ->where('user_id', '<>', $userId)
                ->count();
    }

    public function markAsReadForUser($userId)
    {
        $this->messages()
            ->whereNull('last_read')
            ->where('user_id', '<>', $userId)
            ->update([
                'last_read' => Carbon::now()
            ]);
    }
}
