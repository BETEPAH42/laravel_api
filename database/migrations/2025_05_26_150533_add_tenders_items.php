<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        $tenders = [];
        if (($open = fopen(database_path() . "/migrations/test_task_data.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($open)) !== FALSE) {
                if(in_array('Внешний код',$data)) {
                    continue;
                }
                \App\Models\Tender::create([
                    'xml_id' => $data[0],
                    'number' => $data[1],
                    'status' => trim($data[2]) == 'Открыто',
                    'name' => $data[3],
                    'update' => Carbon::parse($data[4])->format('Y-m-d H:i:s')
                ]);
            }
            fclose($open);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
