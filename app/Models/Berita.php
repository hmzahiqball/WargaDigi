<?php   
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Berita extends Model
{
    use HasFactory;

    protected $table = 'berita';
    protected $fillable = [
        'judul_berita', 'slug', 'kategori', 'isi_berita', 
        'featured_image', 'status', 'operator_id', 'approval_id', 'tanggal_publish'
    ];

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }
}