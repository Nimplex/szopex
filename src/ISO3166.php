<?php

/*
 * Conversion library for converting ISO 3166-1 alpha-3 country codes to language names.
 */

function iso3_to_language(string $code): ?array
{
    static $map = [
        'ABW' => [
            'localized' => '',
            'normalized' => '',
        ],
        'AFG' => [
            'localized' => '',
            'normalized' => '',
        ],
        'AGO' => [
            'localized' => '',
            'normalized' => '',
        ],
        'AIA' => [
            'localized' => '',
            'normalized' => '', 
        ],
        'ALA' => [
            'localized' => '',
            'normalized' => '',
        ],
        'ALB' => [
            'localized' => '',
            'normalized' => '',
        ],
        'AND' => [
            'localized' => '',
            'normalized' => '',
        ],
        'ARE' => [
            'localized' => '',
            'normalized' => '',
        ],
        'ARG' => [
            'localized' => '',
            'normalized' => '',
        ],
        'ARM' => [
            'localized' => '',
            'normalized' => '',
        ],
        'ASM' => [
            'localized' => '',
            'normalized' => '',
        ],
        'ATA' => [
            'localized' => '',
            'normalized' => '',
        ],
        'ATF' => [
            'localized' => '',
            'normalized' => '',
        ],
        'ATG' => [
            'localized' => '',
            'normalized' => '',
        ],
        'AUS' => [
            'localized' => '',
            'normalized' => '',
        ],
        'AUT' => [
            'localized' => '',
            'normalized' => '',
        ],
        'AZE' => [
            'localized' => '',
            'normalized' => '',
        ],
        'BDI' => [
            'localized' => '',
            'normalized' => '',
        ],
        'BEL' => [
            'localized' => '',
            'normalized' => '',
        ],
        'BEN' => [
            'localized' => '',
            'normalized' => '',
        ],
        'BES' => [
            'localized' => '',
            'normalized' => '',
        ],
        'BFA' => [
            'localized' => '',
            'normalized' => '',
        ],
        'BGD' => [
            'localized' => '',
            'normalized' => '',
        ],
        'BGR' => [
            'localized' => '',
            'normalized' => '',
        ],
        'BHR' => [
            'localized' => '',
            'normalized' => '',
        ],
        'BHS' => [
            'localized' => '',
            'normalized' => '',
        ],
        'BIH' => [
            'localized' => '',
            'normalized' => '',
        ],
        'BLM' => [
            'localized' => '',
            'normalized' => '',
        ],
        'BLR' => [
            'localized' => '',
            'normalized' => '',
        ],
        'BLZ' => [
            'localized' => '',
            'normalized' => '',
        ],
        'BMU' => [
            'localized' => '',
            'normalized' => '',
        ],
        'BOL' => [
            'localized' => '',
            'normalized' => '',
        ],
        'BRA' => [
            'localized' => '',
            'normalized' => '',
        ],
        'BRB' => [
            'localized' => '',
            'normalized' => '',
        ],
        'BRN' => [
            'localized' => '',
            'normalized' => '',
        ],
        'BTN' => [
            'localized' => '',
            'normalized' => '',
        ],
        'BVT' => [
            'localized' => '',
            'normalized' => '',
        ],
        'BWA' => [
            'localized' => '',
            'normalized' => '',
        ],
        'CAF' => [
            'localized' => '',
            'normalized' => '',
        ],
        'CAN' => [
            'localized' => '',
            'normalized' => '',
        ],
        'CCK' => [
            'localized' => '',
            'normalized' => '',
        ],
        'CHE' => [
            'localized' => '',
            'normalized' => '',
        ],
        'CHL' => [
            'localized' => '',
            'normalized' => '',
        ],
        'CHN' => [
            'localized' => '',
            'normalized' => '',
        ],
        'CIV' => [
            'localized' => '',
            'normalized' => '',
        ],
        'CMR' => [
            'localized' => '',
            'normalized' => '',
        ],
        'COD' => [
            'localized' => '',
            'normalized' => '',
        ],
        'COG' => [
            'localized' => '',
            'normalized' => '',
        ],
        'COK' => [
            'localized' => '',
            'normalized' => '',
        ],
        'COL' => [
            'localized' => '',
            'normalized' => '',
        ],
        'COM' => [
            'localized' => '',
            'normalized' => '',
        ],
        'CPV' => [
            'localized' => '',
            'normalized' => '',
        ],
        'CRI' => [
            'localized' => '',
            'normalized' => '',
        ],
        'CUB' => [
            'localized' => '',
            'normalized' => '',
        ],
        'CUW' => [
            'localized' => '',
            'normalized' => '',
        ],
        'CXR' => [
            'localized' => '',
            'normalized' => '',
        ],
        'CYM' => [
            'localized' => '',
            'normalized' => '',
        ],
        'CYP' => [
            'localized' => '',
            'normalized' => '',
        ],
        'CZE' => [
            'localized' => '',
            'normalized' => '',
        ],
        'DEU' => [
            'localized' => '',
            'normalized' => '',
        ],
        'DJI' => [
            'localized' => '',
            'normalized' => '',
        ],
        'DMA' => [
            'localized' => '',
            'normalized' => '',
        ],
        'DNK' => [
            'localized' => '',
            'normalized' => '',
        ],
        'DOM' => [
            'localized' => '',
            'normalized' => '',
        ],
        'DZA' => [
            'localized' => '',
            'normalized' => '',
        ],
        'ECU' => [
            'localized' => '',
            'normalized' => '',
        ],
        'EGY' => [
            'localized' => '',
            'normalized' => '',
        ],
        'ERI' => [
            'localized' => '',
            'normalized' => '',
        ],
        'ESH' => [
            'localized' => '',
            'normalized' => '',
        ],
        'ESP' => [
            'localized' => '',
            'normalized' => '',
        ],
        'EST' => [
            'localized' => '',
            'normalized' => '',
        ],
        'ETH' => [
            'localized' => '',
            'normalized' => '',
        ],
        'FIN' => [
            'localized' => '',
            'normalized' => '',
        ],
        'FJI' => [
            'localized' => '',
            'normalized' => '',
        ],
        'FLK' => [
            'localized' => '',
            'normalized' => '',
        ],
        'FRA' => [
            'localized' => '',
            'normalized' => '',
        ],
        'FRO' => [
            'localized' => '',
            'normalized' => '',
        ],
        'FSM' => [
            'localized' => '',
            'normalized' => '',
        ],
        'GAB' => [
            'localized' => '',
            'normalized' => '',
        ],
        'GBR' => [
            'localized' => '',
            'normalized' => '',
        ],
        'GEO' => [
            'localized' => '',
            'normalized' => '',
        ],
        'GGY' => [
            'localized' => '',
            'normalized' => '',
        ],
        'GHA' => [
            'localized' => '',
            'normalized' => '',
        ],
        'GIB' => [
            'localized' => '',
            'normalized' => '',
        ],
        'GIN' => [
            'localized' => '',
            'normalized' => '',
        ],
        'GLP' => [
            'localized' => '',
            'normalized' => '',
        ],
        'GMB' => [
            'localized' => '',
            'normalized' => '',
        ],
        'GNB' => [
            'localized' => '',
            'normalized' => '',
        ],
        'GNQ' => [
            'localized' => '',
            'normalized' => '',
        ],
        'GRC' => [
            'localized' => '',
            'normalized' => '',
        ],
        'GRD' => [
            'localized' => '',
            'normalized' => '',
        ],
        'GRL' => [
            'localized' => '',
            'normalized' => '',
        ],
        'GTM' => [
            'localized' => '',
            'normalized' => '',
        ],
        'GUF' => [
            'localized' => '',
            'normalized' => '',
        ],
        'GUM' => [
            'localized' => '',
            'normalized' => '',
        ],
        'GUY' => [
            'localized' => '',
            'normalized' => '',
        ],
        'HKG' => [
            'localized' => '',
            'normalized' => '',
        ],
        'HMD' => [
            'localized' => '',
            'normalized' => '',
        ],
        'HND' => [
            'localized' => '',
            'normalized' => '',
        ],
        'HRV' => [
            'localized' => '',
            'normalized' => '',
        ],
        'HTI' => [
            'localized' => '',
            'normalized' => '',
        ],
        'HUN' => [
            'localized' => '',
            'normalized' => '',
        ],
        'IDN' => [
            'localized' => '',
            'normalized' => '',
        ],
        'IMN' => [
            'localized' => '',
            'normalized' => '',
        ],
        'IND' => [
            'localized' => '',
            'normalized' => '',
        ],
        'IOT' => [
            'localized' => '',
            'normalized' => '',
        ],
        'IRL' => [
            'localized' => '',
            'normalized' => '',
        ],
        'IRN' => [
            'localized' => '',
            'normalized' => '',
        ],
        'IRQ' => [
            'localized' => '',
            'normalized' => '',
        ],
        'ISL' => [
            'localized' => '',
            'normalized' => '',
        ],
        'ISR' => [
            'localized' => '',
            'normalized' => '',
        ],
        'ITA' => [
            'localized' => '',
            'normalized' => '',
        ],
        'JAM' => [
            'localized' => '',
            'normalized' => '',
        ],
        'JEY' => [
            'localized' => '',
            'normalized' => '',
        ],
        'JOR' => [
            'localized' => '',
            'normalized' => '',
        ],
        'JPN' => [
            'localized' => '',
            'normalized' => '',
        ],
        'KAZ' => [
            'localized' => '',
            'normalized' => '',
        ],
        'KEN' => [
            'localized' => '',
            'normalized' => '',
        ],
        'KGZ' => [
            'localized' => '',
            'normalized' => '',
        ],
        'KHM' => [
            'localized' => '',
            'normalized' => '',
        ],
        'KIR' => [
            'localized' => '',
            'normalized' => '',
        ],
        'KNA' => [
            'localized' => '',
            'normalized' => '',
        ],
        'KOR' => [
            'localized' => '',
            'normalized' => '',
        ],
        'KWT' => [
            'localized' => '',
            'normalized' => '',
        ],
        'LAO' => [
            'localized' => '',
            'normalized' => '',
        ],
        'LBN' => [
            'localized' => '',
            'normalized' => '',
        ],
        'LBR' => [
            'localized' => '',
            'normalized' => '',
        ],
        'LBY' => [
            'localized' => '',
            'normalized' => '',
        ],
        'LCA' => [
            'localized' => '',
            'normalized' => '',
        ],
        'LIE' => [
            'localized' => '',
            'normalized' => '',
        ],
        'LKA' => [
            'localized' => '',
            'normalized' => '',
        ],
        'LSO' => [
            'localized' => '',
            'normalized' => '',
        ],
        'LTU' => [
            'localized' => '',
            'normalized' => '',
        ],
        'LUX' => [
            'localized' => '',
            'normalized' => '',
        ],
        'LVA' => [
            'localized' => '',
            'normalized' => '',
        ],
        'MAC' => [
            'localized' => '',
            'normalized' => '',
        ],
        'MAF' => [
            'localized' => '',
            'normalized' => '',
        ],
        'MAR' => [
            'localized' => '',
            'normalized' => '',
        ],
        'MCO' => [
            'localized' => '',
            'normalized' => '',
        ],
        'MDA' => [
            'localized' => '',
            'normalized' => '',
        ],
        'MDG' => [
            'localized' => '',
            'normalized' => '',
        ],
        'MDV' => [
            'localized' => '',
            'normalized' => '',
        ],
        'MEX' => [
            'localized' => '',
            'normalized' => '',
        ],
        'MHL' => [
            'localized' => '',
            'normalized' => '',
        ],
        'MKD' => [
            'localized' => '',
            'normalized' => '',
        ],
        'MLI' => [
            'localized' => '',
            'normalized' => '',
        ],
        'MLT' => [
            'localized' => '',
            'normalized' => '',
        ],
        'MMR' => [
            'localized' => '',
            'normalized' => '',
        ],
        'MNE' => [
            'localized' => '',
            'normalized' => '',
        ],
        'MNG' => [
            'localized' => '',
            'normalized' => '',
        ],
        'MNP' => [
            'localized' => '',
            'normalized' => '',
        ],
        'MOZ' => [
            'localized' => '',
            'normalized' => '',
        ],
        'MRT' => [
            'localized' => '',
            'normalized' => '',
        ],
        'MSR' => [
            'localized' => '',
            'normalized' => '',
        ],
        'MTQ' => [
            'localized' => '',
            'normalized' => '',
        ],
        'MUS' => [
            'localized' => '',
            'normalized' => '',
        ],
        'MWI' => [
            'localized' => '',
            'normalized' => '',
        ],
        'MYS' => [
            'localized' => '',
            'normalized' => '',
        ],
        'MYT' => [
            'localized' => '',
            'normalized' => '',
        ],
        'NAM' => [
            'localized' => '',
            'normalized' => '',
        ],
        'NCL' => [
            'localized' => '',
            'normalized' => '',
        ],
        'NER' => [
            'localized' => '',
            'normalized' => '',
        ],
        'NFK' => [
            'localized' => '',
            'normalized' => '',
        ],
        'NGA' => [
            'localized' => '',
            'normalized' => '',
        ],
        'NIC' => [
            'localized' => '',
            'normalized' => '',
        ],
        'NIU' => [
            'localized' => '',
            'normalized' => '',
        ],
        'NLD' => [
            'localized' => '',
            'normalized' => '',
        ],
        'NOR' => [
            'localized' => '',
            'normalized' => '',
        ],
        'NPL' => [
            'localized' => '',
            'normalized' => '',
        ],
        'NRU' => [
            'localized' => '',
            'normalized' => '',
        ],
        'NZL' => [
            'localized' => '',
            'normalized' => '',
        ],
        'OMN' => [
            'localized' => '',
            'normalized' => '',
        ],
        'PAK' => [
            'localized' => '',
            'normalized' => '',
        ],
        'PAN' => [
            'localized' => '',
            'normalized' => '',
        ],
        'PCN' => [
            'localized' => '',
            'normalized' => '',
        ],
        'PER' => [
            'localized' => '',
            'normalized' => '',
        ],
        'PHL' => [
            'localized' => '',
            'normalized' => '',
        ],
        'PLW' => [
            'localized' => '',
            'normalized' => '',
        ],
        'PNG' => [
            'localized' => '',
            'normalized' => '',
        ],
        'POL' => [
            'localized' => '',
            'normalized' => '',
        ],
        'PRI' => [
            'localized' => '',
            'normalized' => '',
        ],
        'PRK' => [
            'localized' => '',
            'normalized' => '',
        ],
        'PRT' => [
            'localized' => '',
            'normalized' => '',
        ],
        'PRY' => [
            'localized' => '',
            'normalized' => '',
        ],
        'PSE' => [
            'localized' => '',
            'normalized' => '',
        ],
        'PYF' => [
            'localized' => '',
            'normalized' => '',
        ],
        'QAT' => [
            'localized' => '',
            'normalized' => '',
        ],
        'REU' => [
            'localized' => '',
            'normalized' => '',
        ],
        'ROU' => [
            'localized' => '',
            'normalized' => '',
        ],
        'RUS' => [
            'localized' => '',
            'normalized' => '',
        ],
        'RWA' => [
            'localized' => '',
            'normalized' => '',
        ],
        'SAU' => [
            'localized' => '',
            'normalized' => '',
        ],
        'SDN' => [
            'localized' => '',
            'normalized' => '',
        ],
        'SEN' => [
            'localized' => '',
            'normalized' => '',
        ],
        'SGP' => [
            'localized' => '',
            'normalized' => '',
        ],
        'SGS' => [
            'localized' => '',
            'normalized' => '',
        ],
        'SHN' => [
            'localized' => '',
            'normalized' => '',
        ],
        'SJM' => [
            'localized' => '',
            'normalized' => '',
        ],
        'SLB' => [
            'localized' => '',
            'normalized' => '',
        ],
        'SLE' => [
            'localized' => '',
            'normalized' => '',
        ],
        'SLV' => [
            'localized' => '',
            'normalized' => '',
        ],
        'SMR' => [
            'localized' => '',
            'normalized' => '',
        ],
        'SOM' => [
            'localized' => '',
            'normalized' => '',
        ],
        'SPM' => [
            'localized' => '',
            'normalized' => '',
        ],
        'SRB' => [
            'localized' => '',
            'normalized' => '',
        ],
        'SSD' => [
            'localized' => '',
            'normalized' => '',
        ],
        'STP' => [
            'localized' => '',
            'normalized' => '',
        ],
        'SUR' => [
            'localized' => '',
            'normalized' => '',
        ],
        'SVK' => [
            'localized' => '',
            'normalized' => '',
        ],
        'SVN' => [
            'localized' => '',
            'normalized' => '',
        ],
        'SWE' => [
            'localized' => '',
            'normalized' => '',
        ],
        'SWZ' => [
            'localized' => '',
            'normalized' => '',
        ],
        'SXM' => [
            'localized' => '',
            'normalized' => '',
        ],
        'SYC' => [
            'localized' => '',
            'normalized' => '',
        ],
        'SYR' => [
            'localized' => '',
            'normalized' => '',
        ],
        'TCA' => [
            'localized' => '',
            'normalized' => '',
        ],
        'TCD' => [
            'localized' => '',
            'normalized' => '',
        ],
        'TGO' => [
            'localized' => '',
            'normalized' => '',
        ],
        'THA' => [
            'localized' => '',
            'normalized' => '',
        ],
        'TJK' => [
            'localized' => '',
            'normalized' => '',
        ],
        'TKL' => [
            'localized' => '',
            'normalized' => '',
        ],
        'TKM' => [
            'localized' => '',
            'normalized' => '',
        ],
        'TLS' => [
            'localized' => '',
            'normalized' => '',
        ],
        'TON' => [
            'localized' => '',
            'normalized' => '',
        ],
        'TTO' => [
            'localized' => '',
            'normalized' => '',
        ],
        'TUN' => [
            'localized' => '',
            'normalized' => '',
        ],
        'TUR' => [
            'localized' => '',
            'normalized' => '',
        ],
        'TUV' => [
            'localized' => '',
            'normalized' => '',
        ],
        'TWN' => [
            'localized' => '',
            'normalized' => '',
        ],
        'TZA' => [
            'localized' => '',
            'normalized' => '',
        ],
        'UGA' => [
            'localized' => '',
            'normalized' => '',
        ],
        'UKR' => [
            'localized' => '',
            'normalized' => '',
        ],
        'UMI' => [
            'localized' => '',
            'normalized' => '',
        ],
        'URY' => [
            'localized' => '',
            'normalized' => '',
        ],
        'USA' => [
            'localized' => '',
            'normalized' => '',
        ],
        'UZB' => [
            'localized' => '',
            'normalized' => '',
        ],
        'VAT' => [
            'localized' => '',
            'normalized' => '',
        ],
        'VCT' => [
            'localized' => '',
            'normalized' => '',
        ],
        'VEN' => [
            'localized' => '',
            'normalized' => '',
        ],
        'VGB' => [
            'localized' => '',
            'normalized' => '',
        ],
        'VIR' => [
            'localized' => '',
            'normalized' => '',
        ],
        'VNM' => [
            'localized' => '',
            'normalized' => '',
        ],
        'VUT' => [
            'localized' => '',
            'normalized' => '',
        ],
        'WLF' => [
            'localized' => '',
            'normalized' => '',
        ],
        'WSM' => [
            'localized' => '',
            'normalized' => '',
        ],
        'YEM' => [
            'localized' => '',
            'normalized' => '',
        ],
        'ZAF' => [
            'localized' => '',
            'normalized' => '',
        ],
        'ZMB' => [
            'localized' => '',
            'normalized' => '',
        ],
        'ZWE' => [
            'localized' => '',
            'normalized' => '',
        ]
    ];

    return $map[strtoupper($code)] ?? null;
}
