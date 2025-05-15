<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\View\View;

class AdminPanelController extends BaseController
{
    public function showAdminPanel(): View
    {
        return view('admin-panel');
    }
}