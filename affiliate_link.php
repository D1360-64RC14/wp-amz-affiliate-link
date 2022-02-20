<?php
/**
 * --- ATENÇÃO! ---
 * Defina sua tag de afiliado em AFFILIATE_TAG.
 *
 * Para este plugin ser acessível com o url '/wp-json/...',
 * vá em Configurações -> Links Permanentes, a área
 * 'Configurações comuns' não pode estar em 'Padrão'.
 *
 * Exemplos de formatos de URL de produto válidas:
 * - https://www.amazon.com.br/gp/product/B08C1K6LB2
 * - https://www.amazon.com.br/Echo-Dot-3ª-Geração-Cor-Preta/dp/B07PDHSJ1H
 * - https://www.amazon.com.br/Echo-Dot-3ª-Geração-Cor-Preta/dp/B07PDHSJ1H/ref=p13n_ds_purchase_sim_1p_dp_desktop_5/130-1374962-7819459
 * - https://www.amazon.com.br/dp/B09FTLKBGX
 *
 * --- Documentação da API ---
 * Método: GET
 * Path: /wp-json/api/v1/affiliate-link
 * Parâmetros: url, tag, simple
 *
 * url:    link de produto da Amazon que será transformado em link de afiliado;
 * tag:    (opcional) tag de afiliado a ser utilizada;
 * simple: (opcional) simplifica a saída de erros da API -- útil para bots.
 *
 * --- Exemplos de Uso ---
 * /wp-json/api/v1/affiliate-link?url=https%3A%2F%2Fwww.amazon.com.br%2Fgp%2Fproduct%2FB08C1K6LB2
 * /wp-json/api/v1/affiliate-link?url=https%3A%2F%2Fwww.amazon.com.br%2Fgp%2Fproduct%2FB08C1K6LB2&simple
 * /wp-json/api/v1/affiliate-link?url=https%3A%2F%2Fwww.amazon.com.br%2Fgp%2Fproduct%2FB08C1K6LB2&tag=example-tag-20
 * /wp-json/api/v1/affiliate-link?url=https%3A%2F%2Fwww.amazon.com.br%2Fgp%2Fproduct%2FB08C1K6LB2&tag=example-tag-20&simple
 *
 * @package Affiliate_link
 *
 * @wordpress-plugin
 * Plugin Name: Amazon Product Affiliate Link Generator
 * Description: Este plugin cria um endpoint REST que transforma links de produtos da Amazon em links de afiliado reproduzindo o comportamento do SiteStripe
 * Version:     1.0.0
 * Author:      Diego Garcia
 * Author URI:  https://github.com/D1360-64RC14
 * Plugin URI:  https://github.com/D1360-64RC14/wp-amz-affiliate-link
 * License:     Apache-2.0
 * License URI: https://www.apache.org/licenses/LICENSE-2.0.txt
 * Text Domain: affiliate_link
 */

if (!function_exists('add_action')) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit();
}

/**
 * Tag de afiliado.
 * Caso esteja vazia, o parâmetro de URL 'tag' será obrigatório.
 * Exemplo:
 * const AFFILIATE_TAG = 'example-tag-20';
 */
const AFFILIATE_TAG = '';

define('AFFILIATE_LINK_VERSION', '1.0.0');
define('AFFILIATE_LINK_PLUGIN_DIR', plugin_dir_path(__FILE__));

require_once AFFILIATE_LINK_PLUGIN_DIR . 'endpoints/affiliate_link/index.php';
require_once AFFILIATE_LINK_PLUGIN_DIR . 'endpoints/affiliate_link/utils.php';
require_once AFFILIATE_LINK_PLUGIN_DIR . 'endpoints/affiliate_link/validators/params.validator.php';
require_once AFFILIATE_LINK_PLUGIN_DIR . 'endpoints/affiliate_link/validators/product_url.validator.php';
require_once AFFILIATE_LINK_PLUGIN_DIR . 'endpoints/affiliate_link/errors/basic.error.php';
require_once AFFILIATE_LINK_PLUGIN_DIR . 'endpoints/affiliate_link/errors/product_url.error.php';
require_once AFFILIATE_LINK_PLUGIN_DIR . 'endpoints/affiliate_link/errors/url_params.error.php';

add_action('rest_api_init', 'init_endpoint_affiliate_link');
