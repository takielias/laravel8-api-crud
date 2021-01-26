<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use App\MyResponseBuilder as MRB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ProductResource::collection(Product::paginate(25));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'title' => ['required'], // 'unique:products'
            'description' => ['required', 'string'],
            'price' => ['required', 'regex:/^\d*(\.\d{1,3})?$/', 'gt:0'],
        ];

        if ($request->hasFile('image')) {
            $rules['image'] = 'image|mimes:jpeg,png|max:1024';
        }

        $msg['image.max'] = 'Failed to upload an image. The image maximum size is 1MB.';
        $msg['image.image'] = 'The type of the uploaded file should be an image.';
        $msg['image.uploaded'] = 'Please Check Max File upload Size in php.ini';

        $request->validate($rules, $msg);

        $product = Product::create([
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
        ]);

        if ($request->hasFile('image')) {
            $path = 'images';
            $image_path = imageUpload($request->file('image'), $path);
            $product->image = $image_path;
            $product->save();
        }

        return MRB::asSuccess()
            ->withData([
                'product' => $product,
                'msg' => 'Product Has been added successfully !!!'
            ])
            ->withHttpCode(201)
            ->build();
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
