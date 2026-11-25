<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = ['title', 'content', 'image'];

    // Ikut dimasukkan ke JSON yang dikirim ke Flutter
    protected $appends = ['image_url'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Menghasilkan URL lengkap, atau null kalau tidak ada foto
    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}