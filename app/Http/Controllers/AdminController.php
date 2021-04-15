<?php

namespace App\Http\Controllers;

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
}
