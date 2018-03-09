<?php

namespace App\Console\Commands;

use App\Expense;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RepeatExpense extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'repeat:expense';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks repeat interval of expenses and creates new record';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $expenses = Expense::where('due_date', '=', Carbon::today()->format('Y-m-d'))->get();

        foreach ($expenses as $expense) {
            if ($expense->repeat == 1) {
                $expenseData = ['name' => $expense->name,
                                     'category_id' => $expense->category_id,
                                     'due_date' => $expense->due_date->addDays(1),
                                     'repeat' => $expense->repeat,
                                     'note' => $expense->note,
                                     'amount' => $expense->amount,
                                     'paid' => 0,
                                     'created_by' => 1,
                                     'updated_by' => 1, ];

                $newExpense = new Expense($expenseData);
                $newExpense->save();
            } elseif ($expense->repeat == 2) {
                $expenseData = ['name' => $expense->name,
                                     'category_id' => $expense->category_id,
                                     'due_date' => $expense->due_date->addWeek(),
                                     'repeat' => $expense->repeat,
                                     'note' => $expense->note,
                                     'amount' => $expense->amount,
                                     'paid' => 0,
                                     'created_by' => 1,
                                     'updated_by' => 1, ];

                $newExpense = new Expense($expenseData);
                $newExpense->save();
            } elseif ($expense->repeat == 3) {
                $expenseData = ['name' => $expense->name,
                                     'category_id' => $expense->category_id,
                                     'due_date' => $expense->due_date->addMonth(),
                                     'repeat' => $expense->repeat,
                                     'note' => $expense->note,
                                     'amount' => $expense->amount,
                                     'paid' => 0,
                                     'created_by' => 1,
                                     'updated_by' => 1, ];

                $newExpense = new Expense($expenseData);
                $newExpense->save();
            } elseif ($expense->repeat == 4) {
                $expenseData = ['name' => $expense->name,
                                     'category_id' => $expense->category_id,
                                     'due_date' => $expense->due_date->addYear(),
                                     'repeat' => $expense->repeat,
                                     'note' => $expense->note,
                                     'amount' => $expense->amount,
                                     'paid' => 0,
                                     'created_by' => 1,
                                     'updated_by' => 1, ];

                $newExpense = new Expense($expenseData);
                $newExpense->save();
            }
        }
    }
}
