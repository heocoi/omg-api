<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('first_name', 16);
			$table->string('last_name', 16);
			$table->string('email')->unique();
			$table->string('country', 32);
			$table->string('age', 3);
			$table->boolean('gender');
			$table->string('language', 256);
			$table->boolean('type');
			$table->string('password', 60);
			$table->longText('introduction');
			$table->rememberToken();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}
