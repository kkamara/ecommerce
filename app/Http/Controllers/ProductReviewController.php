<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProductReview;
use App\Product;
use Validator;

class ProductReviewController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Product $product, Request $request)
    {
        

        $user = \App\User::attemptAuth();

        if($product->didUserReviewProduct($user->id) === FALSE)
        {
            if($product->didUserPurchaseProduct($user->id))
            {
                $validator = Validator::make($request->all(), [
                    'rating' => 'required|integer|min:0|max:5',
                    'content' => 'max:600',
                ]);

                if(empty($validator->errors()->all()))
                {
                    $score = filter_var($request->input('rating'), FILTER_SANITIZE_NUMBER_INT);
                    $content = $request->input('content', FILTER_SANITIZE_STRING);

                    $data = array(
                        'user_id' => $user->id,
                        'score' => $score,
                        'content' => $content,
                    );

                    $product->productReview()->create($data);
                    $message = "Successful";

                    return response()->json(compact("product", "message"));
                }
                else
                {
                    return response()->json([
                        'errors' => $validator->errors()->all(),
                        "message" => "Unsuccessful"
                    ], config("app.http.bad_request"));
                }
            }
            else
            {
                return response()->json([
                    "message" => "Unauthorized"
                ], config("app.http.unauthorized"));
            }
        }
        else
        {
            return response()->json([
                'errors' => [
                    "You have already reviewed this product."
                ],
                "message" => "Unsuccessful"
            ], config("app.http.bad_request"));
        }
    }
}
