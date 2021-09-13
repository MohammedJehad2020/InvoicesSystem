<?php

namespace App\Http\Controllers;

use App\Models\InvoiceAttachments;
use App\Models\Invoices;
use App\Models\Invoices_Details;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use File;

class InvoicesDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Invoices_Details  $invoices_Details
     * @return \Illuminate\Http\Response
     */
    public function show(Invoices_Details $invoices_Details)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Invoices_Details  $invoices_Details
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $invoices = Invoices::where('id',$id)->first();
        $details  = Invoices_Details::where('id_Invoice',$id)->get();
        $attachments  = InvoiceAttachments::where('invoice_id',$id)->get();

        return view('invoices.details_invoices',[
            'invoices'=> $invoices,
            'details' => $details,
            'attachments' => $attachments,
            ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoices_Details  $invoices_Details
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoices_Details $invoices_Details)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Invoices_Details  $invoices_Details
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $invoices = InvoiceAttachments::findOrFail($request->id_file);
        $invoices->delete();
        Storage::disk('public_uploads')->delete($request->invoice_number.'/'.$request->file_name);
        session()->flash('delete', 'تم حذف المرفق بنجاح');
        return back();
    }

   // download attachment file   
    public function get_file($invoice_number,$file_name)

    {
        $contents= Storage::disk('public_uploads')->getDriver()->getAdapter()->applyPathPrefix($invoice_number.'/'.$file_name);
        return response()->download( $contents);
    }


    // show attachment file
    public function open_file($invoice_number,$file_name)

    {
        $files = Storage::disk('public_uploads')->getDriver()->getAdapter()->applyPathPrefix($invoice_number.'/'.$file_name);
        return response()->file($files);
    }
}
