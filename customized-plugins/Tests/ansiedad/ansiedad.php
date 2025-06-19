<?php
/*
Plugin Name: PLUGIN TEST ANSIEDAD
Description: Plugin de test de ansiedad con almacenamiento de resultados y respuestas en la base de datos.
Version: 1.0
Author: Psycholoopgy
*/

defined('ABSPATH') or die('Acceso denegado');

register_activation_hook(__FILE__, function () {
    global $wpdb;
    $tabla_resultados = $wpdb->wp_fp6zg . 'ansiedad_resultados';
    $tabla_respuestas = $wpdb->wp_fp6zg . 'ansiedad_respuestas';
    $charset_collate = $wpdb->get_charset_collate();

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    $sql1 = "CREATE TABLE $tabla_resultados (
        cliente_id mediumint(9) NOT NULL AUTO_INCREMENT,
        nombre varchar(255) NOT NULL,
        apellido varchar(255) NOT NULL,
        puntuacion int NOT NULL,
        diagnostico varchar(255) NOT NULL,
        fecha datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (cliente_id)
    ) $charset_collate;";

    $sql2 = "CREATE TABLE $tabla_respuestas (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        cliente_id mediumint(9) NOT NULL,
        pregunta_numero int NOT NULL,
        respuesta_valor int NOT NULL,
        FOREIGN KEY (cliente_id) REFERENCES $tabla_resultados(cliente_id) ON DELETE CASCADE,
        PRIMARY KEY (id)
    ) $charset_collate;";

    dbDelta($sql1);
    dbDelta($sql2);
});

add_shortcode('ansiedad', function () {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['respuestas']) && isset($_POST['nombre']) && isset($_POST['apellido'])) {
        $nombre = sanitize_text_field($_POST['nombre']);
        $apellido = sanitize_text_field($_POST['apellido']);
        $respuestas = array_map('intval', $_POST['respuestas']);
        $total = array_sum($respuestas);

        if ($total <= 20) {
            $diagnostico = "No hay ansiedad";
        } elseif ($total <= 35) {
            $diagnostico = "Ansiedad leve";
        } elseif ($total <= 60) {
            $diagnostico = "Ansiedad moderada";
        } else {
            $diagnostico = "Ansiedad alta";
        }

        global $wpdb;
        $tabla_resultados = $wpdb->wp_fp6zg . 'ansiedad_resultados';
        $tabla_respuestas = $wpdb->wp_fp6zg . 'ansiedad_respuestas';

        $wpdb->insert($tabla_resultados, [
            'nombre' => $nombre,
            'apellido' => $apellido,
            'puntuacion' => $total,
            'diagnostico' => $diagnostico,
            'fecha' => current_time('mysql')
        ]);

        $cliente_id = $wpdb->insert_id;

        foreach ($respuestas as $num => $valor) {
            $wpdb->insert($tabla_respuestas, [
                'cliente_id' => $cliente_id,
                'pregunta_numero' => $num + 1,
                'respuesta_valor' => $valor
            ]);
        }

        $url_autotest = 'https://psycholoopgy.com/autotest/';
        
        return "
        <div class='psicotest-result'>
            <h2>Resultado</h2>
            <p><strong>$nombre $apellido</strong>, tu puntuación es: <strong>$total</strong></p>
            <p>Diagnóstico: $diagnostico</p>
            <a href='$url_autotest' class='back-button'>Volver a los tests</a>
        </div>";
    }

    ob_start();
    ?>
    <div class="psicotest-container">
        <h2>Test de Ansiedad</h2>
        <form method="POST">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" required style="width:100%;margin-bottom:10px;padding:8px;border:1px solid #ccc;border-radius:5px;">
            
            <label for="apellido">Apellido:</label>
            <input type="text" name="apellido" id="apellido" required style="width:100%;margin-bottom:20px;padding:8px;border:1px solid #ccc;border-radius:5px;">

            <?php
            $preguntas = [
                "1. Me siento nervioso/a o inquieto/a sin razón clara.",
                "2. Me cuesta relajarme, incluso cuando no hay motivos evidentes de tensión.",
                "3. Tengo pensamientos repetitivos que no puedo controlar.",
                "4. Me resulta difícil concentrarme o mantener la atención.",
                "5. Me preocupo en exceso por cosas cotidianas.",
                "6. Tengo dificultades para dormir por sentirme agitado/a o ansioso/a.",
                "7. Experimento síntomas físicos como palpitaciones o sudoración sin causa médica.",
                "8. Evito ciertas situaciones por temor a sentir ansiedad.",
                "9. Me siento abrumado/a por las responsabilidades diarias.",
                "10. Suelo anticipar que ocurrirá algo malo, aunque no haya razones claras.",
                "11. Tengo molestias físicas frecuentes (como dolor de cabeza o tensión muscular) sin explicación aparente.",
                "12. Siento que necesito tener el control para no perder la calma.",
                "13. Me irrito fácilmente o cambio de humor de forma repentina.",
                "14. La ansiedad afecta mi vida laboral, académica o social.",
                "15. Dudo mucho al tomar decisiones por miedo a equivocarme.",
                "16. Me siento inseguro/a respecto a mis capacidades o decisiones.",
                "17. Me cuesta disfrutar de los momentos agradables por pensar en lo que podría salir mal.",
                "18. Siento opresión en el pecho o dificultad para respirar cuando estoy tenso/a.",
                "19. Tiendo a imaginar escenarios negativos con frecuencia.",
                "20. Necesito la aprobación de los demás para sentirme tranquilo/a."
            ];

            $opciones = [
                "Nada de acuerdo" => 1,
                "Poco de acuerdo" => 2,
                "Algo de acuerdo" => 3,
                "Muy de acuerdo" => 4
            ];

            foreach ($preguntas as $i => $pregunta) {
    echo "<div class='pregunta-bloque' data-pregunta='$i'>";
    echo "<p><strong>$pregunta</strong></p>";
    foreach ($opciones as $texto => $valor) {
        $id = "preg_{$i}_{$valor}";
        echo "<div><input type='radio' id='$id' name='respuestas[$i]' value='$valor'>";
        echo "<label for='$id'>$texto</label></div>";
    }
    echo "</div>";
}

            ?>
            <button type="submit">Ver resultado</button>
        </form>
         <style>
        .pregunta-bloque {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
        }

        .pregunta-bloque.incompleta {
            border: 2px solid red;
            background-color: #ffe5e5;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('.psicotest-container form');

            form.addEventListener('submit', function (e) {
                let preguntas = document.querySelectorAll('.pregunta-bloque');
                let valido = true;

                preguntas.forEach(bloque => {
                    const radios = bloque.querySelectorAll('input[type="radio"]');
                    const respondida = Array.from(radios).some(r => r.checked);
                    
                    if (!respondida) {
                        bloque.classList.add('incompleta');
                        valido = false;
                    } else {
                        bloque.classList.remove('incompleta');
                    }
                });

                if (!valido) {
                    e.preventDefault();
                    alert('Por favor responde todas las preguntas antes de enviar.');
                }
            });
        });
    </script>
    </div>
    <?php
    return ob_get_clean();
});