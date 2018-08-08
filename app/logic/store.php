<?php

namespace app\logic;


interface  store
{
    public function setValue($identifying,$value);

    public function getValue($identifying);



}