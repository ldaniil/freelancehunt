<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

require_once dirname(__DIR__) . '/bootstrap.php';

Capsule::schema()->create('employer', function (Blueprint $table) {
	$table->integer('id')->primary();
	$table->string('login')->unique();
	$table->string('first_name')->nullable();
	$table->string('last_name')->nullable();
});

Capsule::schema()->create('skill', function (Blueprint $table) {
	$table->integer('id')->primary();
	$table->string('name');
});

Capsule::schema()->create('project', function (Blueprint $table) {
	$table->integer('id')->primary();
	$table->integer('employer_id');
	$table->integer('budget')->nullable();
	$table->string('name');
	$table->string('uri');

	$table
		->foreign('employer_id')
		->references('id')
		->on('employer')
		->onUpdate('cascade')
		->onDelete('restrict');
});

Capsule::schema()->create('project_skill', function (Blueprint $table) {
	$table->increments('id');
	$table->integer('project_id');
	$table->integer('skill_id');

	$table
		->foreign('project_id')
		->references('id')
		->on('project')
		->onUpdate('cascade')
		->onDelete('cascade');

	$table
		->foreign('skill_id')
		->references('id')
		->on('skill')
		->onUpdate('cascade')
		->onDelete('cascade');
});