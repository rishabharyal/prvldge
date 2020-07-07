<?php

namespace App\Contracts;

interface BaseRepository
{
	public function get();
	public function create();
	public function update($id);
	public function delete($id);
}