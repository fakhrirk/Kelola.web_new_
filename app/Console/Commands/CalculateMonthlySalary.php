<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Salary;
use Carbon\Carbon;

class CalculateMonthlySalary extends Command
{
    protected $signature = 'salary:calculate {--month=} {--year=}';
    protected $description = 'Calculate monthly salary for all employees based on attendance';

    public function handle()
    {
        $month = $this->option('month') ?? Carbon::now()->subMonth()->month;
        $year = $this->option('year') ?? Carbon::now()->subMonth()->year;
        $period = Carbon::createFromDate($year, $month, 1)->startOfMonth();

        $this->info("Calculating salaries for: " . $period->format('F Y'));

        // Ambil semua karyawan (bukan owner/admin)
        $employees = User::where('role', 'karyawan')->get();
        $totalDaysInMonth = $period->daysInMonth;

        foreach ($employees as $employee) {
            // Cek apakah gaji untuk periode ini sudah dihitung
            $existingSalary = Salary::where('user_id', $employee->id)
                                ->where('period', $period->toDateString())->exists();

            if ($existingSalary) {
                $this->warn("Salary for {$employee->name} for this period already calculated. Skipping.");
                continue;
            }

            $attendances = Attendance::where('user_id', $employee->id)
                ->whereMonth('attendance_date', $month)
                ->whereYear('attendance_date', $year)
                ->whereNotNull('clock_in')
                ->whereNotNull('clock_out')
                ->count();

            // Asumsi: Potongan dihitung per hari tidak masuk
            $daysNotAttended = $totalDaysInMonth - $attendances;
            $dailySalary = $employee->base_salary / $totalDaysInMonth;
            $deductions = $daysNotAttended * $dailySalary;

            // Contoh bonus (bisa dikembangkan)
            $bonuses = 0; // Logika untuk bonus/tip/lembur bisa ditambahkan di sini

            $finalSalary = $employee->base_salary - $deductions + $bonuses;

            Salary::create([
                'user_id' => $employee->id,
                'period' => $period->toDateString(),
                'base_salary' => $employee->base_salary,
                'deductions' => $deductions,
                'bonuses' => $bonuses,
                'final_salary' => $finalSalary,
                'status' => 'unpaid',
            ]);

            $this->info("Salary calculated for: {$employee->name}");
        }

        $this->info('Salary calculation finished.');
        return 0;
    }
}
