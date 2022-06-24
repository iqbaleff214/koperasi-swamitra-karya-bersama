<?php

namespace App\Traits;

use App\Models\Deposit;
use App\Models\Loan;

trait LoanTrait {
    public function paidLoan($id)
    {
        $total_paid = Deposit::where('loan_id', $id)->where('type', 'wajib')->sum('amount');
        $updated = Loan::find($id)->update(['paid' => $total_paid]);
        return $updated;
    }
}
