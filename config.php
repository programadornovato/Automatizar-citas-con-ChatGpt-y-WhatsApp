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
            $this->tokenWa = 'EAANIQCCaMQIBAOr1C62wcz0c4cOPxUfNMFTkpHWzp1DcUlaqlWM91tzfyky0oAwxOELgmkp0DPd5I3yHjwyZCUpImpsypk7W5eq39HhtBFtXc1j3j2uzZCZAEZAcbK8jQkikZATKZAKmfckmeKvIEaIvnzRg2ncCZA5jOY6oWAqNkrt4FpKxkAwutuvrNh1BeusIY58smCZB0wZDZD';
            $this->telefonoIDWa = '116907067953774';
        } else {
            // Coloca estos valores si remoto es false
            $this->apiKeyChatgpt = 'otro_valor';
            $this->tokenWa = 'otro_valor';
            $this->telefonoIDWa = 'otro_valor';
        }
    }
}