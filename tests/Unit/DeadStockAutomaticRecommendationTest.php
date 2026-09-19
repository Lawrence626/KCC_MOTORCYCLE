<?php

use App\Models\DeadStock;

test('automatic recommendation for 31 to 60 days unsold', function () {
    $deadStock = new DeadStock(['days_without_sale' => 45]);
    $rec = $deadStock->getAutomaticRecommendation();

    expect($rec['recommendation'])->toBe('Apply a discount to increase sales.');
    expect($rec['suggested_discount'])->toBe('5%');
    expect($rec['reason'])->toBe('Item has been unsold for 45 days.');
    expect($rec['suggested_discount_value'])->toBe(5);
});

test('automatic recommendation for 61 to 90 days unsold', function () {
    $deadStock = new DeadStock(['days_without_sale' => 75]);
    $rec = $deadStock->getAutomaticRecommendation();

    expect($rec['recommendation'])->toBe('Apply a discount to increase sales.');
    expect($rec['suggested_discount'])->toBe('10%–15% discount');
    expect($rec['reason'])->toBe('Item has been unsold for 75 days.');
    expect($rec['suggested_discount_value'])->toBe(15);
});

test('automatic recommendation for 91 and more days unsold', function () {
    $deadStock = new DeadStock(['days_without_sale' => 97]);
    $rec = $deadStock->getAutomaticRecommendation();

    expect($rec['recommendation'])->toBe('Apply a discount to increase sales.');
    expect($rec['suggested_discount'])->toBe('20% discount');
    expect($rec['reason'])->toBe('Item has been unsold for 97 days.');
    expect($rec['suggested_discount_value'])->toBe(20);
});

test('automatic recommendation boundary values', function () {
    // 31 days
    $ds31 = new DeadStock(['days_without_sale' => 31]);
    expect($ds31->getAutomaticRecommendation()['suggested_discount'])->toBe('5%');

    // 60 days
    $ds60 = new DeadStock(['days_without_sale' => 60]);
    expect($ds60->getAutomaticRecommendation()['suggested_discount'])->toBe('5%');

    // 61 days
    $ds61 = new DeadStock(['days_without_sale' => 61]);
    expect($ds61->getAutomaticRecommendation()['suggested_discount'])->toBe('10%–15% discount');

    // 90 days
    $ds90 = new DeadStock(['days_without_sale' => 90]);
    expect($ds90->getAutomaticRecommendation()['suggested_discount'])->toBe('10%–15% discount');

    // 91 days
    $ds91 = new DeadStock(['days_without_sale' => 91]);
    expect($ds91->getAutomaticRecommendation()['suggested_discount'])->toBe('20% discount');
});
