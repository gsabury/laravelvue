<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use DB;
use Image;

class UserController extends Controller
{

     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $this->authorize('isAdmin');
        if (\Gate::allows('isAdmin') || \Gate::allows('isAuthor')) {
            return User::latest()->paginate(10);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
        $this->validate($request, [
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users',
            'password' => 'required|string|min:6'
        ]);

        return User::create([
            'name' => $request->name,
            'email' => $request->email,
            'type' => $request->type,
            'bio' => $request->bio,
            'photo' => isset($request->photo) ? $request->photo:'profile.png',
            'password' => Hash::make($request->password),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $this->validate($request, [
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users,email, '.$user->id.'',
            'password' => 'string|min:6'
        ]);

        if($request->password!=""){
            $request->merge(['password' => Hash::make($request['password'])]);
        }else{
            $request->request->remove('password');
        }

        try{
            DB::beginTransaction();  
                $user->update($request->all());
            DB::commit();
            return 1;
        }catch(\Exception $e){
            DB::rollback();
            return 0;
        }

        // return ['message' => 'Updated the user info'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {   
        $this->authorize('isAdmin');
        $user = User::findOrFail($id);
        $user->delete();
        return ['message'=> 'User Deleted Successfully'];
    }

    public function profile(){
        return auth('api')->user();
    }

    public function updateProfile(Request $request){
        
        $user = auth('api')->user();

        if(!empty($request->password)){
            $this->validate($request,[
                'name' => 'required|string|max:191',
                'email' => 'required|string|email|max:191|unique:users,email,'.$user->id,
                'password' => 'sometimes|required|min:6'
            ]);
        }else{
            $this->validate($request,[
                'name' => 'required|string|max:191',
                'email' => 'required|string|email|max:191|unique:users,email,'.$user->id,
            ]);
        }

        

        $currentPhoto = $user->photo;

        if($request->photo != $currentPhoto){
            $ext = explode('/', explode(':', substr($request->photo, 0, strpos($request->photo, ';')))[1])[1];
            $name = time().'.'.$ext;
            Image::make($request->photo)->save(public_path('img/profile/').$name);
            $request->merge(['photo' => $name]);

            $userPhoto = public_path("img/profile").'/'.$currentPhoto;
            if(file_exists($userPhoto) && $userPhoto!= public_path("img/profile/profile.png")){
                @unlink($userPhoto);
            }
        }

        if($request->password!=""){
            $request->merge(['password' => Hash::make($request['password'])]);
        }else{
            $request->request->remove('password');
        }
        
        $user->update($request->all());

    }

    public function searchUser(){

        if ($search = \Request::get('q')) {
            $users = User::where(function($query) use ($search){
                $query->where('name','LIKE',"%$search%")
                        ->orWhere('email','LIKE',"%$search%");
            })->paginate(5);
        }else{
            $users = User::latest()->paginate(5);
        }

        return $users;

    }
}
