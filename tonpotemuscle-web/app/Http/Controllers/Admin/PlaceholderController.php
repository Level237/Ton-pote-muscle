<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PlaceholderController extends Controller
{
    public function __invoke(): View
    {
        $pages = [
            'admin.profile' => [
                'title' => 'Profil',
                'icon' => 'fa-solid fa-user',
                'description' => 'Informations du compte administrateur.',
            ],
        ];

        $page = $pages[request()->route()->getName()] ?? [
            'title' => 'Admin',
            'icon' => 'fa-solid fa-layer-group',
            'description' => '',
        ];

        return view('admin.placeholder', $page);
    }
}
