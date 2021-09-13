<?php

namespace App\Exports;

use App\Models\Invoices;
use Maatwebsite\Excel\Concerns\FromCollection;

class InvoicesExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //  return all data
        return Invoices::all();

        //  return some data
        // return Invoices::select('invoice_number', 'invoice_date')->get();
    }
}
