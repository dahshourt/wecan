<?php

// this is the notification recipient types configuration

// to add a new notification recipient type
// 1. add the type definition here
// 2. make sure to that the case exists in NotificationService::getRecipientEmails()if not add it there
// for any inquiry please contact me(Ahmed Omar)

return [
    // the types are devided into 3 categories
    // 1. Dynamic - resolved from event context
    // 2. Team - resolved from config or group lookup
    // 3. Explicit - needs identifier

    // Dynamic - resolved from event context
    [
        'value' => 'cr_creator',
        'label' => 'CR Creator',
        'category' => 'Dynamic',
        'needs_identifier' => false,
        'description' => 'The user who created the CR'
    ],
    [
        'value' => 'division_manager',
        'label' => 'Division Manager',
        'category' => 'Dynamic',
        'needs_identifier' => false,
        'description' => 'Division manager for this CR'
    ],
    [
        'value' => 'developer',
        'label' => 'Developer',
        'category' => 'Dynamic',
        'needs_identifier' => false,
        'description' => 'Developer assigned to the CR'
    ],
    [
        'value' => 'tester',
        'label' => 'Tester',
        'category' => 'Dynamic',
        'needs_identifier' => false,
        'description' => 'Tester assigned to the CR'
    ],
    [
        'value' => 'designer',
        'label' => 'Designer',
        'category' => 'Dynamic',
        'needs_identifier' => false,
        'description' => 'Designer assigned to the CR'
    ],
    [
        'value' => 'cr_member',
        'label' => 'CR Member',
        'category' => 'Dynamic',
        'needs_identifier' => false,
        'description' => 'CR team member assigned to the CR'
    ],

    // Team-based - resolved from config or group lookup
    [
        'value' => 'cr_team',
        'label' => 'CR Team',
        'category' => 'Team',
        'needs_identifier' => false,
        'description' => 'CR Team group email'
    ],
    [
        'value' => 'qc_team',
        'label' => 'QC Team',
        'category' => 'Team',
        'needs_identifier' => false,
        'description' => 'QC Team group email'
    ],
    [
        'value' => 'sa_team',
        'label' => 'SA Team',
        'category' => 'Team',
        'needs_identifier' => false,
        'description' => 'SA Team group email'
    ],
    [
        'value' => 'as_team',
        'label' => 'AS Team',
        'category' => 'Team',
        'needs_identifier' => false,
        'description' => 'AS Team group email'
    ],
    [
        'value' => 'bo_team',
        'label' => 'BO Team',
        'category' => 'Team',
        'needs_identifier' => false,
        'description' => 'BO Team group email'
    ],
    [
        'value' => 'qa_team',
        'label' => 'QA Team',
        'category' => 'Team',
        'needs_identifier' => false,
        'description' => 'QA Team group email'
    ],
    [
        'value' => 'uat_team',
        'label' => 'UAT Team',
        'category' => 'Team',
        'needs_identifier' => false,
        'description' => 'UAT Team group email'
    ],
    [
        'value' => 'pmo_team',
        'label' => 'PMO Team',
        'category' => 'Team',
        'needs_identifier' => false,
        'description' => 'PMO Team group email'
    ],
    [
        'value' => 'assigned_dev_team',
        'label' => 'Assigned Dev Team',
        'category' => 'Team',
        'needs_identifier' => false,
        'description' => 'The development team assigned to handle the CR'
    ],
    [
        'value' => 'tech_teams',
        'label' => 'Technical Teams',
        'category' => 'Team',
        'needs_identifier' => false,
        'description' => 'All technical teams assigned to the CR'
    ],
    [
        'value' => 'cr_managers',
        'label' => 'CR Managers',
        'category' => 'Team',
        'needs_identifier' => false,
        'description' => 'CR Managers from config'
    ],
    [
        'value' => 'dm_bcc',
        'label' => 'Division Managers (BCC)',
        'category' => 'Team',
        'needs_identifier' => false,
        'description' => 'All division managers emails from config'
    ],
    [
        'value' => 'mds_group',
        'label' => 'MDS Group',
        'category' => 'Dynamic',
        'needs_identifier' => false,
        'description' => 'The group that owns the MDS record (for MDS notifications)'
    ],
    [
        'value' => 'defect_group',
        'label' => 'Defect Group',
        'category' => 'Dynamic',
        'needs_identifier' => false,
        'description' => 'The group assigned to the defect (for defect notifications)'
    ],
    [
        'value' => 'prerequisite_group',
        'label' => 'Prerequisite Group',
        'category' => 'Dynamic',
        'needs_identifier' => false,
        'description' => 'The group assigned to the prerequisite/assistance request (for prerequisite notifications)'
    ],

    // Explicit - needs identifier (Need Specific email or user or group)
    [
        'value' => 'static_email',
        'label' => 'Static Email',
        'category' => 'Explicit',
        'needs_identifier' => true,
        'identifier_type' => 'email',
        'description' => 'Send to a specific email address'
    ],
    [
        'value' => 'user',
        'label' => 'Specific User',
        'category' => 'Explicit',
        'needs_identifier' => true,
        'identifier_type' => 'user_id',
        'description' => 'Send to a specific user by ID'
    ],
    [
        'value' => 'group',
        'label' => 'Specific Group',
        'category' => 'Explicit',
        'needs_identifier' => true,
        'identifier_type' => 'group_id',
        'description' => 'Send to a specific group by ID'
    ],
    [
        'value' => 'cap_users',
        'label' => 'Cap Users',
        'category' => 'Explicit',
        'needs_identifier' => true,
        'identifier_type' => 'email',
        'description' => 'Send to a specific group by ID'
    ],
];
