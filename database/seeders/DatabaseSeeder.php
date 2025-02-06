<?php

// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Request;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Other seeding logic can be added here as well

        // Seeding the requests directly here
        Request::create([
            'Status' => 'Pending',
            'First_Name' => 'Alice',
            'Last_Name' => 'Smith',
            'Nationality' => 'Canadian',
            'Location' => 'Toronto',
            'Format' => 'DOCX',
            'Attachment' => 'file.docx',
            'Date_Created' => now(),
            'Updated_Time' => now(),
            'Users_ID' => 7, // Make sure user with ID 7 exists
        ]);
        
        // Add more Request::create() calls if you want more data

        // You can add other model seeders here if needed
    }
}
