<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;

use App\Tag;

class AdminController extends Controller
{
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
}
