<?php

/**
 * Verifica se a URL de produto é válida.
 * Caso sejam, não fará nada, caso não sejam,
 * retornará uma exception.
 *
 * @param string $product_url URL de produto da Amazon em string.
 *
 * @return void
 * @throws ProductURLError
 */
function validate_product_url(string $product_url) {
    $parsed_url = parse_url($product_url);

    if (empty($parsed_url['host'])) {
        throw new ProductURLError('Hostname inválido');
    }

    if (empty($parsed_url['path'])) {
        throw new ProductURLError('O path é obrigatório');
    }

    if (!preg_match(REGEXP_AMZ_HOST, $parsed_url['host'])) {
        throw new ProductURLError('Hostname inválido. Deve ser um produto da Amazon');
    }

    if (
        !preg_match(REGEXP_DP_PATH, $parsed_url['path']) &&
        !preg_match(REGEXP_GP_PATH, $parsed_url['path'])
    ) {
        throw new ProductURLError('Path de produto inválido. Deve ser um produto da Amazon');
    }
}
