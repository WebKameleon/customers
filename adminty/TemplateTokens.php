<?php

if (!class_exists ('TemplateTokens')) {
class TemplateTokens extends Tokens {
    
    
    public function smektize($txt) {
        if($this->mode>1)
            return $txt;
        return preg_replace('/{{([^}]+)}}/','<span class="init">{{\\1}}</span>',$txt);
    }
    
    public function classInitIfSmekta($txt) {
        if($this->mode>1 || !strstr($txt,'{{'))
            return '';
        return ' class="init"';
    }
}}
