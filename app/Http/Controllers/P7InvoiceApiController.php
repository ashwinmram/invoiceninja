<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;

class P7InvoiceApiController extends Controller
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $invoice = Invoice::where('public_id', $id)->first();
        $newItems = collect($request->invoice_items);

        $newItems->each(function ($item) use ($invoice) {
            $publicId = InvoiceItem::count() + 1;

            $invoice->invoice_items()->create([
                'user_id' => auth()->id(),
                'account_id' => $invoice->account_id,
                'public_id' => $publicId,
                'product_key' => $item['product_key'],
                'notes' => isset($item['notes']) ? $item['notes'] : "",
                'cost' => $item['cost'],
                'qty' => $item['qty'],
                'tax_rate1' => 0.000
            ]);

            $invoice->amount += $item['cost'] * $item['qty'];
            $invoice->balance += $item['cost'] * $item['qty'];
            $invoice->save();
        });

        return $invoice;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
