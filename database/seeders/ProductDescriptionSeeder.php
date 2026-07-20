<?php

namespace Database\Seeders;

use App\Models\ProductDescription;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductDescriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productDescriptions = [
            ['name' => 'PIPE',                       'sku_prefix' => 'PIP', 'is_expirable' => false, 'brands' => ['APIDO','KVIN','TRC','MVR1','MT8 TT','MT8 ST','MT8 V3','MT8 RL','MT8 CNC','ORBR ST','ORBR V2','ORBR BT','HUN','KENOCHI','SOLFILI','NAMBAN GT 125','TSMP']],
            ['name' => 'SHOCK',                      'sku_prefix' => 'SHK', 'is_expirable' => false, 'brands' => ['RCB A3','RCB S2','BOMX X2','BOMX XSTREET','BOMX BLAZE','BOMX PULSE','MUTARRU','SPARK S1']],
            ['name' => 'SWING ARM',                  'sku_prefix' => 'SWA', 'is_expirable' => false, 'brands' => ['DT10','V3','KBF','MINH ANH']],
            ['name' => 'ENGINE SUPPORT',             'sku_prefix' => 'ENS', 'is_expirable' => false, 'brands' => ['P TITANIUM','DT10']],
            ['name' => 'SIDE MIRROR',                'sku_prefix' => 'SMI', 'is_expirable' => false, 'brands' => ['H2C']],
            ['name' => 'TIRE HUGGER',                'sku_prefix' => 'THG', 'is_expirable' => false, 'brands' => ['YAMAHA','OEM V1','OEM V2']],
            ['name' => 'MONORACK FRAME',             'sku_prefix' => 'MNF', 'is_expirable' => false, 'brands' => ['DC']],
            ['name' => 'QUICK THROTTLE',             'sku_prefix' => 'QTR', 'is_expirable' => false, 'brands' => ['KYTA']],
            ['name' => 'TIRE',                       'sku_prefix' => 'TYP', 'is_expirable' => false, 'brands' => ['PRIMAAX','FDR CHAMPION','MUTARRU','QUICK','ZENEOS','PIRELLI','VEE RUBBER','MET ZELLER','CORSA PLATINUM','BEAST TIRE','MAXXIS','APC','ARISUN','JOURNEY']],
            ['name' => 'MAGS',                       'sku_prefix' => 'MAG', 'is_expirable' => false, 'brands' => ['MUTARRU CNC','BOMX HEXA','BOMX 17S','BOMX LYNX STAR MAGS','BOMX LYNX DRACO','BOMX HYDRA','BOMX VELA','BOMX SABER','GEO RACING','G REN','G REN ANDROID','OKIMURA','RCB CT600','RCB SP800','RCB SP811','RCB SP500','ENKEI']],
            ['name' => 'FLAT SEAT',                  'sku_prefix' => 'FLS', 'is_expirable' => false, 'brands' => ['NA THONG TL','NA THONG RL','NA THONG NR']],
            ['name' => 'INDO SEAT',                  'sku_prefix' => 'IDS', 'is_expirable' => false, 'brands' => ['NOI']],
            ['name' => 'RADIATOR CAP',               'sku_prefix' => 'RDC', 'is_expirable' => false, 'brands' => ['BRD']],
            ['name' => 'RADIATOR',                   'sku_prefix' => 'RAD', 'is_expirable' => false, 'brands' => ['BRD']],
            ['name' => 'RADIATOR COVER',             'sku_prefix' => 'RCV', 'is_expirable' => false, 'brands' => ['P TITANIUM']],
            ['name' => 'CVT',                        'sku_prefix' => 'CVT', 'is_expirable' => false, 'brands' => ['MUTARRU PREM']],
            ['name' => 'CVT HALF',                   'sku_prefix' => 'CVH', 'is_expirable' => false, 'brands' => ['MUTARRU PREM']],
            ['name' => 'FRONT SHOCK',                'sku_prefix' => 'FSK', 'is_expirable' => false, 'brands' => ['MUTARRU LIGHTEN','RCB']],
            ['name' => 'DISC',                       'sku_prefix' => 'DSC', 'is_expirable' => false, 'brands' => ['AEK','SHIJIRO','P TITANIUM','GALFER','MAXSPEED','MAXSPEED T-DRIVE']],
            ['name' => 'CALIPER',                    'sku_prefix' => 'CAL', 'is_expirable' => false, 'brands' => ['RCB ES','RCB S27','RCB S26','RCB']],
            ['name' => 'BRAKE MASTER',               'sku_prefix' => 'BKM', 'is_expirable' => false, 'brands' => ['RCB']],
            ['name' => 'BRAKE MASTER PAIR',          'sku_prefix' => 'BMP', 'is_expirable' => false, 'brands' => ['RCB']],
            ['name' => 'BRAKE MASTER PUMP',          'sku_prefix' => 'BPP', 'is_expirable' => false, 'brands' => ['RCB E3']],
            ['name' => 'LEVER',                      'sku_prefix' => 'LVR', 'is_expirable' => false, 'brands' => ['RCB S3','RCB S3 WITH STOPPER','RCB E2']],
            ['name' => 'MONOSHOCK',                  'sku_prefix' => 'MSK', 'is_expirable' => false, 'brands' => ['RCB TARMAX','RCB TARMAX FLOW']],
            ['name' => 'FOOT REST',                  'sku_prefix' => 'FTR', 'is_expirable' => false, 'brands' => ['TQ6']],
            ['name' => 'CLUTCH PERCH',               'sku_prefix' => 'CLP', 'is_expirable' => false, 'brands' => ['RCB E2/E3']],
            ['name' => 'ENGINE OIL',                 'sku_prefix' => 'ENO', 'is_expirable' => true,  'brands' => ['Motul','Castrol','Shell Advance','Yamalube','Honda Pro','Repsol']],
            ['name' => 'BRAKE FLUID (BRAKE OIL)',    'sku_prefix' => 'BKF', 'is_expirable' => true,  'brands' => ['Motul','Brembo','Castrol','Bosch','Honda','Yamaha']],
            ['name' => 'GEAR OIL',                   'sku_prefix' => 'GRO', 'is_expirable' => true,  'brands' => ['Motul','Shell','Yamalube','Honda','Castrol']],
            ['name' => 'COOLANT / RADIATOR COOLANT', 'sku_prefix' => 'COL', 'is_expirable' => true,  'brands' => ['Motul','Honda','Yamaha','Prestone','Caltex']],
            ['name' => 'CVT CLEANER',                'sku_prefix' => 'CVS', 'is_expirable' => true,  'brands' => ['Motul','Muc-Off','STP','ThreeBond']],
            ['name' => 'TIRE SEALANT',               'sku_prefix' => 'TRS', 'is_expirable' => true,  'brands' => ['Slime','Tireject','FlatOut','Cyclo']],
        ];

        foreach ($productDescriptions as $desc) {
            ProductDescription::updateOrCreate(
                ['name' => $desc['name']],
                [
                    'sku_prefix'   => $desc['sku_prefix'],
                    'brands'       => $desc['brands'],
                    'is_expirable' => $desc['is_expirable'],
                    'is_active'    => true,
                ]
            );
        }
    }
}
