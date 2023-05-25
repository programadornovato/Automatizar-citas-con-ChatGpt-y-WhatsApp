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
            $this->tokenWa = 'EAANIQCCaMQIBAEy04T3GZAyeyuq9xF86U2aF03gbbeKsskTCiDgEOAyyojgLTYdJeKrKMiw1ZCnfZB6pwy1GjCq8STvDNFoNwULIenq4silHFobn0AUsHUInucvqZAyTLqexYMQskQZAtNQyaIGCzLwMIx5n3EwprbRoZC9Kc04qZArlcFTNZCebKptfQerwreZCl6HL9ACr1dwZDZD';
            $this->telefonoIDWa = '116907067953774';
        } else {
            // Coloca estos valores si remoto es false
            $this->apiKeyChatgpt = 'otro_valor';
            $this->tokenWa = 'otro_valor';
            $this->telefonoIDWa = 'otro_valor';
        }
    }
}