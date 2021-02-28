<?php

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;

if(!function_exists('getParams')) {
    /**
     * @param \Illuminate\Http\Request $request
     * @param string $sortBy
     * @param array $mores
     * @return array|\Illuminate\Http\JsonResponse|void
     */
    function getParams(Request $request, $sortBy = 'name,created_at', $mores = [])
    {
        $validates = [
            'page' => 'nullable|digits_between:1,10',
            'sort' => 'nullable|in:'.$sortBy,
            'type' => 'nullable|in:asc,desc',
            'query' => 'nullable|string|max:50',
            'item' => 'nullable|numeric|in:10,20,50,100',
        ];

        $validates = !empty($mores) ? array_merge($validates, $mores) : $validates;
        $validator = Validator::make($request->all(), $validates);
        if ($validator->fails()) {
            response()->json([
                'message' => 'The given data was invalid.',
                'errors'  => $validator->errors()
            ],Response::HTTP_UNPROCESSABLE_ENTITY)->throwResponse();
        }

        $sort = $request->get('sort') ?: 'created_at';
        $type = $request->get('type') ?: 'desc';
        $query = $request->get('query');
        $item = $request->get('item') ?: 10;
        return [$sort, $type, $query, $item];
    }
}

if(!function_exists('fileUpload')) {
    /**
     * @param UploadedFile $file
     * @param Product|null $product
     * @return null
     */
    function fileUpload(UploadedFile $file, Product $product = null)
    {
        $name = Product::getUniqueImageName($file->getClientOriginalExtension());
        Storage::disk('public')->put($name, Image::make($file));
        if($product != null && $product->image != null) {
            fileDelete($product->image);
        }
        return $name;
    }
}

if(!function_exists('fileDelete')) {
    /**
     * @param $filePath
     */
    function fileDelete($filePath)
    {
        if(Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
    }
}
