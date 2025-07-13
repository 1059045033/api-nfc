<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;

class WechatAuthController extends Controller
{
    // 处理微信登录
    public function login(Request $request)
    {
        // 1. 获取前端传来的code
        $code = $request->input('code');

        if (empty($code)) {
            return response()->json([
                'success' => false,
                'message' => 'Code 必传',
                'errors' => "",
            ], 401);
        }

        // 2. 向微信服务器请求session_key和openid
        $response = $this->getWechatSession($code);
        if (isset($response['errcode'])) {
            return response()->json([
                'success' => false,
                'message' => $response['errmsg'],
                'errors' => $response['errmsg'],
            ], 401);
        }

        // 3. 处理用户信息（注册或登录）
        $user = $this->processUser($response['openid'], $response);

        // 4. 生成自定义登录态token
        $token = $user->createToken('wechat-miniprogram')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }

    // 获取微信session_key和openid
    protected function getWechatSession($code)
    {
        $url = 'https://api.weixin.qq.com/sns/jscode2session';
        $params = [
            'appid' => env('WECHAT_APPID'),
            'secret' => env('WECHAT_SECRET'),
            'js_code' => $code,
            'grant_type' => 'authorization_code'
        ];
        $response = Http::get($url, $params);
        return $response->json();
    }

    // 处理用户数据
    protected function processUser($openid, $wechatData)
    {
        // 查找或创建用户
        $user = User::firstOrCreate(
            ['openid' => $openid],
            [
                'name' => 'wechat_' . substr($openid, -6),
                'wechat_info' => json_encode($wechatData)
            ]
        );

        // 更新session_key等数据
        $user->update([
            'session_key' => $wechatData['session_key'],
            'last_login_at' => now()
        ]);

        return $user;
    }
}
