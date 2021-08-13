<?php
namespace App\Services;
use App\Services\BaseService;
use App\Repositories\MenuRepositories as Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MenuService extends BaseService{
    protected $menu;

    public function __construct(Menu $menu)
    {
        $this->menu = $menu;
    }

    /**
     * getDatatableData function
     *
     * @param Request $request
     * @return void
     */
    public function getDatatableData(Request $request){
        if($request -> ajax()){

            // Filter datatable
            if(!empty($request->menu_name)){
                $this->menu-> setMenuName($request->menu_name);
            }

            // Show uer list
            $this->menu-> setOrderValue($request->input('order.0.column'));
            $this->menu-> setDirValue($request->input('order.0.dir'));
            $this->menu-> setLengthValue($request->input('length'));
            $this->menu-> setStartValue($request->input('start'));

            $list = $this->menu-> getDataTableList();

            $data = [];
            $no = $request->input('start');
            foreach ($list as $value) {
                $no++;
                $action = '';
                $action .= ' <a style="cursor: pointer" class="dropdown-item edit_data" data-id="'.$value->id.'"><i class="fas fa-edit text-primary"></i> Edit</a>';
                // $action .= ' <a style="cursor: pointer" class="dropdown-item view_data" data-id="'.$value->id.'"><i class="fas fa-eye text-warning"></i> View</a>';
                if($value->deletable == 1){
                    $action .= ' <a style="cursor: pointer" class="dropdown-item delete_data" data-name="'.$value->menu_name.'" data-id="'.$value->id.'"><i class="fas fa-trash text-danger"></i> Delete</a>';
                }


                $btngroup = '<div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-th-list"></i>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    '.$action.'
                                </div>
                            </div>';

                $row = [];
                if($value->deletable == 1){
                    $row []    = ' <div class="custom-control custom-checkbox">
                                    <input value="'.$value->id.'" name="did[]" class="custom-control-input select_data" onchange="selectSingleItem('.$value->id.')" type="checkbox" value="" id="checkBox'.$value->id.'">
                                    <label class="custom-control-label" for="checkBox'.$value->id.'">
                                    </label>
                                </div>';
                }else{
                    $row [] = '';
                }

                $row []    = $no;
                $row []    = $value->menu_name;
                $row []    = DELETABLE[$value->deletable];
                $row []    = $btngroup;
                $data[]    = $row;
            }
            return $this->datatableDraw($request->input('draw'), $this->menu-> countFilter(), $this->menu-> countAll(), $data);
        }
    }

    public function storeOrUpdate(Request $request){
        $collection = collect($request->validated());
        $created_at = $updated_at = Carbon::now();
        if($request->update_id){
            $collection = $collection -> merge(compact('updated_at'));
        }else{
            $collection = $collection -> merge(compact('created_at'));
        }

        return $this->menu->updateOrCreate(['id' => $request->update_id], $collection->all());
    }

    public function edit(Request $request){
        return $this->menu->find($request->id);
    }

    public function delete(Request $request){
        return $this->menu->delete($request->id);
    }

    public function bulkDelete(Request $request){
        return $this->menu->destroy($request->ids);
    }
}