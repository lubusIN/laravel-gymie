<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('preserves leading zeroes in pincode columns', function (): void {
    $pincode = '001234';
    $userId = DB::table('users')->insertGetId([
        'email' => 'owner@example.com',
        'pincode' => $pincode,
    ]);

    DB::table('enquiries')->insert([
        'user_id' => $userId,
        'email' => 'lead@example.com',
        'pincode' => $pincode,
    ]);

    DB::table('members')->insert([
        'email' => 'member@example.com',
        'pincode' => $pincode,
    ]);

    expect(DB::table('users')->value('pincode'))->toBe($pincode)
        ->and(DB::table('enquiries')->value('pincode'))->toBe($pincode)
        ->and(DB::table('members')->value('pincode'))->toBe($pincode);
});
