<?php

namespace App\Http\Controllers;

use App\Exports\InvoicesExport;
use App\Models\InvoiceAttachments;
use App\Models\Invoices;
use App\Models\Invoices_Details;
use App\Models\Product;
use App\Models\Section;
use App\Models\User;
use App\Notifications\AddInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Notifications\AddInvoiceNotification;

class InvoicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoices = Invoices::all();
        return view('invoices.invoices', [
            'invoices' => $invoices,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $sections = Section::all();

        return view('invoices.add_invoice', [
            'sections' => $sections,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Invoices::create([
            'invoice_number' => $request->invoice_number,
            'invoice_Date' => $request->invoice_Date,
            'Due_date' => $request->Due_date,
            'product' => $request->product,
            'section_id' => $request->Section,
            'Amount_collection' => $request->Amount_collection,
            'Amount_Commission' => $request->Amount_Commission,
            'Discount' => $request->Discount,
            'Value_VAT' => $request->Value_VAT,
            'Rate_VAT' => $request->Rate_VAT,
            'Total' => $request->Total,
            'Status' => 'غير مدفوعة',
            'Value_Status' => 2,
            'note' => $request->note,
        ]);

        $invoice_id = Invoices::latest()->first()->id;
        Invoices_Details::create([
            'id_Invoice' => $invoice_id,
            'invoice_number' => $request->invoice_number,
            'product' => $request->product,
            'Section' => $request->Section,
            'Status' => 'غير مدفوعة',
            'Value_Status' => 2,
            'note' => $request->note,
            'user' => (Auth::user()->name),
        ]);

        if ($request->hasFile('pic')) {

            $invoice_id = Invoices::latest()->first()->id;
            $image = $request->file('pic');
            $file_name = $image->getClientOriginalName();
            $invoice_number = $request->invoice_number;

            InvoiceAttachments::create([
               'file_name' => $file_name,
               'invoice_number' => $invoice_number,
               'Created_by' => Auth::user()->name,
               'invoice_id' => $invoice_id,
            ]);

            // move pic
            $imageName = $request->pic->getClientOriginalName();
            $request->pic->move(public_path('Attachments/' . $invoice_number), $imageName);
        }


        //  use email notification
        //    $user = User::first();
        //    Notification::send($user, new AddInvoice($invoice_id));

        // use to database notifiation
        $user = User::get();
        $invoices = Invoices::latest()->first();
        Notification::send($user, new AddInvoiceNotification($invoices));

     




        
        // event(new MyEventClass('hello world'));

        session()->flash('Add', 'تم اضافة الفاتورة بنجاح');
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $invoices = Invoices::where('id', $id)->first();
        return view('invoices.status_update', [
           'invoices' => $invoices,
        ]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function edit(Invoices $invoices, $id)
    {
        $invoices = Invoices::where('id', $id)->first();
        $sections = Section::all();
        return view('invoices.edit_invoices',[
            'sections' => $sections,
            'invoices' => $invoices,
         ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoices $invoices)
    {
        $invoices = invoices::findOrFail($request->invoice_id);
        $invoices->update([
            'invoice_number' => $request->invoice_number,
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'product' => $request->product,
            'section_id' => $request->Section,
            'amount_collection' => $request->amount_collection,
            'amount_commission' => $request->amount_commission,
            'discount' => $request->discount,
            'value_vat' => $request->value_vat,
            'rate_vat' => $request->rate_vat,
            'total' => $request->total,
            'note' => $request->note,
        ]);

        session()->flash('edit', 'تم تعديل الفاتورة بنجاح');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $id = $request->invoice_id;
        $invoices = Invoices::where('id', $id)->first();
        $Details = InvoiceAttachments::where('invoice_id', $id)->first();


      // اذا كان الامر حذف فانه يحذف حذف كامل اما 
    //   اذا كان يحتوي على اي دي 2 فانه يحذفه لكن يبقى موجود في قاعدة البيانات و يذهب لقسم الارشفة  
         $id_page =$request->id_page;


        if (!$id_page==2) {

        if (!empty($Details->invoice_number)) {

            Storage::disk('public_uploads')->deleteDirectory($Details->invoice_number);
        }

        $invoices->forceDelete();
        session()->flash('delete_invoice');
        return redirect('/invoices');

        }

        else {

            $invoices->delete();
            session()->flash('archive_invoice');
            return redirect('/Archive');
        }
    }

    // get section id from add_invoices page
    public function getproducts($id){

        $products = Product::where('section_id', '=', $id)->pluck('Product_name','id');
        return json_encode($products);
        
    }


    
    public function Status_Update($id, Request $request)
    {
        $invoices = Invoices::findOrFail($id);

        if ($request->Status === 'مدفوعة') {

            $invoices->update([
                'Value_Status' => 1,
                'Status' => $request->Status,
                'Payment_Date' => $request->Payment_Date,
            ]);

            Invoices_Details::create([
                'id_Invoice' => $request->invoice_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'Section' => $request->Section,
                'Status' => $request->Status,
                'Value_Status' => 1,
                'note' => $request->note,
                'Payment_Date' => $request->Payment_Date,
                'user' => (Auth::user()->name),
            ]);
        }

        else {
            $invoices->update([
                'Value_Status' => 3,
                'Status' => $request->Status,
                'Payment_Date' => $request->Payment_Date,
            ]);
            invoices_Details::create([
                'id_Invoice' => $request->invoice_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'Section' => $request->Section,
                'Status' => $request->Status,
                'Value_Status' => 3,
                'note' => $request->note,
                'Payment_Date' => $request->Payment_Date,
                'user' => (Auth::user()->name),
            ]);
        }

        session()->flash('Status_Update');
        return redirect('/invoices');

    }


    // الفواتير المدفوعة
    public function Invoice_Paid()
    {
        $invoices = Invoices::where('value_status', 1)->get();
        return view('invoices.invoices_paid',[
            'invoices' => $invoices 
            ]);
    }

    // الفواتير الغير مدفوعة
    public function Invoice_unPaid()
    {
        $invoices = Invoices::where('value_status',2)->get();
        return view('invoices.invoices_unpaid',[
            'invoices' => $invoices 
            ]);
    }

    // الفواتير المدفوعة جزئيا
    public function Invoice_Partial()
    {
        $invoices = Invoices::where('value_status',3)->get();
        return view('invoices.invoices_Partial',[
            'invoices' => $invoices 
            ]);
    }

    // طباعة الفاتورة
    public function Print_invoice($id)
    {
        $invoices = Invoices::where('id', $id)->first();
        return view('invoices.Print_invoice',[
            'invoices' => $invoices,
            ]);
    }

    // تصدير الفاتورة
    public function export() 
    {
        return Excel::download(new InvoicesExport, 'invoices.xlsx');
    }

//  جعل كل الرسائل مقروءة
    public function MarkAsRead_all (Request $request)
    {

        $userUnreadNotification= auth()->user()->unreadNotifications;

        if($userUnreadNotification) {
            $userUnreadNotification->markAsRead();
            return back();
        }


    }



}
