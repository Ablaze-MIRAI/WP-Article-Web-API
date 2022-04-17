<?php

//========================CONFIG========================
$WP_HOME_URL = null;
$ALLOW_ORIGIN = "*";
$DEV_OPTION = false;
//======================================================

header("Access-Control-Allow-Origin: {$ALLOW_ORIGIN}");

function Validation_URL($url){
    if(preg_match("/\/$/", $url)){
        return $url."wp-json/wp/v2/";
    }else{
        return $url."/wp-json/wp/v2/";
    }
}

function get_api(string $URL){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "WP_Article_Web_API Bot(https://ablaze.one)");
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

if(!isset($_GET["home"]) && empty($_GET["home"])){
    if($WP_HOME_URL === null){
        header("Content-Type: application/json; charset=utf-8");
        header("400 Bad Request");
        echo json_encode([
            "code" => 400,
            "message" => 'Not found WP_HOME_URL(Set $WP_HOME_URL or \'home\' parameter)'
        ]);
        exit;
    }else{
        $API_ENDPOINT = Validation_URL($WP_HOME_URL);
    }
}else{
    if($WP_HOME_URL === null){
        $API_ENDPOINT = Validation_URL($_GET["home"]);
    }else{
        $API_ENDPOINT = Validation_URL($WP_HOME_URL);
    }
}

if(isset($_GET["dev"])){
    if(!$DEV_OPTION){
        header("Content-Type: application/json; charset=utf-8");
        header("400 Bad Request");
        echo json_encode([
            "code" => 400,
            "message" => "'DEV OPTION' is disabled"
        ]);
        exit;
    }
    $API_ENDPOINT .= "categories";
    $res = json_decode(get_api($API_ENDPOINT), true);
    $template = file_get_contents("./devoptions/template.html");
    $insert_elements = "";
    foreach($res as $category){
        $insert_elements .= <<<EOF
        <tr>
            <th scope="row">{$category['id']}</th>
            <td>{$category['name']}</td>
        </tr>
        EOF;
    }
    echo str_replace(["<CONTENT:TABLE>", "<CONTENT:SITEURL>"], [$insert_elements, $_GET["home"]], $template);
    exit;
}

$categories = false;
$API_ENDPOINT .= "posts?_embed";
if(isset($_GET["categories"]) && !empty($_GET["categories"])){
    $API_ENDPOINT .= "&categories={$_GET['categories']}";
    $categories = $_GET["categories"];
}

$res = get_api($API_ENDPOINT);
if(!$res){
    header("Content-Type: application/json; charset=utf-8");
    header("500 Internal Server Error");
    echo json_encode([
        "code" => 500,
        "message" => "Failed to acquire data"
    ]);
    curl_close($ch);
    exit;
}

try{
    $res = json_decode($res, true);
}catch(Exception $e){
    header("Content-Type: application/json; charset=utf-8");
    header("500 Internal Server Error");
    echo json_encode([
        "code" => 500,
        "message" => "Failed to purse data"
    ]);
    curl_close($ch);
    exit;
}

$Articles = [];
foreach((array)$res as $Item){
    $description = explode("…", $Item["excerpt"]["rendered"])[0];
    $description = preg_replace("/<\/?.*>/", "", $description);
    $article = [
        "title" => $Item["title"]["rendered"],
        "date" => $Item["date"],
        "author" => $Item["_embedded"]["author"][0]["name"],
        "link" => $Item["guid"]["rendered"],
        "description" => $description,
        "image" => [
            "url" => $Item["_embedded"]["wp:featuredmedia"][0]["source_url"],
            "width" => $Item["_embedded"]["wp:featuredmedia"][0]["media_details"]["width"],
            "height" => $Item["_embedded"]["wp:featuredmedia"][0]["media_details"]["height"] 
        ],
    ];
    array_push($Articles, $article);
}

header("Content-Type: application/json; charset=utf-8");
echo json_encode([
    "code" => 200,
    "categories" => $categories,
    "items" => $Articles
]);
