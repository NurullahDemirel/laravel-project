<?php
namespace App\Enums;

enum LoginWith:string{
    case Google = 'Google';
    case Github = 'Github';
    case Facebook = 'Facebook';
    case Twitter = 'Twitter';
    case Apple = 'Apple';
    case DEFAULT = 'NORMAL';
}
