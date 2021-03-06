<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;

use App\Tag;
use App\User;
use App\Role;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{

    public function index(Request $request){

        // first check if you are loggedin and admin user ...

        if(!Auth::check() && $request->path() != 'login'){
            return redirect('/login');
        }

        if(!Auth::check() && $request->path() == 'login'){
            return view('welcome');
        }

        // you are already logged in... so check for if you are an admin user..
        $user = Auth::user();
        if ($user->userType == 'User') {
            return redirect('/login');
        }
        if ($request->path() == 'login') {
            return redirect('/');
        }
        return view('welcome');
        
    }


    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }


    public function get_tags(){

        return Tag::orderby('id', 'desc')->get();
    }

    public function addTag(Request $request){        
        $this->validate($request, [
            'tagName' => 'required'
        ]);
        return Tag::create([
            'tagName' => $request->tagName 
        ]);
    }


    public function editTag(Request $request){        
        $this->validate($request, [
            'id' => 'required',
            'tagName' => 'required'
        ]);
        Tag::where('id', $request->id)->update([
            'tagName' => $request->tagName 
        ]);

        return response()->json([
            'tagName' => $request->tagName 
        ]);
    }

    public function deleteTag(Request $request){        
        $this->validate($request, [
            'id' => 'required'
        ]);
        return Tag::where('id', $request->id)->delete();
    }

    public function upload(Request $request){  
        
        $this->validate($request, [
            'file' => 'required|mimes:jpg,jpeg,png',
        ]);

        $picName = time().'.'.$request->file->extension();
        $request->file->move(public_path('upload'), $picName);

        return $picName;
    }

    public function deleteImage(Request $request){  
        $fileName = $request->imageName;
        $this->deleteFileFromServer($fileName, false);
        return "Done";
    }

    public function deleteFileFromServer($fileName, $hasFullPath = false){  
        if(!$hasFullPath){
            $filePath = public_path().'/'.$fileName;
        }
        $filePath = public_path().'/'.$fileName;
        if(file_exists($filePath)){
            @unlink($filePath);
        }
        return "Done";
    }


    public function addCategory(Request $request){        
        $this->validate($request, [
            'categoryName' => 'required',
            'iconImage' => 'required'
        ]);
        return Category::create([
            'categoryName' => $request->categoryName,
            'iconImage' => $request->iconImage 
        ]);
    }

    public function getCategory(){
        return Category::orderby('id', 'desc')->get();
    }
    
    public function editCategory(Request $request){
        $this->validate($request, [
            'categoryName' => 'required',
            'iconImage' => 'required'
        ]);
        Category::where('id', $request->id)->update([
            'categoryName' => $request->categoryName,
            'iconImage' => $request->iconImage 
        ]);
    }

    public function deleteCategory(Request $request){
        $this->deleteFileFromServer($request->iconImage);
        $this->validate($request, [
            'id' => 'required'
        ]);
        return Category::where('id', $request->id)->delete();
    }

    public function createUser(Request $request){
        $this->validate($request, [
            'fullName' => 'required',
            'email' => 'bail|required|email|unique:users',
            'password' => 'bail|required|min:6',
            'role_id' => 'required'
        ]);
        $password = bcrypt( $request->password);

        $user = User::create([
            'fullName' => $request->fullName,
            'email' => $request->email, 
            'password' =>  $password, 
            'role_id' => $request->role_id 
        ]);

        return $user;
    } 
    
    public function editUser(Request $request){
        $this->validate($request, [
            'fullName' => 'required',
            'email' => "bail|required|email|unique:users,email,$request->id",
            'password' => 'min:6',
            'role_id' => 'required'
        ]);
        $data = [
            'fullName' => $request->fullName,
            'email' => $request->email, 
            'role_id' => $request->role_id,
        ];
        if($request->password){
            $password = bcrypt( $request->password);
            $data['password'] = $password;
        }
        $user = User::where('id', $request->id)->update($data);
        return $user;
    }


    public function getUsers(){
        return User::where('role_id', '!=', 1)->orderby('id', 'desc')->get();
    }

    public function adminLogin(Request $request)
    {
        $this->validate($request, [
            'email' => 'bail|required|email',
            'password' => 'bail|required|min:6',
        ]);
        if(Auth::attempt(['email'=>$request->email, 'password'=>$request->password])){
            $user = Auth::user();
            if($user->role->isAdmin == 0){
                Auth::logout();
                return response()->json([
                    'msg' => 'Incorrect login details',
                ], 401);
            }
            return response()->json([
                'msg' => 'You are logged in',
                'user' => $user
            ]);
        }else{
            return response()->json([
                'msg' => 'Incorrect login details',
            ], 401);
        }
    }

    public function addRole(Request $request){
        $this->validate($request, [
            'roleName' => 'required',
        ]);

        return Role::create([
            'roleName' => $request->roleName
        ]);
    }
    
    public function getRoles(){
        return Role::orderby('id', 'desc')->get();
    }



    public function editRole(Request $request){
        $this->validate($request, [
            'roleName' => 'required',
        ]);

        Role::where('id', $request->id)->update([
            'roleName' => $request->roleName 
        ]);
    }
    
    public function deleteRole(Request $request){
        $this->validate($request, [
            'id' => 'required'
        ]);
        return Role::where('id', $request->id)->delete();
    }
}
