<?php 
namespace app\user\controller;

use think\Request;
use think\Config;
use think\Cookie;
use Firebase\JWT\JWT;

use app\user\model\User as UserModel;

class Auth
{
    protected $token;
    protected $jwt;

    public function login(Request $request)
    {
        $data = $request->post();

        $validate = \think\Loader::validate('User');
        if (!$validate->scene('login')->check($data)) {
            return json_encode(['code' => 400, 'msg' => $validate->getError()]);
        }


        $user = new UserModel();
        // 获取password
        if ($userData = $user->where('email', $data['email'])->field('id, email, password')->limit(1)->find()) {
            if (password_verify($data['password'], $userData['password'])) {
                // 生成并设置token
                if ($this->getToken($userData)->setToken($request)) {
                    unset($userData['password']);
                    return json_encode(['code' => 200, 'msg' => 'login success!']);
                }
            } else {
                return json_encode(['code' => 400, 'msg' => 'password error!']);
            }
        } else {
            return json_encode(['code' => 400, 'msg' => 'user not exist!']);
        }
    }

    public function logout(Request $request)
    {
        Cookie::set('jwt', '', ['expire' => time()-1, 'httponly' => 'httponly']);
        return json_encode(['code' => 200, 'msg' => 'logout success!']);
    }

    // 获取token
    protected function getToken($userData)
    {
        $this->jwt = array('iss' => Config::get('iss'),
                    'aud' => Config::get('aud'),
                    'exp' => time()+3600*24,
                    'uid' => $userData['id'],
                    'email' => $userData['email']);

        $this->token = JWT::encode($this->jwt, Config::get('jwt_key'));

        return $this;
    }

    // 设置token
    protected function setToken(Request $request)
    {
        if (!is_null($this->token)) {
            Cookie::set('jwt', $this->token, ['expire' => $this->jwt['exp'],'httponly' => 'httponly']);
            return true;
        }
        return false;
    }
}
