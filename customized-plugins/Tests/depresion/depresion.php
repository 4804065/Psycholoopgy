<?php
/*
Plugin Name: PLUGIN TEST DEPRESIÓN
Description: PLUGIN TEST DEPRESIÓN
Version: 1.0
Author: Psycholoopgy
*/

defined('ABSPATH') or die('Acceso denegado');

register_activation_hook(__FILE__, function () {
    global $wpdb;
    $tabla_resultados = $wpdb->wp_fp6zg . 'depresion_resultados';
    $tabla_respuestas = $wpdb->wp_fp6zg . 'depresion_respuestas';
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

add_shortcode('depresion', function () {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['respuestas']) && isset($_POST['nombre']) && isset($_POST['apellido'])) {
        $nombre = sanitize_text_field($_POST['nombre']);
        $apellido = sanitize_text_field($_POST['apellido']);
        $respuestas = array_map('intval', $_POST['respuestas']);
        $total = array_sum($respuestas);

        if ($total <= 20) {
            $diagnostico = "No hay depresión";
        } elseif ($total <= 35) {
            $diagnostico = "Depresión leve";
        } elseif ($total <= 60) {
            $diagnostico = "Depresión moderada";
        } else {
            $diagnostico = "Depresión alta";
        }

        global $wpdb;
        $tabla_resultados = $wpdb->wp_fp6zg . 'depresion_resultados';
        $tabla_respuestas = $wpdb->wp_fp6zg . 'depresion_respuestas';

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
        <h2>Test de Depresión</h2>
        <form method="POST">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" required style="width:100%;margin-bottom:10px;padding:8px;border:1px solid #ccc;border-radius:5px;">
            
            <label for="apellido">Apellido:</label>
            <input type="text" name="apellido" id="apellido" required style="width:100%;margin-bottom:20px;padding:8px;border:1px solid #ccc;border-radius:5px;">

            <?php
            $preguntas = [
                "1. Me siento triste o vacío/a la mayor parte del tiempo.",
                "2. He perdido el interés o el placer por actividades que antes difrutaba.",
                "3. Me cuesta encontrar motivación para realizar tareas diarias.",
                "4. Me siento cansado/a o sin energía casi todos los días.",
                "5. Tengo pensamientos negativos constantes sobre mí mismo/a.",
                "6. Me siento inútil o sin valor.",
                "7. Tengo dificultades para concentrarme o tomar decisiones.",
                "8. He notado cambios en mi apetito (comer mucho más o mucho menos).",
                "9. Tengo problemas para dormir o duermo demasiado.",
                "10. Siento que mi vida carece de sentido.",
                "11. Me aíslo de los demás o evito el contacto social.",
                "12. Me irrito o me enfado con facilidad sin una causa clara.",
                "13. Me siento culpable sin motivo aparente.",
                "14. Me resulta difícil disfrutar de momentos agradables, incluso con seres queridos.",
                "15. Siento que nada va a mejorar, por más que lo intente.",
                "16. Pienso que estoy fallando en aspectos importantes de mi vida.",
                "17. Me siento lento/a en mis pensamientos o movimientos.",
                "18. Tengo pensamientos sobre hacerme daño o desaparecer.",
                "19. Me cuesta expresar lo que siento a las personas cercanas.",
                "20. He considerado buscar ayuda profesional por cómo me siento."
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