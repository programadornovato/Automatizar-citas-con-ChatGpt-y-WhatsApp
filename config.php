<?php
class Config {
    public $apiKeyChatgpt;
    public $tokenWa;
    public $telefonoIDWa;
    public $remoto;

    public function __construct() {
        $remoto=true;
        if ($remoto) {
            // Coloca estos valores si remoto es true
            $this->apiKeyChatgpt = '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';
            $this->tokenWa = 'EAANIQCCaMQIBAM1cIHPp640kaa6fbVXNxyoiGEAQYubPdlI0PsmfTEiZBWZCENX1ZAyGDIC3IHiu3meSGiDU6tTg2Kyam7yjOPVXZAyYpNkrB4byIldswdGmwv1nAZABD3MaO2a5nD4TPe3Dhgbbpd9AGpzylWyrhp0ZBqKFNIvkcwKh8t87QlzZC34fsmpNlni1Eu9slLLRwZDZD';
            $this->telefonoIDWa = '116907067953774';
        } else {
            // Coloca estos valores si remoto es false
            $this->apiKeyChatgpt = 'otro_valor';
            $this->tokenWa = 'otro_valor';
            $this->telefonoIDWa = 'otro_valor';
        }
    }
}