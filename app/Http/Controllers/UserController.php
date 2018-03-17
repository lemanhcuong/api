<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    private $user;

    public function __construct(User $user){
        $this->user = $user;
    }
    /**
     * login
     * return data json
     */
    public function register(Request $request){
        DB::beginTransaction();
        try{
            $this->user->create([
                'name' => $request->name,
                'email' => $request->email,
                'tel' => $request->tel,
                'sex' => $request->sex,
                'password' => Hash::make($request->password)
            ]);
            DB::commit();
            return response()->json([
                'status'=> 200,
                'message'=> 'User created successfully',
            ]);
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'status'=> 404,
                'message'=> $e->getMessage(),
            ]);
        }
    }
    /**
     * login
     * return data json
     */
    public function login(Request $request){
        $credentials = $request->only('email', 'password');
        $token = null;
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['invalid_email_or_password'], 422);
            }
        } catch (JWTException $e) {
            return response()->json(['failed_to_create_token'], 500);
        }
        return response()->json(compact('token'));
    }
    /**
     * get data user
     * return data json
     */
    public function getUserInfo(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        $data =[
            'email'=>$user->email,
            'name'=>$user->name,
            'tel'=>$user->tel,
            'sex'=>$user->sex,
        ];
        return response()->json(['result' => $data]);
    }
    /**
     * update data user
     * return data json
     */
    public function updateUserInfo(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        
        DB::beginTransaction();
        try{
            $this->user->where('email','=',$user->email)->update([
                'name' => $request->name,
                'tel' => $request->tel,
                'sex' => $request->sex,
            ]);
            
            DB::commit();
            return response()->json([
                'status'=> 200,
                'message'=> 'User update successfully',
            ]);
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'status'=> 404,
                'message'=> $e->getMessage(),
            ]);
        }
    }
    /**
     * logout
     * return data json
     */
    public function logout(){
         JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json([
            'status'=> 200,
            'message'=> 'You are now signed out',
        ]);
    }
}
