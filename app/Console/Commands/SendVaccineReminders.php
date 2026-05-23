<?php

namespace App\Console\Commands;

use App\Models\PetRecord;
use App\Notifications\VaccineReminder;
use Illuminate\Console\Command;

class SendVaccineReminders extends Command
{
    protected $signature = 'app:send-vaccine-reminders {--days=7 : 提前天数}';

    protected $description = '发送疫苗/复诊到期提醒';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $targetDate = now()->addDays($days)->format('Y-m-d');

        $records = PetRecord::with(['pet', 'pet.user'])
            ->whereDate('next_visit_date', $targetDate)
            ->get();

        $count = 0;
        foreach ($records as $record) {
            $user = $record->pet->user;
            $pet = $record->pet;

            if (! empty($user->mobile)) {
                $user->notify(new VaccineReminder($pet->name, $record->title, $record->next_visit_date->format('Y-m-d')));
                $this->info("已发送短信提醒给 {$user->name} ({$user->mobile}): 宠物 {$pet->name} 的 {$record->title} 将于 {$days} 天后到期");
            } else {
                $this->warn("用户 {$user->name} 未绑定手机号，跳过提醒");
            }

            $count++;
        }

        $this->info("共处理 {$count} 条提醒");

        return self::SUCCESS;
    }
}
