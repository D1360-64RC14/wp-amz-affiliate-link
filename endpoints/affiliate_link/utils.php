<?php

const REGEXP_DP_PATH = '/^\/.*\/dp\/(?P<asin>[A-Z\d]*)\/?(\/ref=.*)?$/';
const REGEXP_GP_PATH = '/^\/gp\/product\/(?P<asin>[A-Z\d]*)\/?$/';
const REGEXP_AMZ_HOST = '/^(www\.)?amazon\.com(\.\w*)?$/';

/**
 * @param string $asin Código de produto Amazon (ex: B07T8XCPSP).
 * @param string $tag  Tag de afiliado.
 *
 * @return array Objeto contendo parâmetros a ser enviado no GET.
 */
function genLongLinkObject(string $asin, string $tag): array {
    $body = [
        'adUnitDescription' => 'Product links Text only link',
        'adUnitSubType' => 'FullLinks',
        'adUnitType' => 'TEXT',
        'asin' => $asin, // example: 'B07T8XCPSP'
        'createTime' => time() * 1000, // epoch in ms
        'destinationType' => 'ASIN',
        'linkCode' => 'll1', // ll1 = Long Link 1
        'marketplaceId' => 'A2Q3Y263D00KWC', // https://docs.developer.amazonservices.com/en_UK/dev_guide/DG_Endpoints.html
        'p_parameter' => 'SS v2',
        'store' => $tag,
        'tag' => $tag,
        'test_name' => 'SiteStripe V3.0',
        'toolCreation' => 'SS',
    ];

    return [
        'linkId' => md5(json_encode($body)),
        'trackingParams' => $body,
        '_v' => 1,
    ];
}

/**
 * Extrai do path de uma URL de produto
 * da Amazon o seu código de produto (ASIN).
 *
 * @param string $path Path de produto da Amazon.
 *
 * @return string Código de produto (ASIN).
 */
function extractAsinFromPath(string $path): string {
    if (preg_match(REGEXP_GP_PATH, $path, $gp_group_matches)) {
        return $gp_group_matches['asin'];
    }

    preg_match(REGEXP_DP_PATH, $path, $dp_group_matches);
    return $dp_group_matches['asin'];
}

/**
 * Atualiza o objeto de query com as keys necessárias.
 *
 * @param array $url_query  Objeto contendo a query da URL.
 * @param string $link_code Tipo do link. 'll1' para 'Long Link 1', 'sl1' para 'Short Link 1'.
 * @param string $link_id   Hash md5 do link de objeto em JSON. Gerada em `genLongLinkObject()`.
 * @param string $tag       Tag de afiliado.
 */
function updateUrlQuery(array &$url_query, string $link_code, string $link_id, string $tag) {
    $url_query['ref_'] = 'as_li_ss_tl';
    $url_query['linkCode'] = $link_code;
    $url_query['language'] = 'pt_BR';
    $url_query['tag'] = $tag;
    $url_query['linkId'] = $link_id;
}

/**
 * Normaliza o path de uma das URLs de produto da Amazon.
 * Após o código de produto há a seguinte continuação que será removida: '/ref=p13n...'.
 *
 * https://www.amazon.com.br/Echo-Dot-3ª-Geração-Cor-Preta/dp/B07PDHSJ1H/ref=p13n_ds_purchase_sim_1p_dp_desktop_5/130-1374962-7819459
 *
 * @param string $path Path de produto da Amazon.
 *
 * @return void
 */
function sanitizeUrlPath(string &$path) {
    if (preg_match(REGEXP_DP_PATH, $path)) {
        $ref_position = strpos($path, '/ref=');

        if ($ref_position !== false) {
            $path = substr($path, 0, $ref_position);
        }
    }
}

/**
 * Realiza uma requisição para ativação do link de afiliado.
 *
 * @param array $generated_object Objeto de saída da função genLongLinkObject().
 *
 * @return void
 */
function activateAffiliateUrl(array $generated_object) {
    $res = wp_remote_get(
        'https://fls-na.amazon.com/1/assoc-links/1/OP/' . json_encode($generated_object),
        [
            'headers' => [
                'accept' => 'image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
                'accept-language' => 'pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
                'sec-fetch-dest' => 'image',
                'sec-fetch-mode' => 'no-cors',
                'sec-fetch-site' => 'cross-site',
                'Referer' => 'https://www.amazon.com.br/',
                'Referrer-Policy' => 'strict-origin-when-cross-origin',
            ],
        ]
    );

    $status_code = $res['response']['code'];

    if ($status_code == 200) {
        return;
    }
}

/**
 * Retorna uma URL string de um objeto contendo host, path e query.
 *
 * @param array $url_object
 *
 * @return string
 */
function join_url_object(array $url_object): string {
    $query_string = http_build_query($url_object['query']);

    return 'https://' . $url_object['host'] . $url_object['path'] . '?' . $query_string;
}
