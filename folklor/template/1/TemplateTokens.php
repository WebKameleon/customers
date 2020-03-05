<?php

if (!class_exists ('TemplateTokens')) {
class TemplateTokens extends Tokens {
 

    public function code_change($html,$style=null)
    {
        $html=preg_replace('~http://static.folklor.pl([^"]+swf")~','\1',$html);
        
        return $html;
    }
    
    public function noquote($txt) {
        return str_replace('"',"'",$txt);
    }
}}
