<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Translation extends Model
{
    use HasFactory;
    
    protected $fillable = ['locale', 'group', 'key', 'value'];

    // Get Default Language (Cached)
    public static function getTrnaslactionsByLocale(string $locale)
    {
        return Cache::rememberForever('translations_by_locale_' . $locale, function () use($locale) {
            $dbTranslations = self::where('locale', $locale)
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->group . '.' . $item->key => $item->value];
                })
                ->toArray();

            $defaults = self::getDefaultTranslations($locale);
            foreach ($defaults as $k => $v) {
                if (!isset($dbTranslations[$k])) {
                    $dbTranslations[$k] = $v;
                }
            }

            return $dbTranslations;
        });
    }

    public static function forgetCachedTranslations()
    {
        Cache::forget('translations_by_locale');
        Cache::forget('languages_list');
        $locales = ['en', 'bn', 'ar', 'al', 'az', 'bg', 'de', 'es', 'fr', 'id', 'tr', 'vi', 'pt', 'ms', 'sr'];
        foreach ($locales as $loc) {
            Cache::forget('translations_by_locale_' . $loc);
            Cache::forget('translations_' . $loc);
        }
    }

    public static function getDefaultTranslations(string $locale = 'en'): array
    {
        if ($locale === 'bn' || $locale === 'bangla') {
            return [
                'db.Tailor' => 'টেইলার',
                'db.Total Tailors' => 'মোট টেইলার',
                'db.Percentage Tailors' => 'কমিশন ভিত্তিক',
                'db.Monthly Tailors' => 'মাসিক ভিত্তিক',
                'db.Tailor List' => 'টেইলার তালিকা',
                'db.Add Tailor' => 'টেইলার যোগ করুন',
                'db.Edit Tailor' => 'টেইলার এডিট',
                'db.Work Type' => 'চুক্তির ধরন',
                'db.Work Agreement Type' => 'চুক্তির ধরন',
                'db.Rate / Percentage' => 'রেট / কমিশন',
                'db.Activity Count' => 'ডেলিভারি / মাস',
                'db.Total Collected' => 'মোট আদায়',
                'db.Total Due' => 'মোট বকেয়া',
                'db.Deliveries' => 'ডেলিভারি',
                'db.Payments' => 'পেমেন্ট',
                'db.Owner Share Percentage' => 'মালিকের কমিশন (%)',
                'db.Monthly Fixed Rate' => 'মাসিক নির্ধারিত রেট',
                'db.Active Status' => 'সক্রিয় স্ট্যাটাস',
                'db.Delivery List' => 'ডেলিভারি তালিকা',
                'db.Add Delivery' => 'নতুন ডেলিভারি',
                'db.Edit Delivery' => 'ডেলিভারি এডিট',
                'db.Total Delivery Amount' => 'মোট ডেলিভারি বিল',
                'db.Total Owner Share' => 'মালিকের মোট প্রাপ্য',
                'db.Filter Deliveries' => 'ডেলিভারি ফিল্টার',
                'db.Select Tailor' => 'টেইলার নির্বাচন করুন',
                'db.All Percentage Tailors' => 'সকল কমিশন ভিত্তিক টেইলার',
                'db.Date Range' => 'তারিখের রেঞ্জ',
                'db.Payment Status' => 'পেমেন্ট স্ট্যাটাস',
                'db.All Status' => 'সকল স্ট্যাটাস',
                'db.Filter' => 'ফিল্টার',
                'db.Delivery Amount' => 'ডেলিভারি পরিমাণ',
                'db.Owner Get' => 'মালিকের প্রাপ্য',
                'db.Paid Amount' => 'পরিশোধিত টাকা',
                'db.Due Amount' => 'বাকি টাকা',
                'db.Delivery Date' => 'ডেলিভারির তারিখ',
                'db.Add Payment' => 'পেমেন্ট গ্রহণ',
                'db.Add Payment for Due' => 'বকেয়া টাকা গ্রহণ',
                'db.Paying Amount' => 'পরিশোধের পরিমাণ',
                'db.Payment Date' => 'পেমেন্টের তারিখ',
                'db.Save Payment' => 'পেমেন্ট নিশ্চিত করুন',
                'db.Payment History' => 'পেমেন্ট হিস্টোরি',
                'db.Monthly Payment List' => 'মাসিক পেমেন্ট তালিকা',
                'db.Add Monthly Payment' => 'নতুন মাসিক পেমেন্ট',
                'db.Edit Monthly Payment' => 'মাসিক পেমেন্ট এডিট',
                'db.Total Monthly Collection' => 'মোট মাসিক জমা',
                'db.Total Monthly Tailors' => 'মোট মাসিক টেইলার',
                'db.Total Payment Records' => 'মোট পেমেন্ট রেকর্ড',
                'db.Filter Monthly Payments' => 'মাসিক পেমেন্ট ফিল্টার',
                'db.Select Monthly Tailor' => 'মাসিক টেইলার নির্বাচন করুন',
                'db.All Monthly Tailors' => 'সকল মাসিক টেইলার',
                'db.Monthly Tailor' => 'মাসিক টেইলার',
                'db.For Month / Period' => 'মাসের নাম / সময়কাল',
                'db.Month / Period' => 'মাস / সময়কাল',
                'db.Month' => 'মাস',
                'db.month' => 'মাস',
                'db.close' => 'বন্ধ',
                'db.Close' => 'বন্ধ',
                'db.Status' => 'স্ট্যাটাস',
                'db.Update' => 'আপডেট',
                'db.Percentage Basis' => 'কমিশন ভিত্তিক',
                'db.Monthly Basis' => 'মাসিক চুক্তি',
                'db.Owner share percentage per delivery (e.g. 25%)' => 'প্রতি ডেলিভারি থেকে দোকান মালিকের কত শতাংশ প্রাপ্য (যেমন: ২৫%)',
                'db.Monthly fixed amount to pay the shop' => 'প্রতি মাসে টেইলার দোকানকে কত টাকা ভাড়া/পেমেন্ট দিবেন',
                'db.Total delivery bill amount' => 'মোট ডেলিভারি বিলের টাকা',
                'db.Tailor agreed percentage' => 'টেইলারের নির্ধারিত পার্সেন্টেজ',
                'db.Total amount due to owner based on percentage' => 'পার্সেন্টেজ অনুযায়ী মালিকের মোট প্রাপ্য',
                'db.Amount paid immediately by tailor' => 'টেইলার তাৎক্ষণিক কত টাকা জমা দিল',
                'db.Remaining due from owner share' => 'মালিকের প্রাপ্য থেকে বাকি টাকা',
                'db.Enter amount up to total due' => 'সর্বোচ্চ বকেয়া টাকার পরিমাণ ইনপুট দিতে পারেন',
                'db.Phone Number' => 'ফোন নম্বর',
                'db.Address' => 'ঠিকানা',
                'db.Note' => 'নোট',
                'db.Amount' => 'পরিমাণ',
                'db.date' => 'তারিখ',
                'db.Date' => 'তারিখ',
                'db.reference' => 'রেফারেন্স',
                'db.Reference' => 'রেফারেন্স',
                'db.Total' => 'মোট',
                'db.Active' => 'সক্রিয়',
                'db.Inactive' => 'নিষ্ক্রিয়',
                'db.action' => 'অ্যাকশন',
                'db.Action' => 'অ্যাকশন',
                'db.name' => 'নাম',
                'db.Name' => 'নাম',
                'db.submit' => 'দাখিল করুন',
                'db.Submit' => 'দাখিল করুন',
                'db.Percentage' => 'শতাংশ',
                'db.Paid' => 'পরিশোধিত',
                'db.Partial' => 'আংশিক পরিশোধ',
                'db.Due' => 'বকেয়া',
                'db.All' => 'সকল',
                'db.All Time' => 'সর্বমোট',
                'db.Period Collection' => 'নির্দিষ্ট সময়ের আদায়',
                'db.Reset' => 'রিসেট',
                'db.Collected Amount' => 'আদায়ের পরিমাণ',
                'db.View Deliveries in this period' => 'এই সময়ের ডেলিভারিগুলো দেখুন',
                'db.View Payments in this period' => 'এই সময়ের পেমেন্টগুলো দেখুন',
                'db.Showing' => 'দেখানো হচ্ছে',
                'db.Search' => 'অনুসন্ধান',
                'db.records per page' => 'প্রতি পেজে রেকর্ড',
                'db.Are you sure want to delete?' => 'আপনি কি নিশ্চিত মুছে ফেলতে চান?',
                'db.The field labels marked with * are required input fields' => '* চিহ্নিত ফিল্ডগুলো পূরণ করা আবশ্যক',
            ];
        }

        return [
            'db.Tailor' => 'Tailor',
            'db.Total Tailors' => 'Total Tailors',
            'db.Percentage Tailors' => 'Percentage Basis',
            'db.Monthly Tailors' => 'Monthly Basis',
            'db.Tailor List' => 'Tailor List',
            'db.Add Tailor' => 'Add Tailor',
            'db.Edit Tailor' => 'Edit Tailor',
            'db.Work Type' => 'Work Agreement Type',
            'db.Work Agreement Type' => 'Work Agreement Type',
            'db.Rate / Percentage' => 'Rate / Percentage',
            'db.Activity Count' => 'Activity Count',
            'db.Total Collected' => 'Total Collected',
            'db.Total Due' => 'Total Due',
            'db.Deliveries' => 'Deliveries',
            'db.Payments' => 'Payments',
            'db.Owner Share Percentage' => 'Owner Share Percentage',
            'db.Monthly Fixed Rate' => 'Monthly Fixed Rate',
            'db.Active Status' => 'Active Status',
            'db.Delivery List' => 'Delivery List',
            'db.Add Delivery' => 'Add Delivery',
            'db.Edit Delivery' => 'Edit Delivery',
            'db.Total Delivery Amount' => 'Total Delivery Bill',
            'db.Total Owner Share' => 'Total Owner Share',
            'db.Filter Deliveries' => 'Filter Deliveries',
            'db.Select Tailor' => 'Select Tailor',
            'db.All Percentage Tailors' => 'All Percentage Tailors',
            'db.Date Range' => 'Date Range',
            'db.Payment Status' => 'Payment Status',
            'db.All Status' => 'All Status',
            'db.Filter' => 'Filter',
            'db.Delivery Amount' => 'Delivery Amount',
            'db.Owner Get' => 'Owner Share Amount',
            'db.Paid Amount' => 'Paid Amount',
            'db.Due Amount' => 'Due Amount',
            'db.Delivery Date' => 'Delivery Date',
            'db.Add Payment' => 'Add Payment',
            'db.Add Payment for Due' => 'Collect Due Payment',
            'db.Paying Amount' => 'Paying Amount',
            'db.Payment Date' => 'Payment Date',
            'db.Save Payment' => 'Confirm Payment',
            'db.Payment History' => 'Payment History',
            'db.Monthly Payment List' => 'Monthly Payment List',
            'db.Add Monthly Payment' => 'Add Monthly Payment',
            'db.Edit Monthly Payment' => 'Edit Monthly Payment',
            'db.Total Monthly Collection' => 'Total Monthly Collection',
            'db.Total Monthly Tailors' => 'Total Monthly Tailors',
            'db.Total Payment Records' => 'Total Payment Records',
            'db.Filter Monthly Payments' => 'Filter Monthly Payments',
            'db.Select Monthly Tailor' => 'Select Monthly Tailor',
            'db.All Monthly Tailors' => 'All Monthly Tailors',
            'db.Monthly Tailor' => 'Monthly Tailor',
            'db.For Month / Period' => 'For Month / Period',
            'db.Month / Period' => 'Month / Period',
            'db.Month' => 'Month',
            'db.month' => 'month',
            'db.close' => 'Close',
            'db.Close' => 'Close',
            'db.Status' => 'Status',
            'db.Update' => 'Update',
            'db.Percentage Basis' => 'Percentage Basis',
            'db.Monthly Basis' => 'Monthly Basis',
            'db.Owner share percentage per delivery (e.g. 25%)' => 'Owner share percentage per delivery (e.g. 25%)',
            'db.Monthly fixed amount to pay the shop' => 'Monthly fixed amount to pay the shop',
            'db.Total delivery bill amount' => 'Total delivery bill amount',
            'db.Tailor agreed percentage' => 'Tailor agreed percentage',
            'db.Total amount due to owner based on percentage' => 'Total amount due to owner based on percentage',
            'db.Amount paid immediately by tailor' => 'Amount paid immediately by tailor',
            'db.Remaining due from owner share' => 'Remaining due from owner share',
            'db.Enter amount up to total due' => 'Enter amount up to total due',
            'db.Phone Number' => 'Phone Number',
            'db.Address' => 'Address',
            'db.Note' => 'Note',
            'db.Amount' => 'Amount',
            'db.date' => 'Date',
            'db.Date' => 'Date',
            'db.reference' => 'Reference',
            'db.Reference' => 'Reference',
            'db.Total' => 'Total',
            'db.Active' => 'Active',
            'db.Inactive' => 'Inactive',
            'db.action' => 'Action',
            'db.Action' => 'Action',
            'db.name' => 'Name',
            'db.Name' => 'Name',
            'db.submit' => 'Submit',
            'db.Submit' => 'Submit',
            'db.Percentage' => 'Percentage',
            'db.Paid' => 'Paid',
            'db.Partial' => 'Partial',
            'db.Due' => 'Due',
            'db.All' => 'All',
            'db.All Time' => 'All Time',
            'db.Period Collection' => 'Period Collection',
            'db.Reset' => 'Reset',
            'db.Collected Amount' => 'Collected Amount',
            'db.View Deliveries in this period' => 'View Deliveries in this period',
            'db.View Payments in this period' => 'View Payments in this period',
            'db.Showing' => 'Showing',
            'db.Search' => 'Search',
            'db.records per page' => 'records per page',
            'db.Are you sure want to delete?' => 'Are you sure want to delete?',
            'db.The field labels marked with * are required input fields' => 'The field labels marked with * are required input fields',
        ];
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'locale');
    }
}
