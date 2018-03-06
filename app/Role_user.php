<?php 
namespace App;

use Illuminate\Database\Eloquent\Model;
use Zizaco\Entrust\EntrustRole;

class Role_user extends Model
{

	protected $table = 'role_user';

    protected $fillable = [
    		'user_id',
    		'role_id',
    ];

    public function User()
    {
        return $this->belongsTo('App\User');
    }

    public function Role()
    {
        return $this->belongsTo('App\Role');
    }

}