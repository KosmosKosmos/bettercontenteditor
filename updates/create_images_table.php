<?php namespace Kosmoskosmos\Helper\Updates;

use KosmosKosmos\BetterContentEditor\Models\Settings;
use Schema;
use October\Rain\Database\Updates\Migration;

class CreateImagesTable extends Migration
{

    public function up()
    {
        Schema::create('kosmoskosmos_imageuploader', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('item')->nullable();
            $table->string('url')->nullable();
            $table->timestamps();
        });
        Settings::set('enabled_buttons', ["bold", "italic", "link", "align-left", "align-center", "align-right", "subheading", "subheading3"]);
    }

    public function down()
    {
        Schema::dropIfExists('kosmoskosmos_imageuploader');
    }

}
