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
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json(ProductResource::collection(Product::paginate(25)));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
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
                    if (validateBase64Image($value)) {
                        ImageManagerStatic::make($value);
                        return true;
                    }
                    return $fail("Invalid Image");
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

        return response()->json(['success' => true, 'msg' => 'Product has been added successfully.']);

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {

        $rules = [
            'title' => ['required'],
            'description' => ['required', 'string'],
            'price' => ['required', 'regex:/^\d*(\.\d{1,3})?$/', 'gt:0'],
        ];
        if ($request->has('image')) {
            $rules['image'] = ["required", function ($attribute, $value, $fail) use ($request) {
                try {
                    if (validateBase64Image($value)) {
                        ImageManagerStatic::make($value);
                        return true;
                    }
                } catch (\Exception $e) {
                    return $fail("Invalid Image");
                }
            }];
        }

        $msg['image.max'] = 'Failed to upload an image. The image maximum size is 1MB.';
        $msg['image.image'] = 'The type of the uploaded file should be an image.';
        $msg['image.uploaded'] = 'Please Check Max File upload Size in php.ini';

        $request->validate($rules, $msg);

        $product = Product::findOrFail($id);

        $product->update([
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
        ]);

        if ($request->has('image') && validateBase64Image($request->image)) {
            deleteImage($product->image);
            $path = 'images';
            $image_path = axiosFileUpload($request, $path);
            $product->image = $image_path;
            $product->save();
        }

        return response()->json(['success' => true, 'msg' => 'Product has been updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        if ($product) {
            deleteImage($product->image);
            $product->delete();
        }
        return response()->json(['success' => true, 'msg' => 'Product has been removed successfully.']);
    }
}
