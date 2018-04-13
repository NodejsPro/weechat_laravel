<?php

namespace App\Repositories;

use App\Mongodb\Menu;
use Illuminate\Support\Facades\Log;

class MenuRepository extends BaseRepository
{
	/**
	 * Create a new UserRepository instance.
	 *
	 * @param  App\Menu $menu
	 * @return void
	 */
	public function __construct(Menu $menu)
	{
		$this->model = $menu;
	}

	/**
	 * Create a Menu.
	 *
	 * @param  array  $inputs
	 * @return App\Menu
	 */
	public function store($inputs, $connect_page_id, $parent_id = '')
	{
        $menu = new $this->model;
        $menu->connect_page_id  = $connect_page_id;
        $menu->priority_order   = $inputs['priority_order'];
        $menu->parent_id        = $parent_id;
        $this->save($menu, $inputs);
		return $menu;
	}

	/**
	 * Save the Menu.
	 *
	 * @param  App\Menu $menu
	 * @param  Array  $inputs
	 * @return void
	 */
	private function save($menu, $inputs)
	{
        $menu->title    = $inputs['title'];
        $menu->type     = $inputs['type'];
        $menu->url      = @$inputs['url'];
        $menu->scenario_id = @$inputs['scenario_id'];
		$menu->save();
	}

	/**
	 * Update a Menu.
	 *
	 * @param  array  $inputs
	 * @param  App\Models\Menu $menu
	 * @return void
	 */
	public function update($menu, $inputs)
	{
		$this->save($menu, $inputs);
		return $menu;
	}

    public function getAll($connect_page_id, $parent_id = null, $menu_ids = null)
    {
        $model = new $this->model;
        if(empty($parent_id)){
            $model = $model->whereNull('parent_id')->orWhere('parent_id' , '');
        }else{
            $model = $model->where('parent_id', $parent_id);
        }
        if(!empty($menu_ids)){
            $model = $model->whereIn('_id', $menu_ids);
        }
        $model = $model->where('connect_page_id', $connect_page_id)
                       ->orderBy('priority_order', 'ASC');
        return $model->get();
    }

    public function updatePriorityOrder($menu, $input){
        $menu->priority_order   = intval($input['priority_order']);
        $menu->save();
        return $menu;
    }

    public function getMaxPriorityOrder($parent_id = null){
        $model = new $this->model;
        if(empty($parent_id)){
            $model = $model->whereNull('parent_id')->orWhere('parent_id' , '');
        }else{
            $model = $model->where('parent_id', $parent_id);
        }
        return $model->max('priority_order');
    }

    public function destroyByParentId($connect_page_id, $menu_id){
        $model = new $this->model;
        $model->where('connect_page_id', $connect_page_id)
                        ->where('parent_id', $menu_id)
                        ->delete();
    }

    public function updateParentId($item, $parent_id)
    {
        $item->parent_id = $parent_id;
        $item->save();
    }
}
