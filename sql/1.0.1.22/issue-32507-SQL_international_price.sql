-- Update based on story tracker 32215 --
-- Query update for Country table on database SMS_API_V2 --

USE `SMS_API_V2`;

ALTER TABLE `COUNTRY` ADD `COUNTRY_CODE_REF` CHAR(2) CHARACTER SET latin1 NOT NULL AFTER `COUNTRY_CODE`;
ALTER TABLE `COUNTRY` ADD `PHONE_CODE` INT(8) UNSIGNED NULL DEFAULT 0 AFTER `COUNTRY_NAME`;
ALTER TABLE `COUNTRY` ADD UNIQUE `COUNTRY_CODE_REF_UNIQUE` (`COUNTRY_CODE_REF`); 

INSERT INTO `COUNTRY` (`COUNTRY_CODE`, `COUNTRY_CODE_REF`, `COUNTRY_NAME`, `PHONE_CODE`) VALUES
('ABW', 'AW', 'Aruba', 297),
('AFG', 'AF', 'Afghanistan', 93),
('AGO', 'AO', 'Angola', 244),
('AIA', 'AI', 'Anguilla', 1264),
('ALB', 'AL', 'Albania', 355),
('AND', 'AD', 'Andorra', 376),
('ANT', 'AN', 'Netherlands Antilles', 599),
('ARE', 'AE', 'United Arab Emirates', 971),
('ARG', 'AR', 'Argentina', 54),
('ARM', 'AM', 'Armenia', 374),
('ASM', 'AS', 'American Samoa', 1684),
('ATA', 'AQ', 'Antarctica', 0),
('ATF', 'TF', 'French Southern Territories', 0),
('ATG', 'AG', 'Antigua and Barbuda', 1268),
('AUS', 'AU', 'Australia', 61),
('AUT', 'AT', 'Austria', 43),
('AZE', 'AZ', 'Azerbaijan', 994),
('BDI', 'BI', 'Burundi', 257),
('BEL', 'BE', 'Belgium', 32),
('BEN', 'BJ', 'Benin', 229),
('BFA', 'BF', 'Burkina Faso', 226),
('BGD', 'BD', 'Bangladesh', 880),
('BGR', 'BG', 'Bulgaria', 359),
('BHR', 'BH', 'Bahrain', 973),
('BHS', 'BS', 'Bahamas', 1242),
('BIH', 'BA', 'Bosnia and Herzegovina', 387),
('BLR', 'BY', 'Belarus', 375),
('BLZ', 'BZ', 'Belize', 501),
('BMU', 'BM', 'Bermuda', 1441),
('BOL', 'BO', 'Bolivia', 591),
('BRA', 'BR', 'Brazil', 55),
('BRB', 'BB', 'Barbados', 1246),
('BRN', 'BN', 'Brunei Darussalam', 673),
('BTN', 'BT', 'Bhutan', 975),
('BVT', 'BV', 'Bouvet Island', 0),
('BWA', 'BW', 'Botswana', 267),
('CAF', 'CF', 'Central African Republic', 236),
('CAN', 'CA', 'Canada', 1),
('CCK', 'CC', 'Cocos (Keeling) Islands', 672),
('CHE', 'CH', 'Switzerland', 41),
('CHL', 'CL', 'Chile', 56),
('CHN', 'CN', 'China', 86),
('CIV', 'CI', 'Cote D\'Ivoire', 225),
('CMR', 'CM', 'Cameroon', 237),
('COD', 'CD', 'Congo, the Democratic Republic', 242),
('COG', 'CG', 'Congo', 242),
('COK', 'CK', 'Cook Islands', 682),
('COL', 'CO', 'Colombia', 57),
('COM', 'KM', 'Comoros', 269),
('CPV', 'CV', 'Cape Verde', 238),
('CRI', 'CR', 'Costa Rica', 506),
('CUB', 'CU', 'Cuba', 53),
('CXR', 'CX', 'Christmas Island', 61),
('CYM', 'KY', 'Cayman Islands', 1345),
('CYP', 'CY', 'Cyprus', 357),
('CZE', 'CZ', 'Czech Republic', 420),
('DEU', 'DE', 'Germany', 49),
('DJI', 'DJ', 'Djibouti', 253),
('DMA', 'DM', 'Dominica', 1767),
('DNK', 'DK', 'Denmark', 45),
('DOM', 'DO', 'Dominican Republic', 1809),
('DZA', 'DZ', 'Algeria', 213),
('ECU', 'EC', 'Ecuador', 593),
('EGY', 'EG', 'Egypt', 20),
('ERI', 'ER', 'Eritrea', 291),
('ESH', 'EH', 'Western Sahara', 212),
('ESP', 'ES', 'Spain', 34),
('EST', 'EE', 'Estonia', 372),
('ETH', 'ET', 'Ethiopia', 251),
('FIN', 'FI', 'Finland', 358),
('FJI', 'FJ', 'Fiji', 679),
('FLK', 'FK', 'Falkland Islands (Malvinas)', 500),
('FRA', 'FR', 'France', 33),
('FRO', 'FO', 'Faroe Islands', 298),
('FSM', 'FM', 'Micronesia, Federated States o', 691),
('GAB', 'GA', 'Gabon', 241),
('GBR', 'UK', 'United Kingdom', 44),
('GEO', 'GE', 'Georgia', 995),
('GHA', 'GH', 'Ghana', 233),
('GIB', 'GI', 'Gibraltar', 350),
('GIN', 'GN', 'Guinea', 224),
('GLP', 'GP', 'Guadeloupe', 590),
('GMB', 'GM', 'Gambia', 220),
('GNB', 'GW', 'Guinea-Bissau', 245),
('GNQ', 'GQ', 'Equatorial Guinea', 240),
('GRC', 'GR', 'Greece', 30),
('GRD', 'GD', 'Grenada', 1473),
('GRL', 'GL', 'Greenland', 299),
('GTM', 'GT', 'Guatemala', 502),
('GUF', 'GF', 'French Guiana', 594),
('GUM', 'GU', 'Guam', 1671),
('GUY', 'GY', 'Guyana', 592),
('HKG', 'HK', 'Hong Kong', 852),
('HMD', 'HM', 'Heard Island and Mcdonald Isla', 0),
('HND', 'HN', 'Honduras', 504),
('HRV', 'HR', 'Croatia', 385),
('HTI', 'HT', 'Haiti', 509),
('HUN', 'HU', 'Hungary', 36),
('IDN', 'ID', 'Indonesia', 62),
('IND', 'IN', 'India', 91),
('IOT', 'IO', 'British Indian Ocean Territory', 246),
('IRL', 'IE', 'Ireland', 353),
('IRN', 'IR', 'Iran, Islamic Republic of', 98),
('IRQ', 'IQ', 'Iraq', 964),
('ISL', 'IS', 'Iceland', 354),
('ISR', 'IL', 'Israel', 972),
('ITA', 'IT', 'Italy', 39),
('JAM', 'JM', 'Jamaica', 1876),
('JOR', 'JO', 'Jordan', 962),
('JPN', 'JP', 'Japan', 81),
('KAZ', 'KZ', 'Kazakhstan', 7),
('KEN', 'KE', 'Kenya', 254),
('KGZ', 'KG', 'Kyrgyzstan', 996),
('KHM', 'KH', 'Cambodia', 855),
('KIR', 'KI', 'Kiribati', 686),
('KNA', 'KN', 'Saint Kitts and Nevis', 1869),
('KOR', 'KR', 'Korea, Republic of', 82),
('KWT', 'KW', 'Kuwait', 965),
('LAO', 'LA', 'Lao People\'s Democratic Republ', 856),
('LBN', 'LB', 'Lebanon', 961),
('LBR', 'LR', 'Liberia', 231),
('LBY', 'LY', 'Libyan Arab Jamahiriya', 218),
('LCA', 'LC', 'Saint Lucia', 1758),
('LIE', 'LI', 'Liechtenstein', 423),
('LKA', 'LK', 'Sri Lanka', 94),
('LSO', 'LS', 'Lesotho', 266),
('LTU', 'LT', 'Lithuania', 370),
('LUX', 'LU', 'Luxembourg', 352),
('LVA', 'LV', 'Latvia', 371),
('MAC', 'MO', 'Macao', 853),
('MAR', 'MA', 'Morocco', 212),
('MCO', 'MC', 'Monaco', 377),
('MDA', 'MD', 'Moldova, Republic of', 373),
('MDG', 'MG', 'Madagascar', 261),
('MDV', 'MV', 'Maldives', 960),
('MEX', 'MX', 'Mexico', 52),
('MHL', 'MH', 'Marshall Islands', 692),
('MKD', 'MK', 'Macedonia, the Former Yugoslav', 389),
('MLI', 'ML', 'Mali', 223),
('MLT', 'MT', 'Malta', 356),
('MMR', 'MM', 'Myanmar', 95),
('MNG', 'MN', 'Mongolia', 976),
('MNP', 'MP', 'Northern Mariana Islands', 1670),
('MOZ', 'MZ', 'Mozambique', 258),
('MRT', 'MR', 'Mauritania', 222),
('MSR', 'MS', 'Montserrat', 1664),
('MTQ', 'MQ', 'Martinique', 596),
('MUS', 'MU', 'Mauritius', 230),
('MWI', 'MW', 'Malawi', 265),
('MYS', 'MY', 'Malaysia', 60),
('MYT', 'YT', 'Mayotte', 269),
('NAM', 'NA', 'Namibia', 264),
('NCL', 'NC', 'New Caledonia', 687),
('NER', 'NE', 'Niger', 227),
('NFK', 'NF', 'Norfolk Island', 672),
('NGA', 'NG', 'Nigeria', 234),
('NIC', 'NI', 'Nicaragua', 505),
('NIU', 'NU', 'Niue', 683),
('NLD', 'NL', 'Netherlands', 31),
('NOR', 'NO', 'Norway', 47),
('NPL', 'NP', 'Nepal', 977),
('NRU', 'NR', 'Nauru', 674),
('NZL', 'NZ', 'New Zealand', 64),
('OMN', 'OM', 'Oman', 968),
('PAK', 'PK', 'Pakistan', 92),
('PAN', 'PA', 'Panama', 507),
('PCN', 'PN', 'Pitcairn', 0),
('PER', 'PE', 'Peru', 51),
('PHL', 'PH', 'Philippines', 63),
('PLW', 'PW', 'Palau', 680),
('PNG', 'PG', 'Papua New Guinea', 675),
('POL', 'PL', 'Poland', 48),
('PRI', 'PR', 'Puerto Rico', 1787),
('PRK', 'KP', 'Korea, Democratic People\'s Rep', 850),
('PRT', 'PT', 'Portugal', 351),
('PRY', 'PY', 'Paraguay', 595),
('PSE', 'PS', 'Palestinian Territory, Occupie', 970),
('PYF', 'PF', 'French Polynesia', 689),
('QAT', 'QA', 'Qatar', 974),
('REU', 'RE', 'Reunion', 262),
('ROM', 'RO', 'Romania', 40),
('RUS', 'RU', 'Russian Federation', 70),
('RWA', 'RW', 'Rwanda', 250),
('SAU', 'SA', 'Saudi Arabia', 966),
('SCG', 'CS', 'Serbia and Montenegro', 381),
('SDN', 'SD', 'Sudan', 249),
('SEN', 'SN', 'Senegal', 221),
('SGP', 'SG', 'Singapore', 65),
('SGS', 'GS', 'South Georgia and the South Sa', 0),
('SHN', 'SH', 'Saint Helena', 290),
('SJM', 'SJ', 'Svalbard and Jan Mayen', 47),
('SLB', 'SB', 'Solomon Islands', 677),
('SLE', 'SL', 'Sierra Leone', 232),
('SLV', 'SV', 'El Salvador', 503),
('SMR', 'SM', 'San Marino', 378),
('SOM', 'SO', 'Somalia', 252),
('SPM', 'PM', 'Saint Pierre and Miquelon', 508),
('STP', 'ST', 'Sao Tome and Principe', 239),
('SUR', 'SR', 'Suriname', 597),
('SVK', 'SK', 'Slovakia', 421),
('SVN', 'SI', 'Slovenia', 386),
('SWE', 'SE', 'Sweden', 46),
('SWZ', 'SZ', 'Swaziland', 268),
('SYC', 'SC', 'Seychelles', 248),
('SYR', 'SY', 'Syrian Arab Republic', 963),
('TCA', 'TC', 'Turks and Caicos Islands', 1649),
('TCD', 'TD', 'Chad', 235),
('TGO', 'TG', 'Togo', 228),
('THA', 'TH', 'Thailand', 66),
('TJK', 'TJ', 'Tajikistan', 992),
('TKL', 'TK', 'Tokelau', 690),
('TKM', 'TM', 'Turkmenistan', 7370),
('TLS', 'TL', 'Timor-Leste', 670),
('TON', 'TO', 'Tonga', 676),
('TTO', 'TT', 'Trinidad and Tobago', 1868),
('TUN', 'TN', 'Tunisia', 216),
('TUR', 'TR', 'Turkey', 90),
('TUV', 'TV', 'Tuvalu', 688),
('TWN', 'TW', 'Taiwan, Province of China', 886),
('TZA', 'TZ', 'Tanzania, United Republic of', 255),
('UGA', 'UG', 'Uganda', 256),
('UKR', 'UA', 'Ukraine', 380),
('UMI', 'UM', 'United States Minor Outlying I', 1),
('URY', 'UY', 'Uruguay', 598),
('USA', 'US', 'United States', 1),
('UZB', 'UZ', 'Uzbekistan', 998),
('VAT', 'VA', 'Holy See (Vatican City State)', 39),
('VCT', 'VC', 'Saint Vincent and the Grenadin', 1784),
('VEN', 'VE', 'Venezuela', 58),
('VGB', 'VG', 'Virgin Islands, British', 1284),
('VIR', 'VI', 'Virgin Islands, U.s.', 1340),
('VNM', 'VN', 'Viet Nam', 84),
('VUT', 'VU', 'Vanuatu', 678),
('WLF', 'WF', 'Wallis and Futuna', 681),
('WSM', 'WS', 'Samoa', 684),
('YEM', 'YE', 'Yemen', 967),
('ZAF', 'ZA', 'South Africa', 27),
('ZMB', 'ZM', 'Zambia', 260),
('ZWE', 'ZW', 'Zimbabwe', 263)
ON DUPLICATE KEY UPDATE
`COUNTRY_CODE_REF` = VALUES(`COUNTRY_CODE_REF`),
`PHONE_CODE` = VALUES(`PHONE_CODE`);

SET FOREIGN_KEY_CHECKS=1;

-- Table creation on database BILL_PRICELIST --

USE `BILL_PRICELIST`;

CREATE TABLE `BILLING_INTERNATIONAL_PRICE`(
    `BILLING_INTERNATIONAL_PRICE_ID` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `COUNTRY_CODE_REF` CHAR(2) CHARACTER SET latin1 NOT NULL ,
    `UNIT_PRICE` DECIMAL(8, 2) NOT NULL DEFAULT 0,
    PRIMARY KEY(`BILLING_INTERNATIONAL_PRICE_ID`),
    KEY `COUNTRY_CODE_REF`(`COUNTRY_CODE_REF`),
	CONSTRAINT `COUNTRY_CODE_REF_FK` FOREIGN KEY(`COUNTRY_CODE_REF`) REFERENCES `SMS_API_V2`.`COUNTRY`(`COUNTRY_CODE_REF`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE = InnoDB;

ALTER TABLE `BILLING_PROFILE` ADD `USE_INTERNATIONAL_PRICE` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `BILLING_TYPE`;
