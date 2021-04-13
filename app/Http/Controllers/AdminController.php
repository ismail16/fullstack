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

        return Tag::create([
            'tagName' => $request->tagName 
        ]);
    }
}
