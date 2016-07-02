<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('requests', function(Blueprint $table)
		{
			$table->increments('id');
			$table->dateTime('start_time');
			$table->dateTime('end_time');
			$table->string('place');
			$table->string('description');
			$table->integer('category_id')->unsigned();
			$table->integer('author_id')->unsigned();
			$table->timestamps();

			$table->foreign('category_id')->references('id')->on('request_categories');
			$table->foreign('author_id')->references('id')->on('users');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('requests');
	}

}
