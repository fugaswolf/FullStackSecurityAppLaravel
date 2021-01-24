<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    //if you send a new message to a chat, we want to automatically update the timestamp for that chat 
    //and indicate when the chat was last updated
    //Laravel will take care of this process by declaring the $touches
    protected $touches=['chat'];

    protected $fillable=[
        'user_id', 'chat_id', 'body', 'last_read'
    ];

    public function getBodyAttribute($value)
    {
        if($this->trashed()){
            if(!auth()->check()) return null;

            return auth()->id() == $this->sender->id ?
                    'You deleted this message' :
                    "{$this->sender->name} deleted this message";
        }
        return $value;
    }

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
