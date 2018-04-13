<?php

namespace App\Repositories;

use App\Mongodb\File;

class FileRepository extends BaseRepository
{
	/**
	 * Create a new UserRepository instance.
	 *
   	 * @param  App\Mongodb\File $m_file
	 * @return void
	 */
	public function __construct(File $m_file)
	{
		$this->model = $m_file;
	}

    /**
     * Create a company.
     *
     * @param  array  $inputs
     * @return App\Mongodb\File
     */
    public function store($inputs, $connect_page_id)
    {
        $m_file = new $this->model;
        $m_file->connect_page_id    = $connect_page_id;
        $m_file->file_name  = $inputs['file_name'];
        $m_file->type       = $inputs['type'];

        if(isset($inputs["width"])){
            $m_file->width       = $inputs['width'];
        }
        if(isset($inputs["height"])){
            $m_file->height       = $inputs['height'];
        }

        $this->save($m_file, $inputs);
        return $m_file;
    }

	/**
	 * Save the ManageFile.
	 *
	 * @param  App\Mongodb\File $m_file
	 * @param  Array  $inputs
	 * @return void
	 */
  	private function save($m_file, $inputs)
	{
        $m_file->name   = $inputs['name'];
        $m_file->save();
	}

	/**
	 * Update a ManageFile.
	 *
	 * @param  array  $inputs
	 * @param  App\Mongodb\File $m_file
	 * @return void
	 */
	public function update($m_file, $inputs)
	{
	    $this->save($m_file, $inputs);
	    return $m_file;
	}

	public function getAll($connect_page_id, $type = null){
	    $model = $this->model;
	    $model = $model->where('connect_page_id', $connect_page_id);
        if(!empty($type)){
            $model = $model->where('type', $type);
        }
        $model = $model->orderBy('created_at', 'asc');
        return $model->get();
    }

    public function getFileDelete(){
	    return $this->model
            ->onlyTrashed()->where('created_at', '>', new \DateTime('-1 month'))->get();
    }

}
