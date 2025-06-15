<?php

namespace Database\Factories;

use App\Helpers\Helpers;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Create or fetch a subscription (and its plan & member)
        $subscription = Subscription::factory();

        // Dates
        $date = $this->faker->dateTimeBetween('-1 year', 'now');
        $dueDate = (clone $date)->modify('+30 days');

        // Fee and tax
        $fee = $this->faker->randomFloat(2, 50, 1000);
        $taxRate = Helpers::getTaxRate() ?: 0;
        $tax = round(($fee * $taxRate) / 100, 2);

        // Discount
        $discountOptions = array_keys(Helpers::getDiscounts());
        $discountPct = $this->faker->randomElement($discountOptions);
        $discountAmount = round(Helpers::getDiscountAmount($discountPct, $fee), 2);

        // Totals
        $itemsTotal = round(max($fee - $discountAmount, 0), 2);
        $totalAmount = round($itemsTotal + $tax, 2);

        // Payments
        $paidAmount = $this->faker->randomFloat(2, 0, $totalAmount);
        $dueAmount = round($totalAmount - $paidAmount, 2);

        // Status logic
        if ($paidAmount >= $totalAmount) {
            $status = 'paid';
        } elseif ($paidAmount > 0) {
            $status = 'partial';
        } else {
            $status = 'issued';
        }

        return [
            'number'           => $this->faker->unique()->numerify('INV-#####'),
            'subscription_id'  => $subscription,
            'date'             => $date,
            'due_date'         => $dueDate,
            'payment_method'   => $this->faker->randomElement(['cash', 'cheque']),
            'status'           => $status,
            'subscription_fee' => $itemsTotal,
            'tax'              => $tax,
            'discount'         => $discountPct,
            'discount_amount'  => $discountAmount,
            'discount_note'    => $this->faker->sentence(3),
            'paid_amount'      => $paidAmount,
            'total_amount'     => $totalAmount,
            'due_amount'       => $dueAmount,
        ];
    }
}
