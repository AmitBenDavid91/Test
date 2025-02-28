<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// פונקציה לטעינת MathJax באתר
function algebra_tutor_enqueue_scripts() {
    wp_enqueue_script('mathjax', 'https://polyfill.io/v3/polyfill.min.js?features=es6', array(), null, true);
    wp_enqueue_script('mathjax-config', 'https://cdnjs.cloudflare.com/ajax/libs/mathjax/3.2.0/es5/tex-mml-chtml.js', array('mathjax'), null, true);
}
add_action('wp_enqueue_scripts', 'algebra_tutor_enqueue_scripts');

// פונקציה להוספת כפתור "תרגול" לתפריט הראשי
function algebra_tutor_add_menu_item($items, $args) {
    if ($args->theme_location == 'primary') {
        $items .= '<li><a href="' . site_url('/algebra-practice') . '">תרגול</a></li>';
    }
    return $items;
}
add_filter('wp_nav_menu_items', 'algebra_tutor_add_menu_item', 10, 2);


function algebra_tutor_menu() {
    add_menu_page('ניהול תרגילי אלגברה', 'Algebra Tutor', 'manage_options', 'algebra-tutor', 'algebra_tutor_admin_page');
    add_submenu_page('algebra-tutor', 'תוצאות משתמשים', 'תוצאות משתמשים', 'manage_options', 'algebra-results', 'algebra_results_page');
    add_submenu_page('algebra-tutor', 'מאגר השאלות', 'מאגר השאלות', 'manage_options', 'algebra-question-bank', 'algebra_question_bank_page');
    add_submenu_page('algebra-tutor', 'סטטיסטיקות וניתוח', 'סטטיסטיקות', 'manage_options', 'algebra-statistics', 'algebra_statistics_page');
}
add_action('admin_menu', 'algebra_tutor_menu');


