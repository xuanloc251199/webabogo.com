<?php

return [
    'name' => 'Onepays',
    'create' => 'New onepay',
    'webhook_secret' => 'Webhook Secret',
    'webhook_setup_guide' => [
        'title' => 'Onepay Webhook Setup Guide',
        'description' => 'Follow these steps to set up a Onepay webhook',
        'step_1_label' => 'Login to the Onepay Dashboard',
        'step_1_description' => 'Access the :link and click on the "Add Endpoint" button in the "Webhooks" section of the "Developers" tab.',
        'step_2_label' => 'Select Event and Configure Endpoint',
        'step_2_description' => 'Select the "payment_intent.succeeded" event and enter the following URL in the "Endpoint URL" field: :url',
        'step_3_label' => 'Add Endpoint',
        'step_3_description' => 'Click the "Add Endpoint" button to save the webhook.',
        'step_4_label' => 'Copy Signing Secret',
        'step_4_description' => 'Copy the "Signing Secret" value from the "Webhook Details" section and paste it into the "Onepay Webhook Secret" field in the "Onepay" section of the "Payment" tab in the "Settings" page.',
    ]
];
