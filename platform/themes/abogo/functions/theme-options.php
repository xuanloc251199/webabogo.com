<?php

app()->booted(function () {
    theme_option()
        ->setField([
            'id' => 'copyright',
            'section_id' => 'opt-text-subsection-general',
            'type' => 'text',
            'label' => __('Copyright'),
            'attributes' => [
                'name' => 'copyright',
                'value' => __('© :year Your Company. All right reserved.', ['year' => now()->format('Y')]),
                'options' => [
                    'class' => 'form-control',
                    'placeholder' => __('Change copyright'),
                    'data-counter' => 250,
                ],
            ],
            'helper' => __('Copyright on footer of site'),
        ])
        ->setField([
            'id' => 'primary_font',
            'section_id' => 'opt-text-subsection-general',
            'type' => 'googleFonts',
            'label' => __('Primary font'),
            'attributes' => [
                'name' => 'primary_font',
                'value' => 'Roboto',
            ],
        ])
        ->setField([
            'id' => 'primary_color',
            'section_id' => 'opt-text-subsection-general',
            'type' => 'customColor',
            'label' => __('Primary color'),
            'attributes' => [
                'name' => 'primary_color',
                'value' => '#ff2b4a',
            ],
        ])
        ->setSection([
            'title' => __('Partners'),
            'desc' => __('Partners'),
            'id' => 'opt-text-subsection-partners',
            'subsection' => true,
            'icon' => 'ti ti-share',
        ])
        ->setField([
            'id' => 'to_partners',
            'section_id' => 'opt-text-subsection-partners',
            'type' => 'repeater',
            'label' => __('Partners'),
            'attributes' => [
                'name' => 'to_partners',
                'value' => null,
                'fields' => [
                    [
                        'type' => 'mediaImage',
                        'label' => __('Image'),
                        'attributes' => [
                            'name' => 'partner_logo',
                            'value' => null,
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => __('Name'),
                        'attributes' => [
                            'name' => 'partner_name',
                            'value' => null,
                            'options' => [
                                'class' => 'form-control',
                            ],
                        ],
                    ],
                ],
            ],
        ])
        ->setSection([
            'title' => __('Awards'),
            'desc' => __('Awards'),
            'id' => 'opt-text-subsection-awards',
            'subsection' => true,
            'icon' => 'ti ti-share',
        ])
        ->setField([
            'id' => 'to_awards',
            'section_id' => 'opt-text-subsection-awards',
            'type' => 'repeater',
            'label' => __('Awards'),
            'attributes' => [
                'name' => 'to_awards',
                'value' => null,
                'fields' => [
                    [
                        'type' => 'mediaImage',
                        'label' => __('Image'),
                        'attributes' => [
                            'name' => 'award_image',
                            'value' => null,
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => __('Name'),
                        'attributes' => [
                            'name' => 'award_name',
                            'value' => null,
                            'options' => [
                                'class' => 'form-control',
                            ],
                        ],
                    ],
                ],
            ],
        ])
        ->setSection([
            'title' => __('CTA'),
            'desc' => __('CTA'),
            'id' => 'opt-text-subsection-social-links',
            'subsection' => true,
            'icon' => 'ti ti-share',
        ])
        ->setField([
            'id' => 'social_links',
            'section_id' => 'opt-text-subsection-social-links',
            'type' => 'repeater',
            'label' => __('CTA'),
            'attributes' => [
                'name' => 'social_links',
                'value' => null,
                'fields' => [
                    [
                        'type' => 'text',
                        'label' => __('Name'),
                        'attributes' => [
                            'name' => 'social-name',
                            'value' => null,
                            'options' => [
                                'class' => 'form-control',
                            ],
                        ],
                    ],
                    [
                        'type' => 'themeIcon',
                        'label' => __('Icon'),
                        'attributes' => [
                            'name' => 'social-icon',
                            'value' => null,
                            'options' => [
                                'class' => 'form-control',
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => __('URL'),
                        'attributes' => [
                            'name' => 'social-url',
                            'value' => null,
                            'options' => [
                                'class' => 'form-control',
                            ],
                        ],
                    ],
                    [
                        'type' => 'customColor',
                        'label' => __('Color'),
                        'attributes' => [
                            'name' => 'social-color',
                            'value' => null,
                            'options' => [
                                'class' => 'form-control',
                            ],
                        ],
                    ],
                    [
                        'type' => 'mediaImage',
                        'label' => __('Image'),
                        'attributes' => [
                            'name' => 'social-image',
                            'value' => null,
                        ],
                    ],
                    [
                        'type' => 'customColor',
                        'label' => __('Background Color'),
                        'attributes' => [
                            'name' => 'social-background-color',
                            'value' => null,
                            'options' => [
                                'class' => 'form-control',
                            ],
                        ],
                    ],
                ],
            ],
        ])
        ->setSection([
            'title' => __('Profile Contact About'),
            'desc' => __('Profile Contact About'),
            'id' => 'opt-text-subsection-profile-contact-about',
            'subsection' => true,
            'icon' => 'ti ti-user-circle',
        ])

        ->setField([
            'id' => 'contact_image',
            'section_id' => 'opt-text-subsection-profile-contact-about',
            'type' => 'mediaImage',
            'label' => __('Contact Image'),
            'attributes' => [
                'name' => 'contact_image',
                'value' => null,
            ],
        ])
        ->setField([
            'id' => 'contact_description',
            'section_id' => 'opt-text-subsection-profile-contact-about',
            'type' => 'editor',
            'label' => __('Nội dung giới thiệu'),
            'attributes' => [
                'name' => 'contact_description',
                'value' => null, // Default value
                'options' => [ // Optional
                    'class' => 'form-control theme-option-textarea',
                    'row' => '10',
                ],
            ]
        ])

        ->setField([
            'id' => 'contact_mission',
            'section_id' => 'opt-text-subsection-profile-contact-about',
            'type' => 'repeater',
            'label' => "Tầm nhìn, Sứ mệnh",
            'attributes' => [
                'name' => 'contact_mission',
                'value' => null,
                'fields' => [
                    [
                        'type' => 'mediaImage',
                        'label' => __('Image'),
                        'attributes' => [
                            'name' => 'contact_mission_image',
                            'value' => null,
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => __('Name'),
                        'attributes' => [
                            'name' => 'contact_mission_name',
                            'value' => null,
                            'options' => [
                                'class' => 'form-control',
                            ],
                        ],
                    ],
                ],
            ],
        ])

        ->setField([
            'id' => 'contact_market_size',
            'section_id' => 'opt-text-subsection-profile-contact-about',
            'type' => 'mediaImage',
            'label' => "Quy mô thị trường",
            'attributes' => [
                'name' => 'contact_market_size',
                'value' => null,
            ],
        ])
        ->setSection([
            'title' => __('Profile Contact'),
            'desc' => __('Profile Contact Information'),
            'id' => 'opt-text-subsection-profile-contact',
            'subsection' => true,
            'icon' => 'ti ti-user-circle',
        ])
        ->setField([
            'id' => 'profile_name',
            'section_id' => 'opt-text-subsection-profile-contact',
            'type' => 'text',
            'label' => __('Profile Name'),
            'attributes' => [
                'name' => 'profile_name',
                'value' => 'Abogo',
                'options' => [
                    'class' => 'form-control',
                    'placeholder' => __('Enter profile name'),
                ],
            ],
        ])
        ->setField([
            'id' => 'profile_avatar',
            'section_id' => 'opt-text-subsection-profile-contact',
            'type' => 'mediaImage',
            'label' => __('Profile Avatar'),
            'attributes' => [
                'name' => 'profile_avatar',
                'value' => null,
            ],
        ])
        ->setField([
            'id' => 'contact_phone',
            'section_id' => 'opt-text-subsection-profile-contact',
            'type' => 'text',
            'label' => __('Contact Phone'),
            'attributes' => [
                'name' => 'contact_phone',
                'value' => null,
                'options' => [
                    'class' => 'form-control',
                    'placeholder' => __('Enter phone number'),
                ],
            ],
        ])
        ->setField([
            'id' => 'contact_email',
            'section_id' => 'opt-text-subsection-profile-contact',
            'type' => 'email',
            'label' => __('Contact Email'),
            'attributes' => [
                'name' => 'contact_email',
                'value' => null,
                'options' => [
                    'class' => 'form-control',
                    'placeholder' => __('Enter email address'),
                ],
            ],
        ])
        ->setField([
            'id' => 'website_url',
            'section_id' => 'opt-text-subsection-profile-contact',
            'type' => 'text',
            'label' => __('Website URL'),
            'attributes' => [
                'name' => 'website_url',
                'value' => null,
                'options' => [
                    'class' => 'form-control',
                    'placeholder' => __('https://example.com'),
                ],
            ],
        ])
        ->setField([
            'id' => 'facebook_url',
            'section_id' => 'opt-text-subsection-profile-contact',
            'type' => 'text',
            'label' => __('Facebook URL'),
            'attributes' => [
                'name' => 'facebook_url',
                'value' => null,
                'options' => [
                    'class' => 'form-control',
                    'placeholder' => __('https://facebook.com/username'),
                ],
            ],
        ])
        ->setField([
            'id' => 'messenger_url',
            'section_id' => 'opt-text-subsection-profile-contact',
            'type' => 'text',
            'label' => __('Messenger URL'),
            'attributes' => [
                'name' => 'messenger_url',
                'value' => null,
                'options' => [
                    'class' => 'form-control',
                    'placeholder' => __('https://m.me/username'),
                ],
            ],
        ])
        ->setField([
            'id' => 'instagram_url',
            'section_id' => 'opt-text-subsection-profile-contact',
            'type' => 'text',
            'label' => __('Instagram URL'),
            'attributes' => [
                'name' => 'instagram_url',
                'value' => null,
                'options' => [
                    'class' => 'form-control',
                    'placeholder' => __('https://instagram.com/username'),
                ],
            ],
        ])
        ->setField([
            'id' => 'wechat_url',
            'section_id' => 'opt-text-subsection-profile-contact',
            'type' => 'text',
            'label' => __('WeChat URL'),
            'attributes' => [
                'name' => 'wechat_url',
                'value' => null,
                'options' => [
                    'class' => 'form-control',
                    'placeholder' => __('WeChat contact info'),
                ],
            ],
        ])
        ->setField([
            'id' => 'line_url',
            'section_id' => 'opt-text-subsection-profile-contact',
            'type' => 'text',
            'label' => __('Line URL'),
            'attributes' => [
                'name' => 'line_url',
                'value' => null,
                'options' => [
                    'class' => 'form-control',
                    'placeholder' => __('Line contact info'),
                ],
            ],
        ])
        ->setField([
            'id' => 'whatsapp_number',
            'section_id' => 'opt-text-subsection-profile-contact',
            'type' => 'text',
            'label' => __('WhatsApp Url'),
            'attributes' => [
                'name' => 'whatsapp_number',
                'value' => null,
                'options' => [
                    'class' => 'form-control',
                    'placeholder' => __('84987654321 (without +)'),
                ],
            ],
        ])
        ->setField([
            'id' => 'tiktok_url',
            'section_id' => 'opt-text-subsection-profile-contact',
            'type' => 'text',
            'label' => __('TikTok URL'),
            'attributes' => [
                'name' => 'tiktok_url',
                'value' => null,
                'options' => [
                    'class' => 'form-control',
                    'placeholder' => __('https://tiktok.com/@username'),
                ],
            ],
        ])
        ->setField([
            'id' => 'kakaotalk_url',
            'section_id' => 'opt-text-subsection-profile-contact',
            'type' => 'text',
            'label' => __('KakaoTalk URL'),
            'attributes' => [
                'name' => 'kakaotalk_url',
                'value' => null,
                'options' => [
                    'class' => 'form-control',
                    'placeholder' => __('KakaoTalk contact info'),
                ],
            ],
        ]);
});
