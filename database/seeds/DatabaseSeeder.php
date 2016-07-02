<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\RequestCategory;
use Cmgmyr\Messenger\Models\Thread;

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		User::create([
			'email' => 'foo@bar.com',
			'password' => Hash::make('foobar')
		]);

		User::create([
			'email' => 'ho@ge.com',
			'password' => Hash::make('hoge')
		]);

		Thread::create([
			'subject' => 'foo',
		]);

		RequestCategory::create([
			'title' => 'Please travel with me'
		]);

		RequestCategory::create([
			'title' => 'Please shopping with me'
		]);

		RequestCategory::create([
			'title' => 'Please talk with me'
		]);

	}

}
