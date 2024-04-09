<?php

namespace src\Controllers\Admin;

use src\Commons\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $this->renderViewAdmin('dashboard.index', ['title' => 'This is Dashboard']);
    }
}