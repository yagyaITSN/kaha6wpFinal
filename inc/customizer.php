<?php

function kaha6_customizer($wp_customize)
{
    $wp_customize->add_section(
        'section_header_and_footer',
        array(
            'title' => 'Header and Footer Section',
            'description' => 'Header and Footer Section details',
        )
    );

    /* 
    Footer Heading 0 = 0
    Footer Heading 1 = 1
    Footer Heading 2 = 2
    Address = 3
    Phone = 4
    Mail = 5
    Facebook = 6
    X = 7
    LinkedIn = 8
    Insta = 9
    Copyright = 10
    Footer Description = 11
    */

    for ($i = 0; $i <= 11; $i++) {
        $labels = array(
            0 => 'Footer Heading 0',
            1 => 'Footer Heading 1',
            2 => 'Footer Heading 2',
            3 => 'Address',
            4 => 'Phone',
            5 => 'Mail',
            6 => 'Facebook',
            7 => 'X',
            8 => 'LinkedIn',
            9 => 'Insta',
            10 => 'Copyright',
            11 => 'Footer Description'
        );

        $label = $labels[$i];

        $wp_customize->add_setting(
            "setting_site_details$i",
            array(
                'type' => 'theme_mod',
                'default' => '',
                'sanitize_callback' => 'sanitize_text_field'
            )
        );

        $wp_customize->add_control(
            "setting_site_details$i",
            array(
                'label' => $label,
                'description' => "Please enter $label details.",
                'section' => 'section_header_and_footer',
                'type' => ($i !== 11) ? 'text' : 'textarea'

            )
        );
    }
}

add_action('customize_register', 'kaha6_customizer');
