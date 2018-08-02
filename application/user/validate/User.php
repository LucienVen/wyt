<?php
namespace app\user\validate;

use think\Validate;

class User extends Validate
{
    protected $rule = [
        'email' => 'require|email',
        // 'username' => 'max: 30',
        'password' => 'require',
        'password_confirm' => 'require',
    ];

    protected $message = [
        'email.require' => 'email必须',
        'email' => '邮箱格式错误',
        'password.require' => '密码必须',
        'password_confirm.require' => '请再次确定密码'
    ];

    protected $scene = [
        'login' => ['email' => 'require|email', 'password' => 'require'],
        'signup' => ['email' => 'require|email|unique:user', 'password' => 'require|min:6|confirm:password_confirm'],
    ];
}
