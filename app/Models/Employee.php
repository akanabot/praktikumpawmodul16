<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'age',
        'position_id',
        'original_filename',
        'encrypted_filename'
    ];

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    protected static function booted()
    {
        static::updating(function ($employee) {
            \Log::info('Updating Employee:', $employee->toArray());
        });

        static::deleting(function ($employee) {
            \Log::warning('Deleting Employee:', $employee->toArray());
        });
    }
}
