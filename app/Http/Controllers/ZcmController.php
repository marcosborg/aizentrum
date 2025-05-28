<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\PrestashopApi;
use Illuminate\Http\Request;
use App\Http\Controllers\Traits\Iftech;

class ZcmController extends Controller
{
    use Iftech;
    use PrestashopApi;

    public function categories()
    {

        //$access_token = $this->login();

        $categories = $this->zcmCategories();

        return $categories;
    }

    public function subCategories($phase_id)
    {

        $access_token = $this->login();

        $subCategories = $this->zcmSubCategories($access_token, $phase_id);

        return $subCategories;
    }

    public function zcmUpdateState(Request $request)
    {
        $access_token = $this->login();

        return $this->followup($access_token, $request->id, $request->phase_id, $request->status_id, $request->obs, $request->email);
    }

    public function checkZcmStock($prestashop_id)
    {
        $access_token = $this->login();

        return $this->checkStock($access_token, $prestashop_id);
    }

    public function prestashopProduct($prestashop_id)
    {

        $product = $this->product($prestashop_id)->products[0];
        $product->manufacturer_name = $this->manufacturer($product->id_manufacturer)->manufacturers[0]->name;
        $product->category_name = $this->category($product->id_category_default)['categories'][0]['name'][0]['value'];

        return $product;
    }

    public function prestashopCreateStock(Request $request)
    {
        $access_token = $this->login();
        return $this->createStock($access_token, $request);
    }

    public function prestashopUpdateStock(Request $request)
    {
        $access_token = $this->login();
        return $this->updateStock($access_token, $request);
    }
}
