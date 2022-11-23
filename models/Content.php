<?php namespace KosmosKosmos\BetterContentEditor\Models;

use Model;
use BackendAuth;
use October\Rain\Database\Traits\Revisionable;

class Content extends Model
{
    use Revisionable;

    protected $revisionable = ['item', 'content'];
    protected $fillable = ['item', 'content'];
    public $table = 'kosmoskosmos_content_history';
    public $revisionableLimit = 3;

    public $morphMany = [
        'revision_history' => ['System\Models\Revision', 'name' => 'revisionable']
    ];

    public function getRevisionableUser()
    {
        return BackendAuth::getUser()->id;
    }

}
