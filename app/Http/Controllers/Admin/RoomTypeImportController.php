<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;

class RoomTypeImportController extends Controller
{
    public const SAMPLE_HEADERS = [
        'name',
        'room_type',
        'bed_type',
        'room_size',
        'description',
        'price',
        'weekend_price',
        'holiday_price',
        'base_guests',
        'max_guests',
        'extra_guest_fee',
        'discount_percent',
        'min_stay',
        'max_stay',
        'check_in_time',
        'check_out_time',
        'is_active',
        'is_featured',
        'sort_order',
        'features',
    ];

    public function sampleDownload()
    {
        $filename = 'room-type-import-sample.xlsx';
        $tmpFile = tempnam(sys_get_temp_dir(), 'room_type_sample_') . '.xlsx';

        $writer = new Writer();
        $writer->openToFile($tmpFile);

        $writer->addRow(Row::fromValues(self::SAMPLE_HEADERS));

        $samples = [
            [
                'Standard Room', 'standard', 'queen', 25,
                'A comfortable standard room with modern amenities and city view.',
                35000, 40000, 45000, 2, 3, 5000, 0, 1, 30, '14:00', '11:00', 'true', 'false', 1,
                'Free WiFi|fa-wifi,Air Conditioning|fa-snowflake,Flat Screen TV|fa-tv,Mini Bar|fa-glass-whiskey',
            ],
            [
                'Deluxe Room', 'deluxe', 'king', 35,
                'Spacious deluxe room with premium furnishings and balcony access.',
                55000, 65000, 75000, 2, 4, 7500, 10, 1, 30, '14:00', '11:00', 'true', 'true', 2,
                'Free WiFi|fa-wifi,Air Conditioning|fa-snowflake,Balcony|fa-door-open,Room Service|fa-concierge-bell,Jacuzzi|fa-bath',
            ],
            [
                'Executive Suite', 'suite', 'king', 55,
                'Luxurious suite with separate living area, workspace, and panoramic views.',
                95000, 110000, 130000, 2, 4, 10000, 0, 2, 30, '14:00', '11:00', 'true', 'true', 3,
                'Free WiFi|fa-wifi,Air Conditioning|fa-snowflake,Balcony|fa-door-open,Room Service|fa-concierge-bell,Jacuzzi|fa-bath,Workspace|fa-laptop,Minibar|fa-glass-whiskey',
            ],
        ];

        foreach ($samples as $sample) {
            $writer->addRow(Row::fromValues(array_map(fn ($v) => (string) $v, $sample)));
        }

        $writer->close();

        return response()->download($tmpFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ])->deleteFileAfterSend();
    }
}
