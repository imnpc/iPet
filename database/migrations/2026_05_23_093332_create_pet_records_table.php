<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pet_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();

            $table->enum('type', [
                'vaccine',
                'checkup',
                'illness',
                'medication',
                'surgery',
                'grooming',
                'other',
            ])->comment('记录类型');

            $table->string('title', 200)->comment('记录标题');
            $table->date('visit_date')->comment('就诊日期');
            $table->date('next_visit_date')->nullable()->comment('下次复诊/疫苗日期');
            $table->string('hospital_name', 200)->nullable()->comment('医院名称');
            $table->string('vet_name', 100)->nullable()->comment('医生姓名');
            $table->string('hospital_phone', 20)->nullable()->comment('医院电话');
            $table->decimal('weight', 6, 2)->nullable()->comment('体重(kg)');
            $table->decimal('temperature', 4, 1)->nullable()->comment('体温(℃)');
            $table->text('symptoms')->nullable()->comment('症状描述');
            $table->text('diagnosis')->nullable()->comment('诊断结果');
            $table->text('treatment')->nullable()->comment('治疗方案');
            $table->text('prescription')->nullable()->comment('处方/用药说明');
            $table->text('notes')->nullable()->comment('备注');
            $table->decimal('cost', 10, 2)->nullable()->comment('费用');

            $table->softDeletes();
            $table->timestamps();

            $table->index(['pet_id', 'type']);
            $table->index(['pet_id', 'visit_date']);
            $table->index('next_visit_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pet_records');
    }
};
