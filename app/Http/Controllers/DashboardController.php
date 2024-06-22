<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function index()
    {
        $array_months = [];
        $current_month = 0;
        $prev_month = 0;
        $prev_2month = 0;


        $users = User::WhereMonth('created_at', now()->subMonth(2))
            ->whereYear('created_at', now()->year)
            ->orWhereMonth('created_at', now()->subMonths(1))
            ->whereYear('created_at', now()->year)
            ->orWhereMonth('created_at', now()->subMonth(0))
            ->whereYear('created_at', now()->year)
            ->orderBy('created_at', 'desc')
            ->get();

        $users->map(function ($value) {
            $value->month = $value->created_at->format('M');
            return $value;
        });

        foreach ($users as $user) {
            if ($user->month == now()->format('M')) {
                $current_month += 1;
                // return '1st';
            }

            if ($user->month == now()->subMonth(1)->format('M')) {
                $prev_month += 1;
            }

            if ($user->month == now()->subMonth(2)->format('M')) {
                $prev_2month += 1;
            }
        }

        foreach ($users->pluck('month')->unique() as $key => $month) {
            array_push($array_months, array($key => $month));
        }

        return response()->json(
            [
                'months' => $array_months,
                'users_count' => array($current_month, $prev_month, $prev_2month),
            ]
            ,
            200
        );
    }

}
