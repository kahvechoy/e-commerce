<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $category = Category::select('id', 'name', 'image')->latest()->get();
        
        return view('pages.admin.category.index', compact(
            'category'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'image' => 'required|image|mimes:png,jpg,jpeg|max:2048'
        ]);

        try{
            $data = $request->all();

            //store image
            $image = $request->file('image');
            $image->storeAs('public/category', $image->hashName());

            $data ['image'] = $image->hashName();
            $data['slug'] = Str::slug($request->name);

            Category::create($data);

            //dd($category);

            return redirect()->back()->with('success', 'Category added successfully');

        } catch(Exception $e){
            //dd($e->getMessage());
            return redirect()->back()->with('error', 'Failed to add category');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //Get data by id
        $category = Category::find($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //Melakukan Validasi
        $this->validate($request, [
            'name' => 'required',
            'image' => 'image|mimes: jpeg,png,jpg|max:5048'
        ]);

        try {
            //get data by id
            $category = Category::find($id);
            //jika image kosong
            if ($request->file('image') == '') {
                $category->update([
                    'name' => $request->name,
                    'slug' => Str::slug($request->name)
                ]);
                return redirect()->route('category.index');
            } else {
                // jika gambar ingin di update
                // Hapus image lama
                Storage::disk('local')->delete('public/category/' . basename($category->image));

                // Upload image baru
                $image = $request->file('image');
                $image->storeAs('public/category/', $image->hashName());

                // Update data
                $category->update([
                    'name' => $request->name,
                    'slug' => Str::slug($request->name),
                    'image' => $image->hashName()
                ]);

                return redirect()->back()->with('succes', 'Category Berhasil di Edit');
            }
        } catch (Exception $th) {
            return redirect()->back()->with('error', 'Failed Something wrong');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            //find category by id
            $category = Category::find($id);

            //delete image
            Storage::disk('local')->delete('public/category/' .basename($category->image));

            //delete category
            $category->delete();

            return redirect()->back()->with('success', 'Category deleted successfully');

        } catch(Exception $e) {
            dd($e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete category');
        }
    }
}
