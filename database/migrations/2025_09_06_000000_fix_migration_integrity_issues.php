<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Sessions: Add proper foreign key constraint on user_id
        if (Schema::hasTable('sessions') && ! $this->hasForeignKey('sessions', 'user_id')) {
            Schema::table('sessions', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // 2. Rooms: Enforce unsigned values for price and guests
        if (Schema::hasTable('rooms')) {
            Schema::table('rooms', function (Blueprint $table) {
                $table->decimal('price', 10, 2)->unsigned()->change();
                $table->unsignedInteger('guests')->change();
            });
        }

        // 3. Feedbacks: Enforce unsigned rating and add index on is_approved
        if (Schema::hasTable('feedbacks')) {
            Schema::table('feedbacks', function (Blueprint $table) {
                $table->unsignedTinyInteger('rating')->change();
            });

            if (! Schema::hasIndex('feedbacks', ['is_approved'])) {
                Schema::table('feedbacks', function (Blueprint $table) {
                    $table->index('is_approved');
                });
            }
        }

        // 4. WhatsApp Leads: remove duplicate phone numbers, then add unique constraint on phone
        if (Schema::hasTable('whatsapp_leads')) {
            $this->deduplicateWhatsappLeadPhones();

            if (! Schema::hasIndex('whatsapp_leads', ['phone'], 'unique')) {
                Schema::table('whatsapp_leads', function (Blueprint $table) {
                    $table->unique('phone');
                });
            }
        }

        // 5. Contacts: Add index on is_read for admin inbox filtering
        if (Schema::hasTable('contacts') && ! Schema::hasIndex('contacts', ['is_read'])) {
            Schema::table('contacts', function (Blueprint $table) {
                $table->index('is_read');
            });
        }

        // 6. Attractions: Add index on category for grouping queries
        if (Schema::hasTable('attractions') && ! Schema::hasIndex('attractions', ['category'])) {
            Schema::table('attractions', function (Blueprint $table) {
                $table->index('category');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('sessions') && $this->hasForeignKey('sessions', 'user_id')) {
            Schema::table('sessions', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
        }

        if (Schema::hasTable('rooms')) {
            Schema::table('rooms', function (Blueprint $table) {
                $table->decimal('price', 8, 2)->change();
                $table->integer('guests')->change();
            });
        }

        if (Schema::hasTable('feedbacks')) {
            Schema::table('feedbacks', function (Blueprint $table) {
                $table->tinyInteger('rating')->change();
            });

            if (Schema::hasIndex('feedbacks', ['is_approved'])) {
                Schema::table('feedbacks', function (Blueprint $table) {
                    $table->dropIndex(['is_approved']);
                });
            }
        }

        if (Schema::hasTable('whatsapp_leads') && Schema::hasIndex('whatsapp_leads', ['phone'], 'unique')) {
            Schema::table('whatsapp_leads', function (Blueprint $table) {
                $table->dropUnique(['phone']);
            });
        }

        if (Schema::hasTable('contacts') && Schema::hasIndex('contacts', ['is_read'])) {
            Schema::table('contacts', function (Blueprint $table) {
                $table->dropIndex(['is_read']);
            });
        }

        if (Schema::hasTable('attractions') && Schema::hasIndex('attractions', ['category'])) {
            Schema::table('attractions', function (Blueprint $table) {
                $table->dropIndex(['category']);
            });
        }
    }

    private function hasForeignKey(string $table, string $column): bool
    {
        foreach (Schema::getForeignKeys($table) as $foreignKey) {
            if (in_array($column, $foreignKey['columns'] ?? [], true)) {
                return true;
            }
        }

        return false;
    }

    private function deduplicateWhatsappLeadPhones(): void
    {
        $duplicatePhones = DB::table('whatsapp_leads')
            ->select('phone')
            ->whereNotNull('phone')
            ->groupBy('phone')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('phone');

        foreach ($duplicatePhones as $phone) {
            $keepId = DB::table('whatsapp_leads')
                ->where('phone', $phone)
                ->min('id');

            DB::table('whatsapp_leads')
                ->where('phone', $phone)
                ->where('id', '!=', $keepId)
                ->delete();
        }
    }
};
