<?php

namespace App\Http\Controllers;

use App\Models\BillRoom;
use Barryvdh\DomPDF\Facade\Pdf;

class BillPdfController extends Controller
{
    public function __invoke(BillRoom $bill): \Illuminate\Http\Response
    {
        return Pdf::loadView('components.bill', ['record' => $bill])
            ->download($bill->room()->first()->name.'-'.$bill->at->format('m/Y').'.pdf');
    }
}
