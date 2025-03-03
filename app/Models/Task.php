<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $dates = ['due_date', 'created_at', 'updated_at'];

    const STATUS_COMPLETED = 'completed';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_NOT_STARTED = 'not_started';

    protected $fillable = [
        'title',
        'description',
        'due_date',
        'completed',
        'status'
    ];

    protected static function booted()
    {
        static::saving(function ($task) {
            // Синхронизация completed и status
            $task->completed = $task->status === self::STATUS_COMPLETED;
        });
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_COMPLETED => 'Выполнена',
            self::STATUS_IN_PROGRESS => 'В процессе',
            self::STATUS_NOT_STARTED => 'Не выполнена',
        ];
    }
}
