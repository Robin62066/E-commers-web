<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;

class CategoryController extends Controller
{
    //for category listing
    public function index(Request $request)
    {
        $categories = Category::latest();
        if (!empty($request->get('keyword'))) {
            $categories = $categories->where('name', 'like', '%' . $request->get('keyword') . '%');
        }
        $categories = $categories->paginate(10);
        // dd($categories); //for printing
        $data['categories'] = $categories;
        return view('admin.category.list', $data);
        // return view('admin.category.list',compact('categories'));
    }

    //for creating caterogy 
    public function create()
    {
        return view('admin.category.create');
    }

    //for storing caterogy 
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories'
        ]);

        if ($validator->passes()) {
            $category = new Category();
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->save();

            session()->flash('success', 'Category added successfully!');

            return response()->json([
                'status' => true,
                'message' => "Category created successfully"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    //for editing caterogy 
    public function edit() {}

    //for delete caterogy 
    public function distroy() {}
}
