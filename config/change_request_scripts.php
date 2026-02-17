<?php

return [
    'locked_statuses' => [
        "Reject",
        "Closed",
        "CR Closed",
        "Reject kam",
        "Closed kam"
    ],

    'rejection_reason_statuses' => [
        "Reject",
        "Reject kam",
        "CR Team FB"
    ],

    'cap_users' => [
        'show' => [
            "Pending CAB",
            "Pending CAB Approval",
            "CR Doc Valid"
        ],
        'hide' => [
            "Pending CR Document Validation",
            "Pending Update CR Doc"
        ],
    ],

    'workload' => [
        'mandatory' => ["Release Plan"],
        'optional' => ["Pending Business"],
    ],

    'technical_teams' => [
        'hide_statuses' => ["260", "223", "273"],
        'hide_texts' => [
            "Test in Progress",
            "Pending HL Design",
            "Assess the defects"
        ],
        'required_statuses' => ["257", "220", "276", "275"],
        'extra_required_texts' => [
            "Pending CD FB",
            "Request MD's"
        ],
    ],

    'designer_required_statuses' => [
        "Pending Design",
        "Pending Design kam"
    ],

    'testing_estimation' => [
        'status_text' => 'Testing Estimation'
    ],

    'promo' => [
        'tech_teams_mandatory_status' => 'SA FB',
        'tech_teams_mandatory_status_id' => '141',
        'review_cd_id' => '100'
    ],

    'defects' => [
        'final_uat_text' => "Final UAT Results & FB"
    ]
];
