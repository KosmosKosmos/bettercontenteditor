<?php namespace Kosmoskosmos\Helper\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateRevisionsTable extends Migration
{

    public function up()
    {
        Schema::create('kosmoskosmos_content_history', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('item')->nullable();
            $table->text('content')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('kosmoskosmos_content_history');
    }

}
