<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Support\Str;

trait PrestashopApi
{

    public function zentrumSearch($website, $search)
    {
        $encodedSearch = urlencode($search);

        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => $website . '/api/search?query=' . $encodedSearch . '&language=1&output_format=JSON&display=full',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic SlRVUjNDS0JXN0dWSzFVUUdKNzlSMk5VVU1QWUZNNkI6'
                ),
            )
        );

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response);
    }

    public function categories()
    {
        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => 'https://techniczentrum.com/api/categories?language=1&output_format=JSON&display=full',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic SlRVUjNDS0JXN0dWSzFVUUdKNzlSMk5VVU1QWUZNNkI6',
                ),
            )
        );

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);
    }

    public function manufacturers()
    {

        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => 'https://techniczentrum.com/api/manufacturers?language=1&output_format=JSON&display=full',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic SlRVUjNDS0JXN0dWSzFVUUdKNzlSMk5VVU1QWUZNNkI6',
                ),
            )
        );

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);

    }

    public function category($category_id)
    {

        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => 'https://techniczentrum.com/api/categories/' . $category_id . '?output_format=JSON&display=full',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic SlRVUjNDS0JXN0dWSzFVUUdKNzlSMk5VVU1QWUZNNkI6',
                ),
            )
        );

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response, true);

    }

    public function manufacturer($manufacturer_id)
    {

        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => 'https://techniczentrum.com/api/manufacturers/' . $manufacturer_id . '?output_format=JSON&display=full',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic SlRVUjNDS0JXN0dWSzFVUUdKNzlSMk5VVU1QWUZNNkI6',
                ),
            )
        );

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);


    }

    public function newProduct($request)
    {

        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => 'https://techniczentrum.com/api/products',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '<prestashop xmlns:xlink="http://www.w3.org/1999/xlink">
  <product>
    <id_manufacturer>' . $request->id_manufacturer . '</id_manufacturer>
    <id_category_default>' . $request->id_category . '</id_category_default>
    <id_tax_rules_group>111</id_tax_rules_group>
    <type>simple</type>
    <id_shop_default>1</id_shop_default>
    <reference>' . $request->reference . '</reference>
    <mpn>' . $request->reference . '</mpn>
    <condition>used</condition>
    <state>1</state>
    <price>' . $request->price . '</price>
    <active>0</active>
    <show_price>1</show_price>
    <meta_description>
      <language id="1">' . $request->name[0] . '</language>
      <language id="3">' . $request->name[1] . '</language>
      <language id="5">' . $request->name[2] . '</language>
      <language id="8">' . $request->name[3] . '</language>
    </meta_description>
    <meta_title>
      <language id="1">' . $request->name[0] . '</language>
      <language id="3">' . $request->name[1] . '</language>
      <language id="5">' . $request->name[2] . '</language>
      <language id="8">' . $request->name[3] . '</language>
    </meta_title>
    <link_rewrite>
      <language id="1">' . Str::slug($request->name[0]) . '</language>
      <language id="3">' . Str::slug($request->name[1]) . '</language>
      <language id="5">' . Str::slug($request->name[2]) . '</language>
      <language id="8">' . Str::slug($request->name[3]) . '</language>
    </link_rewrite>
    <name>
      <language id="1">' . $request->name[0] . '</language>
      <language id="3">' . $request->name[1] . '</language>
      <language id="5">' . $request->name[2] . '</language>
      <language id="8">' . $request->name[3] . '</language>
    </name>
    <description>
      <language id="1">' . $request->description[0] . '</language>
      <language id="3">' . $request->description[1] . '</language>
      <language id="5">' . $request->description[2] . '</language>
      <language id="8">' . $request->description[3] . '</language>
    </description>
    <associations>
      <categories>
        <category>
          <id>' . $request->id_category . '</id>
        </category>
      </categories>
    </associations>
  </product>
</prestashop>
',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/xml',
                    'Authorization: Basic SlRVUjNDS0JXN0dWSzFVUUdKNzlSMk5VVU1QWUZNNkI6',
                ),
            )
        );

        $response = curl_exec($curl);

        curl_close($curl);

        // Analisar a resposta XML
        $xmlObject = simplexml_load_string($response);

        // Extrair o valor do ID
        $id = (string) $xmlObject->product->id;

        // Retornar o ID como resposta JSON
        return response()->json(['id' => $id], 200);

    }

    public function product($prestashop_id)
    {

        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => 'https://techniczentrum.com/api/products/' . $prestashop_id . '?output_format=JSON&display=full',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic SlRVUjNDS0JXN0dWSzFVUUdKNzlSMk5VVU1QWUZNNkI6',
                ),
            )
        );

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);

    }    

}
