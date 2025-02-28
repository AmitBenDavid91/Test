<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly;
}

// טעינת TinyMCE של וורדפרס
function algebra_tutor_enqueue_admin_scripts($hook) {
    if (strpos($hook, 'algebra-tutor') === false) return;
    wp_enqueue_script('wp-tinymce');
    wp_enqueue_script('mathjax', 'https://polyfill.io/v3/polyfill.min.js?features=es6', array(), null, true);
    wp_enqueue_script('mathjax-config', 'https://cdnjs.cloudflare.com/ajax/libs/mathjax/3.2.0/es5/tex-mml-chtml.js', array('mathjax'), null, true);
}
add_action('admin_enqueue_scripts', 'algebra_tutor_enqueue_admin_scripts');

// דף הניהול הראשי להוספת שאלות עם קטגוריות ותצוגה מקדימה
function algebra_tutor_admin_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'algebra_exercises';
    $category_table = $wpdb->prefix . 'algebra_categories';
    $categories = $wpdb->get_results("SELECT * FROM $category_table ORDER BY name ASC");

    if (!empty($_POST['new_question']) && !empty($_POST['answer1']) && !empty($_POST['answer2']) && !empty($_POST['answer3']) && !empty($_POST['answer4']) && !empty($_POST['correct_answer']) && !empty($_POST['category']) && !empty($_POST['difficulty'])) {
        $choices = json_encode(array($_POST['answer1'], $_POST['answer2'], $_POST['answer3'], $_POST['answer4']));
        
        $wpdb->insert(
            $table_name,
            array(
                'question' => wp_kses_post($_POST['new_question']),
                'choices' => $choices,
                'correct_answer' => sanitize_text_field($_POST['correct_answer']),
                'category' => sanitize_text_field($_POST['category']),
                'difficulty' => sanitize_text_field($_POST['difficulty'])
            )
        );
        echo "<script>location.replace('?page=algebra-tutor&added=true');</script>";
    }

    if (isset($_GET['added']) && $_GET['added'] == 'true') {
        echo "<div class='updated'><p>השאלה נוספה בהצלחה!</p></div>";
    }

    echo "<h2>ניהול תרגילי אלגברה</h2>
    <form method='POST'>
        <p>שאלה:</p>
        <textarea name='new_question' id='new_question'></textarea>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                tinymce.init({
                    selector: '#new_question',
                    menubar: true,
                    plugins: 'lists link image charmap  fullscreen media paste     hr',
                    toolbar: 'undo redo | formatselect | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | forecolor backcolor | link image media table | emoticons hr preview',
                    image_uploadtab: true,
                    file_picker_callback: function(callback, value, meta) {
                        var input = document.createElement('input');
                        input.setAttribute('type', 'file');
                        input.setAttribute('accept', 'image/*');
                        input.onchange = function() {
                            var file = this.files[0];
                            var reader = new FileReader();
                            reader.onload = function(e) {
                                callback(e.target.result, { alt: file.name });
                            };
                            reader.readAsDataURL(file);
                        };
                        input.click();
                    },
                    setup: function(editor) {
                        editor.on('keyup', function () {
                            document.getElementById('preview').innerHTML = editor.getContent();
                            if (typeof MathJax !== 'undefined') {
                                MathJax.typesetPromise([document.getElementById('preview')]);
                            }
                        });
                    }
                });
            });
        </script>
        <p>תצוגה מקדימה:</p>
        <div id='preview' style='border:1px solid #ccc; padding:10px;'></div>
        <p>אפשרויות תשובה:</p>
        <p><input type='text' name='answer1' required></p>
        <p><input type='text' name='answer2' required></p>
        <p><input type='text' name='answer3' required></p>
        <p><input type='text' name='answer4' required></p>
        <p>תשובה נכונה: <input type='text' name='correct_answer' required></p>
        <p>קטגוריה: 
            <select name='category' required>
                <option value=''>בחר קטגוריה</option>";
    foreach ($categories as $category) {
        echo "<option value='{$category->name}'>{$category->name}</option>";
    }
    echo "</select></p>
        <p>רמת קושי: <select name='difficulty'>
            <option value='קל'>קל</option>
            <option value='בינוני'>בינוני</option>
            <option value='קשה'>קשה</option>
        </select></p>
        <p><input type='submit' value='הוסף תרגיל'></p>
    </form>";
}
