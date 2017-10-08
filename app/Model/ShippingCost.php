<?php namespace App\Model;

class Payment extends BaseModel
{
    protected $fillable = ['order_id', 'user_id', 'status', 'reference_no', 'user_account', 'user_bank_account', 'notes'];

    public static function changeStatus($id, $status) {
        $model = self::find($id);
        $model->status = $status;
        return $model->save();
    }
}
?>