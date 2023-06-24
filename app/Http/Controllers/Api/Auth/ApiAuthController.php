<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiAuthController extends Controller
{
    public $successStatus = 200;

    /**
     * API data login
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {   
        
        // $macId = new User();
        // $macId->macId     = $request->macId;
        // echo $macId->macId;
        // exit;
        // $usera = Auth::user();
        // $usera = User::find($usera->id);
        
        // print_r($usera);
        // exit;
        // $a = $usera->macId;
        // $s = $request->macId;
        // echo $a.' '.$s;
        // if($usera){
            
                if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
                    $user = Auth::user();
                    // echo $user;
                    // exit;
                    $data['message'] = "success";
                    $data['token'] = $user->createToken('nApp')->accessToken;
                    $user = User::find($user->id);
                    // echo $user;
                    // exit;
                    $userf = User::get();
                    // echo $userf;
                    // exit;
                    
                    $a = $user->macId; //assign macid
                    // echo $a.'$a';
                    $s = $request->macId;
                    // echo $s.'$s';
                    $macIda = $request->macId;
                    // echo $macIda.'$macIda';
                    // if($macIda == $user->macId){
                    //     $data['message'] = "assda";
                    //     return response()->json($data);
                        
                    // }
                    $email = $request->email;
                    // echo $email;
                    // // echo $user->email;
                    // exit;
                    // if($email == $user->email && $user->macId !== null ){
                    //     $data['message'] = "pass email";
                    //     return response()->json($data);
                    // }
                    if($macIda && $user->macId !== $macIda){
                        if($user->macId){ 
                            
                            $data['message'] = "You can not login this device";
                            return response()->json($data);
                            
                        }else{
                            $validator = Validator::make(
                                ['macId' => $macIda],
                                ['macId' => 'unique:users']
                            );
                            
                            if ($validator->fails()) {
                                $data['message'] = "You can not login from this device";
                                return response()->json($data);
                            }
                            $macId           = User::find($user->id);
                            $macId->macId     = $request->macId;
                            $macId->save();
                            $data['message'] = "Press login one more time";
                            return response()->json($data);
                        }
                    } 
                    else if($a == $s){
                        $data['user'] = $user;
                        return response()->json($data, $this->successStatus);
                        // else{
                        // $data['message'] = "Unauthorised Mac Id";
                        // return response()->json($data);
                        // }
                        
                    }
        }

        $data['message'] = "Email or Password is incorrect";
        return response()->json($data);
    }
}
