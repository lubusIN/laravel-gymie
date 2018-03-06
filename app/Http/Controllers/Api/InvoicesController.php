<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\invoice;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class InvoicesController extends Controller
{
	public function index()
	{
		$invoices = invoice::all();

		return response()->json($invoices);
	}

	public function show($id)
	{
		$invoice = invoice::findOrFail($id);

		return response()->json($invoice);
	}

	public function unpaid()
    {
      $invoices = Invoice::where('status',0)->get();

      return response()->json($invoices);
    }

    public function paid()
    {
      $invoices = Invoice::where('status',1)->get();

      return response()->json($invoices);
    }

    public function partial()
    {
      $invoices = Invoice::where('status',2)->get();

      return response()->json($invoices);
    }

    public function overpaid()
    {
      $invoices = Invoice::where('status',3)->get();

      return response()->json($invoices);
    }
}