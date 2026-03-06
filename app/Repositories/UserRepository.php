<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends Repository {

	public $model;

	public function __construct(User $user)
	{
		$this->model = $user;
	}

	public function get(): Object
	{
		return $this->model
		->leftJoin('ruangans', 'ruangans.id', '=', 'user.ruangan_id')
		->get([
            'user.idUser',
            'user.username',
            'user.nama',
            'user.alamat',
            'user.telepon',
            'user.hakAkses',
            'user.approval_level',
            'user.jabatan',
            'user.roleadmin',
            'user.ruangan_id',
            'ruangans.nama_ruangan'
        ]);
	}

	public function getKasir(): Object
	{
		return $this->model->where('hakAkses', 2)->get();
	}

	public function select($name): Object
	{
		return $this->model->where('hakAkses', '!=', 1)->where('nama', 'like', '%'.$name.'%')->get(['idUser as id', 'nama as text']);
	}

}

?>
