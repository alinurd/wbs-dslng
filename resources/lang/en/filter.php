<?php

return [
    'title' => 'Filter Data',
    'reset' => 'Reset Filter',
    'reset_icon' => 'undo',
    
    'fields' => [
        'year' => 'Year',
        'complaint_type' => 'Complaint Type',
        'directorate' => 'Directorate',
        'status' => 'Status',
        'forward_to' => 'Forwarded To',
        'complaint_code' => 'Complaint Code',
    ],
    
    'placeholders' => [
        'year' => 'All Years',
        'type' => 'All Types',
        'directorate' => 'All Directorates',
        'status' => 'All Status',
        'forward_to' => 'All',
        'complaint_code' => 'Enter complaint code...',
        'year_option' => 'Year :year',
        'all_years' => 'All Years',
    ],
    
    'filter_badges' => [
        'year' => 'Year: :value',
        'type' => 'Type: :value',
        'directorate' => 'Directorate: :value',
        'status' => 'Status: :value',
        'forward_to' => 'Forward: :value',
        'complaint_code' => 'Code: :value',
    ],
    
    'messages' => [
        'no_active_filters' => 'No active filters',
        'active_filters' => 'Active Filters',
        'apply_filters' => 'Apply Filters',
        'clear_all' => 'Clear All',
    ],
];