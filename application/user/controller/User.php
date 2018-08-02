<?php
namespace app\user\controller;

use think\Config;
use think\Request;
use think\Cookie;
use Firebase\JWT\JWT;


use app\user\model\User as UserModel;

class User
{

    // 获取当前登录用户信息
    public function index(Request $request)
    {
        // 判断token
        $jwt = Cookie::get('jwt');
        $decoded = JWT::decode($jwt, Config::get('jwt_key'), array('HS256'));
        return json_encode($decoded);

        // 取出用户email
    }
    
    // 用户注册
    public function save(Request $request)
    {
        $data = $request->post();

        $user = new UserModel();

        // 验证
        $validate = \think\Loader::validate('User');
        if (!$validate->scene('signup')->check($data)) {
            return json_encode(['code' => 400, 'msg' => $validate->getError()]);
        }

        // 自动填充用户名字段 username
        if (!isset($data['username'])) {
            $data['username'] = $data['email'];
        }

        try {
            $user->allowField(true)->save($data);
        } catch (\Exception $e) {
            return json_encode(['code' => 500, 'msg' => $e->getMessage()]);
        }
        return json_encode(['code' => 200, 'msg' => 'Registration success!']);
    }
}
