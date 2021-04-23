<?php

namespace App\Http\Controllers\Front\Cart;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\CartModel;
use App\Model\ReviewModel;
use App\Model\ProductModel;
use App\Model\ProductImageModel;
use App\Model\ProductAdditionalData;
use Auth;

class CartController extends Controller
{
    public function showPage(Request $request)
    {
        return view('frontend.pages.cart');
    }


    public function cart_details_fx(Request $request)
    {
        if(Auth::user())
        {
            $cartQuery = CartModel::where(['user_id' => Auth::user()->id])->get();
        }
        else
        {
            $cartQuery = CartModel::where(['user_ip' => $_SERVER['REMOTE_ADDR']])->get();
        }

        $html['cart_details'] = "";
        /// cart query
        if(count($cartQuery) > 0)
        {
            foreach($cartQuery as $cQuery)
            {
                $productImage = ProductModel::where('id',$cQuery->product_id)->get();
                foreach($productImage as $pImages)
                {
                    $product_new_img = $pImages->product_thumbnail;
                }

                $msg = "";
                if($cQuery->additional_product_id > 0)
                {
                    $mainQuery = ProductAdditionalData::where('id',$cQuery->additional_product_id)->get();
                    foreach($mainQuery as $m1Query)
                    {
                        $msg_data = $m1Query->product_customize_name;
                        $msg_customize_link = $m1Query->product_customize_link;
                    }
                    $msg = '<span>Customize Msg: '.$msg_data.' Custom Link : '.$msg_customize_link.'</span>';
                }
                $html['cart_details'] .= '<tr>
                                            <td>
                                                <div class="cart_pic_area">
                                                    <div class="cart_pic_con">
                                                        <img src="'.str_replace('public','storage',asset($product_new_img)).'">
                                                        <a href="#"><i class="fa fa-times"></i></a>
                                                    </div>
                                                    <h3 class="p-details-popup">'.$cQuery->product_name.$msg.'</h3>
                                                </div>
                                            </td>
                                            <td><h3>$'.$cQuery->product_price.'</h3></td>
                                            <td>
                                                <div class="quantity-block">
                                                <a class="quantity-arrow-minus"> - </a>
                                                <input class="quantity-num" type="number" min=1 value="'.$cQuery->product_quantity.'" />
                                                <a class="quantity-arrow-plus"> + </a>
                                                </div>
                                            </td>
                                            <td><h3><span>$'.(($cQuery->product_price)*($cQuery->product_quantity)).'</span></h3></td>
                                        </tr>';
            }
        }
        else
        {
            $html['cart_details'] .= '<tr><td colspan=4><p class="loading_text"> <i class="fa fa-times"></i> No data in cart </p></td></tr>';
        }

        echo json_encode($html);
        
    }

    public function total_cart_price_fx(Request $request)
    {
        if(Auth::user())
        {
            $cartTotalpriceQuery = CartModel::where(['user_id' => Auth::user()->id])->get();
        }
        else
        {
            $user_ip = $_SERVER['REMOTE_ADDR'];
            $cartTotalpriceQuery = CartModel::where(['user_ip' => $user_ip])->get();
        }

        if(count($cartTotalpriceQuery) > 0)
        {
            $html_cart_add_price = 0;
            foreach($cartTotalpriceQuery as $cTPQuery)
            {
                $html_cart_add_price += (($cTPQuery->product_price)*($cTPQuery->product_quantity));
            }

            echo json_encode($html_cart_add_price);
        }
        else
        {
            $html_cart_add_price = 0;
            echo json_encode($html_cart_add_price);
        }
    }
}
