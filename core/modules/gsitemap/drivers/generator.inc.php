<?php

function gsitemap_generate () {
    global
        $SYS_LANGUAGES,
        $_TEXTID,
        $EDITLANGUAGE,
        $LANGUAGES,
        $COREROOT,
        $CURRENTPLATFORM,
        $MOD_GSITEMAP,
        $_dtd_types;
    $XML = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

    $type = ($MOD_GSITEMAP["odd"])?$MOD_GSITEMAP["odd"]:"category";
    $products = $CURRENTPLATFORM->getAllOfType($type);

    foreach ($products AS $key => $product) {
        $data_ = $product->translate_object_data();
        $lngcnt = count($LANGUAGES);
        foreach ($LANGUAGES AS $lang) {
            $skip = false;
            $url = "http://{$_SERVER["HTTP_HOST"]}/";
            if ($MOD_GSITEMAP["seo"] == 1 && $data_["tid"]) {
                if ($lngcnt > 1)
                $url .= "{$lang}/";
                $url .= "{$data_["tid"]}.html";
            } else if (!$MOD_GSITEMAP["seo"]) {
                $url .= "index.php?id={$product->id}";
                if ($lngcnt > 1)
                $url .= "&lang={$lang}";
            } else {
                $skip = true;
            }
            if (!$skip) {
                $time = date("r", $product->_date);
                $XML .= "\t<url>\n\t\t<loc>{$url}</loc>\n\t\t<lastmod>{$time}</lastmod>\n\t</url>\n";
            }
        }
    }
    $XML .= "</urlset>\n";
    
    return $XML;
}

?>