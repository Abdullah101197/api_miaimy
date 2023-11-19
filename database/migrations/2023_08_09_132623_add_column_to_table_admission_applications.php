    <?php

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
            Schema::table('admission_applications', function (Blueprint $table) {
                $table->string('required_doc', 1000)->nullable();
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            // if (!Schema::hasColumn('admission_applications', 'applicant_name')) {
              

            //         Schema::table('admission_applications', function (Blueprint $table) {
            //             $table->dropColumn('applicant_name');
            //         });
               
            // }
          
        }
    };
