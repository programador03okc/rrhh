<?php

namespace App\Models\Tesoreria;

use Illuminate\Contracts\Session\Session;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class Usuario extends Authenticatable
{
	use Notifiable;
    //
    protected $table = 'configuracion.sis_usua';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;

   protected $fillable = [
		'id_trabajador',
		'usuario',
		'clave',        
		'estado',     
		'fecha_registro',
		'nombre_corto'  
    ];

	protected $hidden = [
		'clave',
	];

   protected $appends = ['login_rol', 'empresa'];

    /*protected $hidden = ['clave'];
    protected $guarded = ['id_usuario'];*/

	public function getAuthPassword(){
		return $this->clave;
	}

	public function getLoginRolAttribute(){
		return session('login_rol');
	}

	public function getConceptoLoginRolAttribute(){
		$rol = Rol::with('rol_concepto')->findOrFail($this->login_rol);
		return $rol->rol_concepto->descripcion;
	}

	public function getCargoAttribute(){
		$rol = Rol::with('cargo')->findOrFail($this->login_rol);
		return $rol->cargo->descripcion;
	}

	public function roles(){
		return $this->trabajador->roles();
	}

	public function getEmpresaAttribute(){
		$roles = $this->trabajador->roles;

		$empresas = [];
		foreach ($roles as $rol){
			$area = Area::findOrFail($rol->pivot->id_area)->grupo->sede->empresa;
			$empresas[] = $area;
		}
		return collect($empresas);
	}


    public function trabajador(){
        return $this->belongsTo('App\Models\Tesoreria\Trabajador','id_trabajador','id_trabajador');
	}
	
	public function obtenerRoles(){
		$rolesBD = $this->trabajador->roles()->get();
		foreach ($rolesBD as $key){
			$roles[] = $key->id_rol_concepto;
		}
		return $roles;
	}














	// SECCION ROLES PERSONALIZADA

	public function authorizeRoles($roles)
	{
		if ($this->hasAnyRole($roles)) {
			return true;
		}
		abort(401, 'Esta acción no está autorizada.');
	}
	public function hasAnyRole($roles)
	{
		if (is_array($roles)) {
			foreach ($roles as $role) {
				//echo $role .'<br>';
				if ($this->hasRole($role)) {
					//dd('rol');
					return true;
				}
			}
		} else {
			if ($this->hasRole($roles)) {
				return true;
			}
		}
		return false;
	}
	public function hasRole($role)
	{
		//DB::enableQueryLog();
		//dd($this->roles()->where('rrhh.rrhh_rol.id_rol_concepto', $role)->first());

		//($this->trabajador->roles()->get()->toArray());
		//$query = DB::getQueryLog();
		//dd($query);
		if ($this->trabajador->roles()->where('rrhh.rrhh_rol.id_rol_concepto', $role)->first()) {
			//dd('a');
			return true;
		}
		//dd('b');
		return false;
	}
}
