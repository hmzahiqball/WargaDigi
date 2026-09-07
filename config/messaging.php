<?php

return [
    'default' => env('MESSAGING_DRIVER', 'whatsapp'),

    'drivers' => [
        'telegram' => [
            'name' => 'telegram',
            'label' => 'Telegram',
            'icon' => 'bi bi-telegram',
            'outline_button_class' => 'btn-outline-telegram',
            'solid_button_class' => 'btn-telegram',
        ],
        'whatsapp' => [
            'name' => 'whatsapp',
            'label' => 'WhatsApp',
            'icon' => 'bi bi-whatsapp',
            'outline_button_class' => 'btn-outline-whatsapp',
            'solid_button_class' => 'btn-whatsapp',
        ],
    ],
];
