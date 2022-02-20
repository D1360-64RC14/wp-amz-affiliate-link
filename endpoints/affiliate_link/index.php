<?php

/**
 * @return false|void
 */
function init_endpoint_affiliate_link() {
    if (!function_exists('register_rest_route')) {
        return false;
    }

    register_rest_route('api/v1', '/affiliate_link', [
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'get_affiliate_link',
    ]);
}

function get_affiliate_link(WP_REST_Request $request) {
    $request->set_default_params(['tag' => AFFILIATE_TAG]);

    $url_params = [
        'tag' => $request->get_param('tag'),
        'url' => $request->get_param('url'),
        'simple' => $request->get_param('simple'),
    ];

    try {
        validate_url_params($url_params);
        validate_product_url($url_params['url']);
    } catch (URLParamsError | ProductURLError $error) {
        // Caso o parâmetro 'simple' exista,
        // não retornará uma mensagem de erro em JSON.
        if (isset($url_params['simple'])) {
            return $error->simple_ensure_response();
        }
        return $error->ensure_response();
    }

    $product_url = parse_url($url_params['url']);

    if (!isset($product_url['query'])) {
        $product_url['query'] = [];
    }
    parse_str($product_url['query'], $product_url['query']);

    sanitizeUrlPath($product_url['path']);

    $asin = extractAsinFromPath($product_url['path']);
    $long_link_data = genLongLinkObject($asin, $url_params['tag']);

    updateUrlQuery(
        $product_url['query'],
        $long_link_data['trackingParams']['linkCode'],
        $long_link_data['linkId'],
        $url_params['tag']
    );

    $result_url = join_url_object($product_url);
    activateAffiliateUrl($long_link_data);

    // Gambiarra para a API retornar um plain
    // text ao invés de um json-like.
    print_r($result_url);
    exit();
}
