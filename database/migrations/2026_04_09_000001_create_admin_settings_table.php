<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('group')->default('general')->index();
            $table->string('label');
            $table->longText('value')->nullable();
            $table->string('type', 20)->default('text'); // text|boolean|color|json|number|email|url|textarea
            $table->timestamps();
        });

        // Seed defaults
        $now = now();
        DB::table('admin_settings')->insert([
            // General
            ['key'=>'site_name',        'group'=>'general',      'label'=>'Site Name',             'value'=>'IndoorB',                        'type'=>'text',     'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'site_tagline',     'group'=>'general',      'label'=>'Tagline',               'value'=>'Book Indoor Sports Venues Easily','type'=>'text',     'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'site_description', 'group'=>'general',      'label'=>'Site Description',      'value'=>'The best indoor sports booking platform.','type'=>'textarea','created_at'=>$now,'updated_at'=>$now],
            ['key'=>'contact_email',    'group'=>'general',      'label'=>'Contact Email',         'value'=>'admin@indoorb.com',               'type'=>'email',    'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'contact_phone',    'group'=>'general',      'label'=>'Contact Phone',         'value'=>'+92 300 0000000',                 'type'=>'text',     'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'address',          'group'=>'general',      'label'=>'Address',               'value'=>'Lahore, Pakistan',                'type'=>'text',     'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'timezone',         'group'=>'general',      'label'=>'Timezone',              'value'=>'Asia/Karachi',                    'type'=>'text',     'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'currency',         'group'=>'general',      'label'=>'Currency Symbol',       'value'=>'Rs.',                             'type'=>'text',     'created_at'=>$now,'updated_at'=>$now],
            // Appearance
            ['key'=>'logo_url',         'group'=>'appearance',   'label'=>'Logo URL',              'value'=>'/images/logo.png',                'type'=>'url',      'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'favicon_url',      'group'=>'appearance',   'label'=>'Favicon URL',           'value'=>'/images/logo.png',                'type'=>'url',      'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'primary_color',    'group'=>'appearance',   'label'=>'Primary Color',         'value'=>'#19722d',                         'type'=>'color',    'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'footer_text',      'group'=>'appearance',   'label'=>'Footer Text',           'value'=>'© 2026 IndoorB. All rights reserved.','type'=>'text', 'created_at'=>$now,'updated_at'=>$now],
            // Booking
            ['key'=>'default_slot_duration','group'=>'booking',  'label'=>'Default Slot (minutes)','value'=>'60',                             'type'=>'number',   'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'max_advance_days',  'group'=>'booking',     'label'=>'Max Advance Booking (days)','value'=>'30',                         'type'=>'number',   'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'cancellation_hours','group'=>'booking',     'label'=>'Cancel Cutoff (hours)', 'value'=>'24',                             'type'=>'number',   'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'tax_rate',          'group'=>'booking',     'label'=>'Tax Rate (%)',           'value'=>'0',                              'type'=>'number',   'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'allow_guest_booking','group'=>'booking',    'label'=>'Allow Guest Booking',   'value'=>'0',                              'type'=>'boolean',  'created_at'=>$now,'updated_at'=>$now],
            // Notifications
            ['key'=>'notify_new_booking','group'=>'notifications','label'=>'Email on New Booking', 'value'=>'1',                              'type'=>'boolean',  'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'notify_cancellation','group'=>'notifications','label'=>'Email on Cancellation','value'=>'1',                             'type'=>'boolean',  'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'notify_admin_email','group'=>'notifications','label'=>'Admin Notification Email','value'=>'admin@indoorb.com',           'type'=>'email',    'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'whatsapp_notify',   'group'=>'notifications','label'=>'WhatsApp Notifications','value'=>'0',                            'type'=>'boolean',  'created_at'=>$now,'updated_at'=>$now],
            // Social
            ['key'=>'social_facebook',   'group'=>'social',      'label'=>'Facebook URL',          'value'=>'',                               'type'=>'url',      'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'social_instagram',  'group'=>'social',      'label'=>'Instagram URL',         'value'=>'',                               'type'=>'url',      'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'social_twitter',    'group'=>'social',      'label'=>'Twitter / X URL',       'value'=>'',                               'type'=>'url',      'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'social_youtube',    'group'=>'social',      'label'=>'YouTube URL',           'value'=>'',                               'type'=>'url',      'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'social_whatsapp',   'group'=>'social',      'label'=>'WhatsApp Number',       'value'=>'',                               'type'=>'text',     'created_at'=>$now,'updated_at'=>$now],
            // Security
            ['key'=>'maintenance_mode',  'group'=>'security',    'label'=>'Maintenance Mode',      'value'=>'0',                              'type'=>'boolean',  'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'registration_open', 'group'=>'security',    'label'=>'New Registrations Open','value'=>'1',                             'type'=>'boolean',  'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'session_lifetime',  'group'=>'security',    'label'=>'Session Lifetime (min)','value'=>'120',                            'type'=>'number',   'created_at'=>$now,'updated_at'=>$now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_settings');
    }
};
