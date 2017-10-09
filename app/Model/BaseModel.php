<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    public static function getList($params = [], $page = 1, $perPage = 10, $searchColumn = []) {
        $model = self::query();

        foreach ($searchColumn as $col) {
            if (isset($params[$col])) {
                $model->where($col, $params[$col]);
            }
        }

        return $model->paginate($perPage, ['*'], "page", $page);
    }

    public static function createData($data) {
        $model = self::query();
        return $model->create($data);
    }

    public static function updateData($id, $data) {
        $model = self::find($id);
        return $model->update($data);
    }

    public static function removeData($id) {
        $model = self::find($id);
        return $model->delete();
    }

    public static function changeStatus($id) {
        $model = self::find($id);
        $model->status = !$model->status;
        return $model->save();
    }
}
?>