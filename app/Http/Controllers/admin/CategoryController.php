<?php

namespace App\Http\Controllers\admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\TempImage;
use Illuminate\Support\Facades\File;

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

            //save image here
            if (!empty($request->image_id)) {
                $temImage = TempImage::find($request->image_id);
                $extArray = explode('.', $temImage->name);
                $ext = last($extArray);

                $newImageName = $category->id . "." . $ext;

                $spath = public_path() . '/temp/' . $temImage->name;
                $dpath = public_path() . '/uploads/category/' . $newImageName;
                File::copy($spath, $dpath);
                $category->image = $newImageName;
                $category->save();
            }
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
    public function edit($categoryId, Request $request)
    {

        $category = Category::find($categoryId);
        if (empty($category)) {
            return redirect()->route('categories.index');
        }


        return view('admin.category.edit', compact('category'));
    }

    //update the category
    public function update($categoryId, Request $request)
    {

        $category = Category::find($categoryId);
        if (empty($category)) {
            return response()->json([
                "status" => false,
                "notFound" => true,
                "message" => "category not found",

            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,' . $category->id . ',id'
        ]);

        if ($validator->passes()) {

            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->save();

            $oldImage = $category->image;

            //save image here
            if (!empty($request->image_id)) {
                $temImage = TempImage::find($request->image_id);
                $extArray = explode('.', $temImage->name);
                $ext = last($extArray);

                $newImageName = $category->id . '-' . time() . "." . $ext;

                $spath = public_path() . '/temp/' . $temImage->name;
                $dpath = public_path() . '/uploads/category/' . $newImageName;
                File::copy($spath, $dpath);
                $category->image = $newImageName;
                $category->save();

                //delete old image herer
                File::delete(public_path() . '/uploads/category/' . $oldImage);
            }

            session()->flash('success', 'Category Updated successfully!');

            return response()->json([
                'status' => true,
                'message' => "Category Updated successfully"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
    //for delete caterogy 
    public function distroy($categoryId, Request $request)
    {
        $category = Category::find($categoryId);
        if (empty($category)) {
            session()->flash('error', "category not found");
            return response()->json([
                'status' => true,
                'message' => "Category deleted successfully"
            ]);
            //return redirect()->route('categories.index');

        }


        //delete old image herer
        File::delete(public_path() . '/uploads/category/' . $category->image);

        $category->delete();

        session()->flash('success', "category deleted successfully");

        return response()->json([
            "status" => true,

            "message" => "category delete successfully",

        ]);
    }
}
