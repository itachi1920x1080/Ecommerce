<?php

namespace App\Http\Controllers;

abstract class Controller
{

    // អនុញ្ញាតឱ្យបញ្ចូលទិន្នន័យទៅក្នុងឈរ (Columns) ទាំងនេះ
    protected $fillable = ['code', 'type', 'value', 'expires_at', 'is_active'];
}
