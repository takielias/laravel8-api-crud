<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(ProductResource::collection(Product::paginate(25)));

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
        if ($request->has('image')) {
            $rules['image'] = ["required", function ($attribute, $value, $fail) use ($request) {
                try {
                    ImageManagerStatic::make($value);
                    return true;
                } catch (\Exception $e) {
                    return $fail("Invalid Image");
                }
            }];
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

        if ($request->has('image')) {
            $path = 'images';
            $image_path = axiosFileUpload($request, $path);
            $product->image = $image_path;
            $product->save();
        }

        return response()->json(['success' => true, 'msg' => 'Product has been addedd successfully.']);

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return response()->json(new ProductResource($product));
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
        Product::destroy([$id]);
        return response()->json(['success' => true, 'msg' => 'Product has been removed successfully.']);
    }
}
