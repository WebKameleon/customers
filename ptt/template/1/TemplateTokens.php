<?php

if (!class_exists ('TemplateTokens')) {
class TemplateTokens extends Tokens {

    public function ptt_date($epoch,$time=1)
    {
        $months=[
                 'pl'=>['stycznia','lutego','marca','kwietnia','maja','czerwca','lipca','sierpnia','września','października','listopada','grudnia'],
                 'en'=>['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'],
                ];

        $lang=$this->webtd['lang'];
        return date('j',$epoch).' '.$months[$lang][date('n',$epoch)-1].date($time?' Y H:i':' Y',$epoch);
    }

}}
