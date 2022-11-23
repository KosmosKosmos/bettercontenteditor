<?php namespace KosmosKosmos\BetterContentEditor\Models;

use Model;

class Images extends Model
{
    protected $fillable = ['item', 'url'];
    public $table = 'kosmoskosmos_imageuploader';
}
