<?php namespace App\Repositories;

use Illuminate\Support\Facades\Log;

abstract class BaseRepository {

	/**
	 * The Model instance.
	 *
	 * @var Illuminate\Database\Eloquent\Model
	 */
	protected $model;

	/**
	 * Get number of records.
	 *
	 * @return array	 */
	public function getNumber()
	{
		$total = $this->model->count();
        return $total;
	}

	/**
	 * Get number of records.
	 *
	 * @return array	 */
	public function getAllData()
	{
		return $this->model->get();
	}

	/**
	 * Destroy a model.
	 *
	 * @param  int $id
	 * @return void
	 */
	public function destroy($id)
	{
		$this->getById($id)->delete();
	}

	/**
	 * Get Model by id.
	 *
	 * @param  int  $id
	 * @return App\Models\Model
	 */
	public function getById($id)
	{
		return $this->model->find($id);
	}

	public function getOneWithTrashedById($id)
	{
		return $this->model->find($id);
	}

    /**
     * Get Model by id.
     *
     * @param  int  $id
     * @return App\Models\Model
     */
    public function getOneByField($field, $value)
    {
        return $this->model->where($field, $value)->first();
    }

	public function getAllByField($field, $value)
	{
		return $this->model->where($field, $value)
                            ->orderBy('created_at', 'asc')->get();
	}

	public function getFirstOrNew($arr){
		return $this->model->firstOrNew($arr);
	}
    /**
     * Get Model by job id.
     *
     * @param  int  $id
     * @return App\Models\Model
     */
    public function getByKey($key)
    {
        return $this->model->where('key','=',$key)->first();
    }

	public function getKeyValue($name, $id, $condition){
		return $this->model->where($condition)
			->orderBy('created_at', 'DESC')
			->pluck($name, $id);
	}

	public function deleteAllByField($field, $value)
    {
        return $this->model->where($field, $value)->delete();
    }

    public function duplicateByBot($item, $data) {
        $model = $item->replicate();
        foreach ($data as $column => $val) {
            $model->$column = $val;
        }
        $model->save();
        return $model;
    }

    public function getAllByConnectPage($connect_page_id, $offset = 0, $limit = 10){
        $model = new $this->model;
        $model = $model->where('connect_page_id', $connect_page_id)
            ->skip($offset)
            ->take($limit)
            ->orderBy('created_at', 'DESC');
        return $model->get();
    }

    public function getCountByConnectPage($connect_page_id, $sns_type = null){
        $model = new $this->model;
        $model = $model->where('connect_page_id', $connect_page_id);
        $group_type = config('constants.group_type_service');
        if(isset($sns_type) && ($sns_type == $group_type['web_embed'] || $sns_type == $group_type['web_embed_efo'])){
            $model = $model->where('start_flg', config('constants.active.enable'));
            $model = $model->whereNull('preview_flg');
        }
        return $model->count();
    }

    public function getAllInListId($id_list) {
        $model = new $this->model;
        $model = $model->whereIn('_id', $id_list);
        return $model->get();
    }

}
