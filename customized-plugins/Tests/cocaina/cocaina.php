<?php
/*
Plugin Name: PLUGIN TEST COCAÍNA
Description: PLUGIN TEST COCAÍNA
Version: 1.0
Author: Psycholoopgy
*/

defined('ABSPATH') or die('Acceso denegado');

register_activation_hook(__FILE__, function () {
    global $wpdb;
    $tabla_resultados = $wpdb->wp_fp6zg . 'cocaina_resultados';
    $tabla_respuestas = $wpdb->wp_fp6zg . 'cocaina_respuestas';
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

add_shortcode('cocaina', function () {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['respuestas']) && isset($_POST['nombre']) && isset($_POST['apellido'])) {
        $nombre = sanitize_text_field($_POST['nombre']);
        $apellido = sanitize_text_field($_POST['apellido']);
        $respuestas = array_map('intval', $_POST['respuestas']);
        $total = array_sum($respuestas);

        if ($total <= 20) {
            $diagnostico = "No hay adicción";
        } elseif ($total <= 35) {
            $diagnostico = "Adicción leve";
        } elseif ($total <= 60) {
            $diagnostico = "Adicción moderada";
        } else {
            $diagnostico = "Adicción alta";
        }

        global $wpdb;
        $tabla_resultados = $wpdb->wp_fp6zg . 'cocaina_resultados';
        $tabla_respuestas = $wpdb->wp_fp6zg . 'cocaina_respuestas';

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
        <h2>Test de Adicción a la Cocaína</h2>
        <form method="POST">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" required style="width:100%;margin-bottom:10px;padding:8px;border:1px solid #ccc;border-radius:5px;">
            
            <label for="apellido">Apellido:</label>
            <input type="text" name="apellido" id="apellido" required style="width:100%;margin-bottom:20px;padding:8px;border:1px solid #ccc;border-radius:5px;">

            <?php
            $preguntas = [
                "1. He consumido cocaína en momentos en los que no era apropiado o seguro hacerlo.",
                "2. Siento que necesito consumir para sentirme bien o funcionar.",
                "3. He intentado dejar de consumir cocaína, pero no lo he logrado.",
                "4. El consumo de cocaína ha afectado negativamente mi salud física o mental.",
                "5. He tenido problemas con familiares o amigos por mi consumo.",
                "6. He mentido o escondido el consumo de cocaína a personas cercanas.",
                "7. He gastado más dinero del que puedo permitirme en cocaína.",
                "8. He tenido problemas legales, laborales o académicos relacionados con el consumo.",
                "9. Después de consumir, siento culpa, vergüenza o vacío.",
                "10. Mi tolerancia ha aumentado: necesito más cantidad para sentir los mismos efectos.",
                "11. Me he expuesto a situaciones peligrosas bajo los efectos de la cocaína.",
                "12. He descuidado responsabilidades importantes por consumir.",
                "13. Pienso frecuentemente en volver a consumir, incluso cuando no lo hago.",
                "14. Me cuesta disfrutar de actividades si no estoy bajo los efectos de la cocaína.",
                "15. Me he prometido a mí mismo/a dejar de consumir y he vuelto a hacerlo.",
                "16. Mi estado de ánimo cambia drásticamente cuando no consumo.",
                "17. El consumo me ha alejado de personas o actividades que antes eran importantes para mí.",
                "18. He usado cocaína como forma de escapar de mis problemas emocionales.",
                "19. Me resulta difícil imaginar mi vida sin cocaína.",
                "20. Estoy considerando buscar ayuda profesional por mi consumo."
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