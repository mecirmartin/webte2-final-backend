<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MyUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class MyUserController extends Controller
{
    //
    function saveUser(Request $request){

        //check ci v requeste je id
        if($request->input('id') != null){
            $user = MyUser::find($request->input('id'));
            if($user==null){       //ci v DB je taky user
                $myUser = new MyUser();
                $myUser->name = $request->input('name');
                $myUser->r = $request->input('r');
                $myUser->init_values = $request->input('init_values');
                $myUser->save();
                return $myUser;
            }else{          //ked nie je
                $this->updateUser($request,$request->input('id'));
            }
        }else{
            $myUser = new MyUser();
            $myUser->name = $request->input('name');
            $myUser->r = $request->input('r');
            $myUser->init_values = $request->input('init_values');
            $myUser->save();
            return $myUser;
        }

    }

    function deleteUser($id){
        $user = MyUser::find($id);
        if($user == null)  return response("",404);
        else $user->delete();
        return response("",204);

    }


    function getUser($id){
        Cache::flush();
        $user = MyUser::find($id);
        if($user == null)  return response("",404);
        return response($user,200);

    }


    function getAllUsers(){
        Cache::flush();
        $users = MyUser::all();
        return response($users,200);
    }

    function updateUser(Request $request, $id)
    {
        $user = MyUser::find($id);
        if($user == null)  return response("",404);
        $input = $request->all();
        $user->update($input);
        return response($user,200);
    }
}
