<?php
namespace App\Repositories;
use App\Repositories\BaseRepositories;
use App\Models\Role;

class RoleRepositories extends BaseRepositories{

    protected $order = array('id' => 'desc');
    protected $role_name;

    public function __construct(Role $model)
    {
        $this->model = $model;
    }

    public function setRoleName($role_name){
        $this->role_name = $role_name;
    }

    // datatable list query
    private function getDataTableQuery(){
        // column wise sorting
        $this->column_order = [null,'id','role_name',null,null,null];
        // datatable list query
        $query = $this->model;

        // datatable data filter query
        if(!empty($this->role_name)){
            $query->where('role_name', 'like','%' .$this->role_name. '%');
        }

        // Sorting
        if(isset($this->orderValue) && isset($this->dirValue)){
            $query->orderBy($this->column_order[$this->orderValue], $this->dirValue);
        }else if(isset($this->order)){
            $query->orderBy(key($this->order), $this->order[key($this->order)]);
        }
        return $query;
    }

    public function getDataTableList()
    {
        $query = $this->getDataTableQuery();
        if ($this->lengthValue != -1) {
            $query->offset($this->startValue)->limit($this->lengthValue);
        }
        return $query->get();
    }

    // count function
    public function countFilter(){
        $query = $this->getDataTableQuery();
        return $query->get()->count();
    }

    public function countAll(){
        return $this->model::toBase()->get()->count();
    }

}
