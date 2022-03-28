<?php

function preparePassword(string $password) : string{
    return hash('sha512', "f8es6d54165es4d1f652s41".$password."44dbjsbdsdhjd65dc0x85");
}