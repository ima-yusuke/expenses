<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Japanese extends Model
{
    use HasFactory;

    protected $fillable = ['japanese', 'word_id'];
    protected $table = 'japanese'; // テーブル名を `japanese` に設定

    /**
     * Get the word that owns the Japanese meaning.
     */
    public function word()
    {
        return $this->belongsTo(Word::class);
    }
}
