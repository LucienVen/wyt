<?php
namespace app\user\model;

use think\Model;

class User extends Model
{
    protected $autoWriteTimestamp = true;

    public function setPasswordAttr($value)
    {
        // return md5($value);
        return password_hash($value, PASSWORD_DEFAULT);
    }
}
