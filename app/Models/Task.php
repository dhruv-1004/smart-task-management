<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;




class Task extends Model
{
    use HasFactory;
    protected $fillable = ['title','description','status','priority','due_date','user_id','progress'];

    public function setDueDateFromDays(int $days):void 
    {
        $this->due_date=Carbon::now()->addDays($days)->toDateString();
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
