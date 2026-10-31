<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PlaceholderController extends Controller
{
    public function __invoke(): View
    {
        $pages = [
            'admin.enrollments.index' => [
                'title' => 'Inscrits',
                'icon' => 'fa-solid fa-user-group',
                'description' => 'Membres inscrits à un programme.',
            ],
            'admin.rankings.index' => [
                'title' => 'Classements',
                'icon' => 'fa-solid fa-trophy',
                'description' => 'Classement provisoire, clôture, publication et export CSV.',
            ],
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
