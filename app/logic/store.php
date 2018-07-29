<?php

namespace core\verify;


interface  store
{
    public function setValue($identifying,$value);

    public function getValue($identifying);



}