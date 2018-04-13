<?php

namespace App\Repositories;

use App\Mongodb\Mail;

class MailRepository extends BaseRepository
{
	/**
	 * Create a new Mail instance.
	 *
	 * @param  App\Mail $sticker
	 * @return void
	 */
	public function __construct(Mail $mail)
	{
		$this->model = $mail;
	}

	/**
	 * Create a Email.
	 *
	 * @param  array  $inputs
	 * @return App\Mail
	 */
	public function store($inputs, $connect_page_id)
	{
        $mail = new $this->model;
        $mail->connect_page_id = $connect_page_id;
		$this->save($mail, $inputs);
		return $mail;
	}

	private function save($mail, $inputs)
	{
        $mail->from_name = $inputs['from_name'];
//        $mail->from_email = $inputs['from_email'];
        $mail->to  = $inputs['to'];
        $mail->name = $inputs['email_name'];
        $mail->subject  = $inputs['subject'];
        $mail->content  = $inputs['content'];
        $mail->save();
        return $mail;
	}

	public function update($mail, $inputs)
	{
		$this->save($mail, $inputs);
        return $mail;
	}

    public function getAll($connect_page_id, $keyword = null)
    {
        $model = new $this->model;
        $model = $model->where('connect_page_id', $connect_page_id);
        if(!empty($keyword)){
            $model = $model->where(function($q) use ($keyword) {
                $q->Where('to', 'like', "%{$keyword}%");
                $q->orWhere('name', 'like', "%{$keyword}%");
            });
        }
        $model = $model->orderBy('created_at', 'DESC');
        return $model->get();
    }

}
