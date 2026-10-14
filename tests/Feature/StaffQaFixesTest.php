<?php

namespace Tests\Feature;

use App\Livewire\Admin\Customers;
use App\Livewire\Staff\BookingsManagement;
use App\Livewire\Staff\CustomBooking;
use App\Livewire\Staff\Finance;
use App\Livewire\Staff\OnlinePayment;
use App\Livewire\Staff\PermanentBookings;
use App\Livewire\Staff\PoolBookingsManagement;
use App\Livewire\Staff\StaffDashboard;
use App\Livewire\Staff\StaffReport;
use App\Livewire\Staff\TournamentManagement;
use App\Models\BookingBooking;
use App\Models\LeagueLeague;
use App\Models\PoolsPoolbooking;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * QA regression suite for the staff panel fixes.
 *
 * Must run against a PostgreSQL database that mirrors the production Django
 * schema (the Laravel migrations alone are less strict than production).
 */
class StaffQaFixesTest extends TestCase
{
    use DatabaseTransactions;

    private User $staff;

    private int $venueId;

    private int $sportId;

    private int $djangoUserId;

    protected function setUp(): void
    {
        parent::setUp();

        if (DB::getDriverName() !== 'pgsql' || ! Schema::hasColumn('league_league', 'approval_status')) {
            $this->markTestSkipped('Requires a PostgreSQL database cloned from the production Django schema.');
        }

        $this->venueId = $this->createVenue();
        $this->sportId = $this->createSport($this->venueId);

        $this->staff = User::factory()->create([
            'role' => 'staff',
            'complex_id' => $this->venueId,
            'contact' => '0771234567',
        ]);

        $this->djangoUserId = $this->createDjangoUser($this->staff->email);

        $this->actingAs($this->staff);
    }

    // ── 1. Cash & card payment collection ──────────────────────────────────────

    public function test_finance_collects_cash_payment_into_offline_amount(): void
    {
        $booking = $this->createBooking(['price' => 2500, 'balance_due' => 2500]);

        Livewire::test(Finance::class)
            ->call('openPaymentModal', $booking->id, 'sport')
            ->assertSet('showPaymentModal', true)
            ->set('collectPaymentMethod', 'cash')
            ->call('collectPayment')
            ->assertSet('showPaymentModal', false)
            ->assertHasNoErrors();

        $booking->refresh();
        $this->assertSame('Paid', $booking->payment_status);
        $this->assertSame('cash', $booking->payment_method);
        $this->assertSame('FullyPaid', $booking->financial_status);
        $this->assertEquals(2500, (float) $booking->offline_paid_amount);
        $this->assertEquals(0, (float) $booking->online_paid_amount);
        $this->assertEquals(2500, (float) $booking->amount_paid);
        $this->assertEquals(0, (float) $booking->balance_due);
    }

    public function test_finance_switching_to_card_survives_rehydration_and_records_online_amount(): void
    {
        $booking = $this->createBooking(['price' => 1000, 'balance_due' => 1000]);

        // Each chained call is a separate Livewire request (dehydrate → hydrate).
        Livewire::test(Finance::class)
            ->call('openPaymentModal', $booking->id, 'sport')
            ->set('collectPaymentMethod', 'cash')
            ->set('collectPaymentMethod', 'card')
            ->assertSet('paymentBookingId', $booking->id)
            ->call('collectPayment')
            ->assertSet('showPaymentModal', false);

        $booking->refresh();
        $this->assertSame('card', $booking->payment_method);
        $this->assertEquals(1000, (float) $booking->online_paid_amount);
        $this->assertEquals(0, (float) $booking->offline_paid_amount);
        $this->assertEquals(0, (float) $booking->balance_due);
    }

    public function test_finance_uses_price_minus_advance_when_balance_due_is_zero(): void
    {
        $booking = $this->createBooking(['price' => 2500, 'advance_amount' => 500, 'balance_due' => 0]);

        Livewire::test(Finance::class)
            ->call('openPaymentModal', $booking->id, 'sport')
            ->set('collectPaymentMethod', 'transfer')
            ->call('collectPayment');

        $booking->refresh();
        $this->assertEquals(2000, (float) $booking->amount_paid);
        $this->assertEquals(2000, (float) $booking->online_paid_amount);
    }

    public function test_finance_collects_pool_booking_payment_without_touching_missing_columns(): void
    {
        $poolBooking = $this->createPoolBooking(['booking_total' => 1500, 'balance_due' => 1500]);

        Livewire::test(Finance::class)
            ->call('openPaymentModal', $poolBooking->id, 'pool')
            ->set('collectPaymentMethod', 'cash')
            ->call('collectPayment')
            ->assertSet('showPaymentModal', false);

        $poolBooking->refresh();
        $this->assertSame('FullyPaid', $poolBooking->financial_status);
        $this->assertEquals(1500, (float) $poolBooking->amount_paid);
        $this->assertEquals(0, (float) $poolBooking->balance_due);
    }

    public function test_finance_reset_filters_clears_every_filter(): void
    {
        Livewire::test(Finance::class)
            ->set('search', 'farhan')
            ->set('startDate', '2025-11-01')
            ->set('endDate', '2025-11-20')
            ->set('paymentStatus', 'Paid')
            ->set('paymentMethod', 'cash')
            ->call('resetFilters')
            ->assertSet('search', '')
            ->assertSet('startDate', '')
            ->assertSet('endDate', '')
            ->assertSet('paymentStatus', '')
            ->assertSet('paymentMethod', '');
    }

    public function test_online_payment_collects_card_payment(): void
    {
        $booking = $this->createBooking([
            'price' => 3000,
            'balance_due' => 3000,
            'payment_method' => 'genie',
        ]);

        Livewire::test(OnlinePayment::class)
            ->call('openPaymentModal', $booking->id, 'sport')
            ->assertSet('collectPaymentMethod', 'cash')
            ->set('collectPaymentMethod', 'card')
            ->call('collectPayment')
            ->assertSet('showPaymentModal', false);

        $booking->refresh();
        $this->assertSame('card', $booking->payment_method);
        $this->assertEquals(3000, (float) $booking->online_paid_amount);
        $this->assertSame('FullyPaid', $booking->financial_status);
    }

    // ── 2. Booking ID "#" removal & search ─────────────────────────────────────

    public function test_finance_table_shows_booking_id_without_hash_prefix(): void
    {
        $booking = $this->createBooking(['user_name' => 'Hash Check']);

        Livewire::test(Finance::class)
            ->assertSee('Hash Check')
            ->assertDontSeeHtml('>#'.$booking->id.'<')
            ->call('openPaymentModal', $booking->id, 'sport')
            ->assertDontSeeHtml('>#'.$booking->id.'<');
    }

    #[DataProvider('searchableComponents')]
    public function test_search_by_name_and_numeric_id_does_not_crash(string $component): void
    {
        $booking = $this->createBooking(['user_name' => 'Farhan Nazeer', 'payment_method' => 'genie']);

        Livewire::test($component)
            ->set('search', 'Farhan')
            ->assertOk()
            ->assertSee('Farhan Nazeer')
            ->set('search', (string) $booking->id)
            ->assertOk()
            ->assertSee('Farhan Nazeer');
    }

    public static function searchableComponents(): array
    {
        return [
            'finance' => [Finance::class],
            'online payment' => [OnlinePayment::class],
        ];
    }

    public function test_finance_search_is_case_insensitive(): void
    {
        $this->createBooking(['user_name' => 'Farhan Nazeer']);

        Livewire::test(Finance::class)
            ->set('search', 'farhan')
            ->assertSee('Farhan Nazeer');
    }

    public function test_permanent_bookings_table_has_no_id_column(): void
    {
        $permanentId = $this->createPermanentBooking();
        $this->createBooking(['user_name' => 'Permanent Player', 'permanent_source_id' => $permanentId]);

        Livewire::test(PermanentBookings::class)
            ->assertOk()
            ->assertSee('Permanent Player')
            ->assertDontSeeHtml('>ID</th>')
            ->assertDontSeeHtml('>#'.$permanentId.'<');
    }

    public function test_permanent_bookings_search_by_name_phone_and_id(): void
    {
        $permanentId = $this->createPermanentBooking();
        $this->createBooking([
            'user_name' => 'Permanent Player',
            'user_number' => '0779998887',
            'permanent_source_id' => $permanentId,
        ]);

        Livewire::test(PermanentBookings::class)
            ->set('search', 'permanent')
            ->assertOk()
            ->assertSee('Permanent Player')
            ->set('search', '0779998887')
            ->assertSee('Permanent Player')
            ->set('search', (string) $permanentId)
            ->assertOk()
            ->assertSee('Permanent Player')
            ->set('search', 'nobody-matches-this')
            ->assertDontSee('Permanent Player');
    }

    public function test_report_csv_export_has_no_booking_id_column(): void
    {
        $booking = $this->createBooking(['user_name' => 'Csv Player', 'booking_date' => now()->toDateString()]);

        $component = Livewire::test(StaffReport::class)->call('exportReport');

        $component->assertFileDownloaded();

        $csv = base64_decode($component->effects['download']['content']);
        $lines = array_values(array_filter(explode("\n", $csv)));

        $this->assertSame(
            'Player Name,Court,Sport,Date,Start Time,End Time,Status,Payment Status,Revenue',
            $lines[0]
        );
        $this->assertStringNotContainsString('Booking ID', $csv);
        $this->assertStringStartsWith('"Csv Player"', $lines[1]);
        $this->assertStringNotContainsString((string) $booking->id.',', $lines[1]);
    }

    // ── 3. Time select feed (custom booking timeline) ──────────────────────────

    public function test_custom_booking_pill_click_sets_start_then_extends_range(): void
    {
        Livewire::test(CustomBooking::class)
            ->call('selectTimeSlotFromPill', '10:00')
            ->assertSet('selectedTime', '10:00')
            ->assertSet('selectedEndTime', '11:00')
            ->call('selectTimeSlotFromPill', '13:00')
            ->assertSet('selectedTime', '10:00')
            ->assertSet('selectedEndTime', '14:00')
            ->call('selectTimeSlotFromPill', '11:00')
            ->assertSet('selectedTime', '11:00')
            ->assertSet('selectedEndTime', '14:00')
            ->call('selectTimeSlotFromPill', '08:00')
            ->assertSet('selectedTime', '08:00')
            ->assertSet('selectedEndTime', '14:00');
    }

    public function test_custom_booking_last_slot_extends_to_midnight(): void
    {
        Livewire::test(CustomBooking::class)
            ->call('selectTimeSlotFromPill', '21:00')
            ->call('selectTimeSlotFromPill', '23:00')
            ->assertSet('selectedTime', '21:00')
            ->assertSet('selectedEndTime', '00:00');
    }

    public function test_custom_booking_changing_start_keeps_valid_end_and_fixes_invalid_end(): void
    {
        Livewire::test(CustomBooking::class)
            ->set('selectedTime', '10:00')
            ->set('selectedEndTime', '13:00')
            ->set('selectedTime', '11:00')
            ->assertSet('selectedEndTime', '13:00')
            ->set('selectedTime', '15:00')
            ->assertSet('selectedEndTime', '16:00');
    }

    public function test_custom_booking_end_time_options_start_after_selected_start(): void
    {
        $component = Livewire::test(CustomBooking::class)->set('selectedTime', '22:00');

        $values = array_column($component->get('endTimeOptions'), 'value');
        $this->assertSame(['23:00', '00:00'], $values);
    }

    // ── 4. Mobile number validation ────────────────────────────────────────────

    #[DataProvider('invalidPhoneNumbers')]
    public function test_custom_booking_rejects_invalid_phone_numbers(string $phone): void
    {
        Livewire::test(CustomBooking::class)
            ->set('playerName', 'QA Player')
            ->set('phoneNumber', $phone)
            ->call('createCustomPackageBooking')
            ->assertHasErrors('phoneNumber');
    }

    #[DataProvider('validPhoneNumbers')]
    public function test_custom_booking_accepts_valid_phone_numbers(string $phone): void
    {
        Livewire::test(CustomBooking::class)
            ->set('playerName', 'QA Player')
            ->set('phoneNumber', $phone)
            ->call('createCustomPackageBooking')
            ->assertHasNoErrors('phoneNumber');
    }

    public static function invalidPhoneNumbers(): array
    {
        return [
            'sequential 10' => ['1234567890'],
            'sequential 9' => ['123456789'],
            'reverse sequential' => ['9876543210'],
            'zero sequential' => ['0123456789'],
            'repeated ones 12' => ['111111111111'],
            'repeated ones 10' => ['1111111111'],
            'repeated zeros' => ['0000000000'],
            'repeated sevens' => ['0777777777'],
            'too short' => ['111111'],
            'letters' => ['07712abcde'],
            'too long' => ['077123456789'],
        ];
    }

    public static function validPhoneNumbers(): array
    {
        return [
            'local mobile' => ['0771234567'],
            'intl plus' => ['+94771234567'],
            'intl no plus' => ['94771234567'],
            'landline' => ['0112345678'],
            'with spaces' => ['077 123 4567'],
            'with dashes' => ['077-123-4567'],
        ];
    }

    #[DataProvider('phoneValidatedForms')]
    public function test_every_booking_form_rejects_placeholder_phone(string $component, string $field, string $action): void
    {
        Livewire::test($component)
            ->set($field, '1234567890')
            ->call($action)
            ->assertHasErrors($field);

        Livewire::test($component)
            ->set($field, '0771234567')
            ->call($action)
            ->assertHasNoErrors($field);
    }

    public static function phoneValidatedForms(): array
    {
        return [
            'bookings management' => [BookingsManagement::class, 'phoneNumber', 'addBooking'],
            'staff dashboard quick booking' => [StaffDashboard::class, 'bookingFormUserNumber', 'saveQuickBooking'],
            'pool bookings' => [PoolBookingsManagement::class, 'userNumber', 'saveBooking'],
            'admin customers' => [Customers::class, 'phoneNumber', 'addUser'],
            'tournament contact' => [TournamentManagement::class, 'form_contact_phone', 'createTournament'],
        ];
    }

    // ── 5. Tournament creation (league_league NOT NULL constraints) ────────────

    public function test_tournament_is_created_against_production_schema(): void
    {
        Livewire::test(TournamentManagement::class)
            ->call('showCreate')
            ->set('form_name', 'QA Premier League')
            ->set('form_format', 'round_robin')
            ->set('form_num_teams', '6')
            ->set('form_overs', '10')
            ->set('form_contact_email', 'qa@example.com')
            ->set('form_contact_phone', '0771234567')
            ->call('createTournament')
            ->assertHasNoErrors()
            ->assertSet('view', 'detail');

        $league = LeagueLeague::where('name', 'QA Premier League')->firstOrFail();
        $this->assertSame(6, (int) $league->num_teams);
        $this->assertSame(15, (int) $league->squad_size);
        $this->assertSame(11, (int) $league->min_players);
        $this->assertSame($this->djangoUserId, (int) $league->created_by_id);
        $this->assertSame(['overs' => 10], $league->cricket_config);
    }

    public function test_tournament_team_can_be_added_after_creation(): void
    {
        $component = Livewire::test(TournamentManagement::class)
            ->set('form_name', 'QA Team League')
            ->call('createTournament')
            ->assertHasNoErrors()
            ->call('showAddTeam')
            ->set('team_name', 'Jaffna Kings')
            ->call('addTeam')
            ->assertHasNoErrors()
            ->assertSet('view', 'detail');

        $leagueId = $component->get('selectedTournamentId');
        $this->assertDatabaseHas('league_leagueteam', ['league_id' => $leagueId, 'team_short_name' => 'JAF']);
        $this->assertDatabaseHas('league_leaguepointstable', ['league_id' => $leagueId]);
    }

    public function test_tournament_rejects_invalid_contact_email(): void
    {
        Livewire::test(TournamentManagement::class)
            ->set('form_name', 'QA Email League')
            ->set('form_contact_email', 'not-an-email')
            ->call('createTournament')
            ->assertHasErrors('form_contact_email');
    }

    // ── Fixtures ───────────────────────────────────────────────────────────────

    private function createVenue(): int
    {
        return DB::table('booking_venue')->insertGetId([
            'name' => 'QA Arena',
            'address' => 'Jaffna',
            'rating' => 0,
            'reviews' => 0,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
            'analytics_enabled' => false,
            'venue_category' => 'sports',
            'is_featured' => false,
            'advance_payment_required' => false,
            'bank_transfer_payments_enabled' => true,
            'cash_payments_enabled' => true,
            'online_payments_enabled' => true,
            'points_redemption_enabled' => false,
            'venue_card_payments_enabled' => true,
            'booking_payment_mode' => 'full',
        ]);
    }

    private function createSport(int $venueId): int
    {
        return DB::table('booking_sport')->insertGetId([
            'venue_id' => $venueId,
            'name' => 'Cricket',
            'price' => 2500,
            'image' => 'sport.jpg',
            'available' => true,
            'status' => 'active',
            'maximum_court' => 2,
            'advance_required' => false,
            'average_rating' => 0,
            'created_at' => now(),
            'updated_at' => now(),
            'private_booking_min_duration_minutes' => 60,
            'private_booking_price_multiplier' => 1,
            'private_booking_pricing_mode' => 'multiplier',
            'private_booking_enabled' => false,
        ]);
    }

    private function createDjangoUser(string $email): int
    {
        return DB::table('users_user')->insertGetId([
            'password' => 'x',
            'email' => $email,
            'first_name' => 'Permanent',
            'last_name' => 'Player',
            'phone_number' => '0779998887',
            'is_superuser' => false,
            'is_active' => true,
            'is_staff' => false,
            'availability' => 'available',
            'is_public_profile' => true,
            'is_show_contact' => true,
            'points' => 0,
            'referral_code' => 'QA'.uniqid(),
            'username' => 'qa_'.uniqid(),
            'username_changes_used' => 0,
            'accepted_tnc' => true,
            'gender' => 'male',
            'allow_direct_team_add' => true,
            'point_penalty_debt' => 0,
            'reserved_points' => 0,
        ]);
    }

    private function createBooking(array $overrides = []): BookingBooking
    {
        return BookingBooking::create(array_merge([
            'game_name' => 'Cricket',
            'user_name' => 'QA Player',
            'user_number' => '0771234567',
            'court_number' => '1',
            'booking_date' => now()->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'duration' => 60,
            'price' => 2500,
            'payment_status' => 'Pending',
            'payment_method' => 'cash',
            'status' => 'Confirmed',
            'game_id_id' => $this->sportId,
            'complex_id_id' => $this->venueId,
        ], $overrides));
    }

    private function createPoolBooking(array $overrides = []): PoolsPoolbooking
    {
        $poolId = DB::table('pools_pool')->insertGetId([
            'name' => 'QA Pool',
            'status' => 'active',
            'capacity' => 50,
            'guest_names_required' => false,
            'max_admissions_per_booking' => 10,
            'created_at' => now(),
            'updated_at' => now(),
            'venue_id' => $this->venueId,
            'private_request_enabled' => false,
            'private_request_limit_type' => 'none',
            'private_request_limit_value' => 0,
            'max_sessions_per_booking' => 1,
            'private_booking_enabled' => false,
        ]);

        $occurrenceId = DB::table('pools_poolsessionoccurrence')->insertGetId([
            'pool_id' => $poolId,
            'session_date' => now()->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'name' => 'QA Slot',
            'capacity' => 50,
            'status' => 'open',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return PoolsPoolbooking::create(array_merge([
            'booking_reference' => 'POOL-QA'.uniqid(),
            'pool_id' => $poolId,
            'occurrence_id' => $occurrenceId,
            'user_name' => 'Pool Player',
            'user_number' => '0771234567',
            'financial_status' => 'Pending',
        ], $overrides));
    }

    private function createPermanentBooking(): int
    {
        return DB::table('booking_permanentbooking')->insertGetId([
            'recurring_config' => json_encode(['days' => ['monday']]),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'duration' => 60,
            'price' => 2500,
            'created_at' => now(),
            'user_id' => $this->djangoUserId,
            'sport_id' => $this->sportId,
            'complex_id' => $this->venueId,
            'is_active' => true,
            'status' => 'active',
        ]);
    }
}
