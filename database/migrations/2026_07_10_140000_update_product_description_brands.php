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
        // Update brand mappings for product descriptions
        $brandMappings = [
            'PIPE' => ['APIDO', 'KVIN', 'TRC', 'MVR1', 'MT8 TT', 'MT8 ST', 'MT8 V3', 'MT8 RL', 'MT8 CNC', 'ORBR ST', 'ORBR V2', 'ORBR BT', 'HUN', 'KENOCHI', 'SOLFILI', 'NAMBAN', 'TSMP'],
            'SHOCK' => ['RCB A3', 'RCB S2', 'BOMX X2', 'BOMX XSTREET', 'BOMX BLAZE', 'BOMX PULSE', 'MUTARRU', 'SPARK S1'],
            'SWING ARM' => ['DT10', 'V3'],
            'RAD COVER' => ['KBF'],
            'CENTER/SIDE STAND' => ['MINH ANH'],
            'ENGINE SUPPORT' => ['P TITANIUM', 'DT10'],
            'SIDE MIRROR' => ['H2C'],
            'TIRE HUGGER' => ['YAMAHA', 'OEM V1', 'OEM V2'],
            'MONORACK FRAME' => ['DC'],
            'QUICK THROTTLE' => ['KYTA'],
            'HEAT GUARD' => [],
            'TIRE' => ['PRIMAAX', 'FDR', 'MUTARRU'],
            'MAGS' => ['MUTARRU CNC', 'BOMX HEXA', 'BOMX 17S', 'BOMX LYNX STARMAGS', 'BOMX LYNX DRACO', 'BOMX LYNX', 'BOMX HYDRA', 'BOMX VELA', 'BOMX SABER', 'GEO RACING', 'G REN', 'G REN ANDROID', 'OKIMURA', 'RCB CT600', 'RCB SP800', 'RCB SP811', 'RCB SP500', 'ENKEI'],
            'TIRES' => ['PRIMAAX', 'QUICK', 'ZENEOS', 'PIRELLI', 'VEE RUBBER', 'METZELLER', 'CORSA PLATINUM', 'BEAST', 'MAXXIS', 'APC', 'ARISUN', 'JOURNEY', 'FDR CHAMPION', 'MUTARRU'],
            'FLAT SEAT' => ['NA THONG TL', 'NA THONG RL', 'NA THONG NR'],
            'INDO SEAT' => [],
            'NOI' => [],
            'RADIATOR CAP' => ['BRD'],
            'RADIATOR' => ['BRD'],
            'RADIATOR COVER' => ['PTITANIUM'],
            'BRAKE SHOE' => ['RCB'],
            'CVT' => ['MUTARRU PREM'],
            'CVT HALF' => ['MUTARRU PREM'],
            'FRONT SHOCK' => ['MUTARRU LIGHTEN', 'RCB'],
            'DISC' => ['AEK', 'SHIJIRO', 'PTITANIUM', 'GALFER', 'MAXSPEED', 'MAXSPEED TDRIVE'],
            'CALIPER' => ['RCB ES', 'RCB S27', 'RCB S26', 'RCB', 'RCB 4 PISTON R1'],
            'BRAKE MASTER' => ['RCB'],
            'BRAKE MASTER PAIR' => ['RCB'],
            'BRAKE MASTER PUMP' => ['RCB E3'],
            'LEVER' => ['RCB S3', 'RCB S3 W/STOPPER', 'RCB E2'],
            'MONOSHOCK' => ['RCB TARMAX', 'RCB TARMAX FLOW'],
            'FOOT REST' => ['TQ6'],
            'CLUTCH PERCH' => ['RCB E2/E3'],
            'ENGINE OIL' => [],
            'BRAKE FLUID (BRAKE OIL)' => [],
            'GEAR OIL' => [],
            'COOLANT / RADIATOR COOLANT' => [],
            'CVT CLEANER' => [],
            'TIRE SEALANT' => [],
        ];

        foreach ($brandMappings as $description => $brands) {
            DB::table('product_descriptions')
                ->where('name', $description)
                ->update(['brands' => json_encode($brands)]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed as this is a data update
    }
};
