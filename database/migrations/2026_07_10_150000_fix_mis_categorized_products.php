<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix mis-categorized products based on user's data

        // Move KBF from PIPE to RAD COVER
        DB::table('product_catalog')
            ->where('product_description', 'PIPE')
            ->where('brand', 'KBF')
            ->update(['product_description' => 'RAD COVER']);

        // Move MINH ANH from PIPE to CENTER/SIDE STAND
        DB::table('product_catalog')
            ->where('product_description', 'PIPE')
            ->where('brand', 'MINH ANH')
            ->update(['product_description' => 'CENTER/SIDE STAND']);

        // Move MAGS brands from PIPE to MAGS
        $magsBrands = ['MUTARRU CNC', 'BOMX HEXA', 'BOMX 17S', 'BOMX LYNX STARMAGS', 'BOMX LYNX DRACO', 'BOMX LYNX', 'BOMX VELA', 'BOMX SABER', 'GEO RACING', 'G REN', 'G REN ANDROID', 'OKIMURA'];
        DB::table('product_catalog')
            ->where('product_description', 'PIPE')
            ->whereIn('brand', $magsBrands)
            ->update(['product_description' => 'MAGS']);

        // Move NA THONG brands from PIPE to FLAT SEAT
        $flatSeatBrands = ['NA THONG TL', 'NA THONG RL', 'NA THONG NR'];
        DB::table('product_catalog')
            ->where('product_description', 'PIPE')
            ->whereIn('brand', $flatSeatBrands)
            ->update(['product_description' => 'FLAT SEAT']);

        // Move RCB brands from PIPE to SHOCK
        $shockBrands = ['RCB A3', 'RCB S2', 'BOMX X2', 'BOMX XSTREET', 'BOMX BLAZE', 'BOMX PULSE', 'MUTARRU', 'SPARK S1'];
        DB::table('product_catalog')
            ->where('product_description', 'PIPE')
            ->whereIn('brand', $shockBrands)
            ->update(['product_description' => 'SHOCK']);

        // Move RCB CT600/SP800/SP811/SP500 from PIPE to MAGS
        $rcbMagsBrands = ['RCB CT600', 'RCB SP800', 'RCB SP811', 'RCB SP500'];
        DB::table('product_catalog')
            ->where('product_description', 'PIPE')
            ->whereIn('brand', $rcbMagsBrands)
            ->update(['product_description' => 'MAGS']);

        // Move MUTARRU PREM from PIPE to CVT
        DB::table('product_catalog')
            ->where('product_description', 'PIPE')
            ->where('brand', 'MUTARRU PREM')
            ->update(['product_description' => 'CVT']);

        // Move DISC brands from PIPE to DISC
        $discBrands = ['AEK', 'SHIJIRO', 'PTITANIUM', 'GALFER', 'MAXSPEED', 'MAXSPEED TDRIVE'];
        DB::table('product_catalog')
            ->where('product_description', 'PIPE')
            ->whereIn('brand', $discBrands)
            ->update(['product_description' => 'DISC']);

        // Move CALIPER brands from PIPE to CALIPER
        $caliperBrands = ['RCB ES', 'RCB S27', 'RCB S26', 'RCB', 'RCB 4 PISTON R1'];
        DB::table('product_catalog')
            ->where('product_description', 'PIPE')
            ->whereIn('brand', $caliperBrands)
            ->update(['product_description' => 'CALIPER']);

        // Move LEVER brands from PIPE to LEVER
        $leverBrands = ['RCB S3', 'RCB S3 W/STOPPER', 'RCB E2'];
        DB::table('product_catalog')
            ->where('product_description', 'PIPE')
            ->whereIn('brand', $leverBrands)
            ->update(['product_description' => 'LEVER']);

        // Move RCB E2/E3 from PIPE to CLUTCH PERCH
        DB::table('product_catalog')
            ->where('product_description', 'PIPE')
            ->where('brand', 'RCB E2/E3')
            ->update(['product_description' => 'CLUTCH PERCH']);

        // Move RCB TARMAX from PIPE to MONOSHOCK
        $monoshockBrands = ['RCB TARMAX', 'RCB TARMAX FLOW'];
        DB::table('product_catalog')
            ->where('product_description', 'PIPE')
            ->whereIn('brand', $monoshockBrands)
            ->update(['product_description' => 'MONOSHOCK']);

        // Move TQ6 from PIPE to FOOT REST
        DB::table('product_catalog')
            ->where('product_description', 'PIPE')
            ->where('brand', 'TQ6')
            ->update(['product_description' => 'FOOT REST']);

        // Move P TITANIUM from PIPE to ENGINE SUPPORT
        DB::table('product_catalog')
            ->where('product_description', 'PIPE')
            ->where('brand', 'P TITANIUM')
            ->update(['product_description' => 'ENGINE SUPPORT']);

        // Move DT10 from PIPE to SWING ARM
        DB::table('product_catalog')
            ->where('product_description', 'PIPE')
            ->where('brand', 'DT10')
            ->update(['product_description' => 'SWING ARM']);

        // Move V3 from PIPE to SWING ARM
        DB::table('product_catalog')
            ->where('product_description', 'PIPE')
            ->where('brand', 'V3')
            ->update(['product_description' => 'SWING ARM']);

        // Move H2C from PIPE to SIDE MIRROR
        DB::table('product_catalog')
            ->where('product_description', 'PIPE')
            ->where('brand', 'H2C')
            ->update(['product_description' => 'SIDE MIRROR']);

        // Move YAMAHA from PIPE to TIRE HUGGER
        DB::table('product_catalog')
            ->where('product_description', 'PIPE')
            ->where('brand', 'YAMAHA')
            ->update(['product_description' => 'TIRE HUGGER']);

        // Move OEM V1/V2 from PIPE to TIRE HUGGER
        $tireHuggerBrands = ['OEM V1', 'OEM V2'];
        DB::table('product_catalog')
            ->where('product_description', 'PIPE')
            ->whereIn('brand', $tireHuggerBrands)
            ->update(['product_description' => 'TIRE HUGGER']);

        // Move DC from PIPE to MONORACK FRAME
        DB::table('product_catalog')
            ->where('product_description', 'PIPE')
            ->where('brand', 'DC')
            ->update(['product_description' => 'MONORACK FRAME']);

        // Move KYTA from PIPE to QUICK THROTTLE
        DB::table('product_catalog')
            ->where('product_description', 'PIPE')
            ->where('brand', 'KYTA')
            ->update(['product_description' => 'QUICK THROTTLE']);

        // Move PRIMAAX/FDR/MUTARRU from PIPE to TIRE
        $tireBrands = ['PRIMAAX', 'FDR', 'MUTARRU'];
        DB::table('product_catalog')
            ->where('product_description', 'PIPE')
            ->whereIn('brand', $tireBrands)
            ->update(['product_description' => 'TIRE']);

        // Remove BRAND (generic) from PIPE
        DB::table('product_catalog')
            ->where('product_description', 'PIPE')
            ->where('brand', 'BRAND')
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed as this is a data cleanup
    }
};
