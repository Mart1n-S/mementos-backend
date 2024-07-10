<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Revision extends Model
{
    use HasFactory;

    /**
     * The name of the table associated with the model.
     *
     * @var string
     */
    protected $table = 'revisions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'carte_id',
        'niveau',
        'dateRevision',
    ];

    /**
     * Get the user that owns the revision.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the carte that owns the revision.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function carte()
    {
        return $this->belongsTo(Carte::class);
    }
}
