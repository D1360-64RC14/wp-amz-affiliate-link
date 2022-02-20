<?php

/**
 * Verifica se os parâmetros de URL são existentes.
 * Caso sejam, não fará nada, caso não sejam,
 * retornará uma exception.
 *
 * @param array $url_params Parâmetros de URL.
 *
 * @return void
 * @throws URLParamsError
 */
function validate_url_params(array $url_params) {
    // Verifica se ambos os campos existem.
    if (empty($url_params['tag'])) {
        throw new URLParamsError('Parâmetro \'tag\' obrigatório');
    }

    if (empty($url_params['url'])) {
        throw new URLParamsError('Parâmetro \'url\' obrigatório');
    }
}
