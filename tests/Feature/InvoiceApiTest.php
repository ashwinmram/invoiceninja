<?php

namespace Tests\Feature;

use Tests\P7TestCase;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class InvoiceApiTest extends P7TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function an_invoice_can_be_created_via_the_api()
    {
        $this->assertEquals(0, Invoice::get()->count());
        $this->assertEquals(0, InvoiceItem::get()->count());

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'X-Ninja-Token' => config('services.ninja.token')
        ])->json('POST', '/api/v1/invoices', [
            'client_id' => 1,
            'invoice_items' => [
                [
                    'product_key' => 'ROOM',
                    'notes' => 'Test',
                    'cost' => 100,
                    'qty' => 1
                ],
                [
                    'product_key' => 'SVC',
                    'cost' => 10,
                    'qty' => 1
                ],
                [
                    'product_key' => 'GST',
                    'cost' => 7.7,
                    'qty' => 1
                ]
            ]
        ]);

        $response->assertStatus(200);

        $this->assertEquals(1, Invoice::get()->count());
        $this->assertEquals(3, InvoiceItem::get()->count());
    }

    /** @test */
    public function new_invoice_items_can_be_added_to_an_existing_invoice()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'X-Ninja-Token' => config('services.ninja.token')
        ])->json('POST', '/api/v1/invoices', [
            'client_id' => 1,
            'invoice_items' => [
                [
                    'product_key' => 'ROOM',
                    'notes' => 'Test',
                    'cost' => 100,
                    'qty' => 1
                ],
                [
                    'product_key' => 'SVC',
                    'cost' => 10,
                    'qty' => 1
                ],
                [
                    'product_key' => 'GST',
                    'cost' => 7.7,
                    'qty' => 1
                ]
            ]
        ]);

        $response->assertStatus(200);

        $this->assertEquals(1, Invoice::get()->count());
        $this->assertEquals(3, InvoiceItem::get()->count());

        $invoice = Invoice::first();

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'X-Ninja-Token' => config('services.ninja.token')
        ])->json('PATCH', "/api/v1/p7/invoices/{$invoice->id}", [
            'invoice_items' => [
                [
                    'product_key' => 'ROOM2',
                    'notes' => 'Test',
                    'cost' => 100,
                    'qty' => 1
                ],
                [
                    'product_key' => 'SVC2',
                    'cost' => 10,
                    'qty' => 1
                ],
                [
                    'product_key' => 'GST',
                    'cost' => 7.7,
                    'qty' => 1
                ]
            ]
        ]);

        $response->assertStatus(200);

        $this->assertEquals(1, Invoice::get()->count());
        $this->assertEquals(6, InvoiceItem::get()->count());

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'X-Ninja-Token' => config('services.ninja.token')
        ])->json('POST', '/api/v1/invoices', [
            'client_id' => 2,
            'invoice_items' => [
                [
                    'product_key' => 'ROOM3',
                    'notes' => 'Test',
                    'cost' => 100,
                    'qty' => 1
                ],
                [
                    'product_key' => 'SVC3',
                    'cost' => 10,
                    'qty' => 1
                ],
                [
                    'product_key' => 'GST3',
                    'cost' => 7.7,
                    'qty' => 1
                ]
            ]
        ]);

        $this->assertEquals(2, Invoice::get()->count());
        $this->assertEquals(9, InvoiceItem::get()->count());
    }
}
