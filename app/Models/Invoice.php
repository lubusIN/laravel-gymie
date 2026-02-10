<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\Status;
use App\Helpers\Helpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'number',
        'subscription_id',
        'date',
        'due_date',
        'payment_method',
        'status',
        'tax',
        'discount',
        'discount_amount',
        'discount_note',
        'paid_amount',
        'total_amount',
        'due_amount',
        'subscription_fee',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'date'        => 'date',
        'due_date'    => 'date',
        'status'      => Status::class
    ];

    /**
     * The member who owns this invoice.
     *
     * @return BelongsTo
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * The subscription this invoice is for.
     *
     * @return BelongsTo
     */
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Boot the model and handle invoice calculations on saving.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($invoice) {
            if (!$invoice->number) {
                $invoice->number = Helpers::generateLastNumber('invoice', Invoice::class, $invoice->date);
            }
            Helpers::updateLastNumber('invoice', $invoice->number, $invoice->date);

            $fee = $invoice->subscription_fee ?? 0;
            $taxRate = Helpers::getTaxRate() ?: 0;
            $discountAmount = $invoice->discount_amount ?? 0;
            $itemsTotal = max($fee - $discountAmount, 0);
            $itemsTotal = round($itemsTotal, 2);
            $totalTax = round(($fee * $taxRate) / 100, 2);
            $paid = $invoice->paid_amount ?? 0;
            $totalAmount = $itemsTotal + $totalTax;

            // Handle paid amount based on status
            if ($invoice->status === 'paid') {
                $paid = $totalAmount;
            } elseif (in_array($invoice->status, ['cancelled', 'refund'])) {
                $paid = 0;
            } else {
                $paid = $invoice->paid_amount ?? 0;
                // Ensure paid amount doesn't exceed total amount
                if ($paid > $totalAmount) {
                    $paid = $totalAmount;
                }
            }

            $dueAmount = $totalAmount - $paid;

            // Update status if not explicitly set based on payment conditions
            if (!in_array($invoice->status, ['paid', 'cancelled', 'refund'])) {
                if ($dueAmount <= 0) {
                    $status = 'paid';
                } elseif ($paid > 0) {
                    $status = 'partial';
                } else {
                    $status = $invoice->status ?? 'issued';
                }
            } else {
                $status = $invoice->status;
            }

            $invoice->subscription_fee = $itemsTotal;
            $invoice->total_amount = $totalAmount;
            $invoice->paid_amount = $paid;
            $invoice->tax = $totalTax;
            $invoice->due_amount = $dueAmount;
            $invoice->status = $status;
        });
    }
}
