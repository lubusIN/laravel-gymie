<?php 
namespace App;

use Auth;
use Illuminate\Database\Eloquent\Model;
use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole
{

	protected $table = 'roles';

    protected $fillable = [
    		'name',
    		'display_name',
    		'description',
    ];

    public function Users()
    {
        return $this->belongsToMany('App\User');
    }

    public function Permissions()
    {
    	return $this->hasMany('App\Permission');
    }

    public function Role_users()
    {
        return $this->belongsToMany('App\Role_user');
    }

    public function scopeExcludeGymie($query)
    {
        if (Auth::User()->id != 1) 
        {
            return $query->where('id','!=',1);
        }
        return $query;
    }

}