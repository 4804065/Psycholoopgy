<?php
/*
Plugin Name: PLUGIN TEST CRECIMIENTO PERSONAL
Description: PLUGIN TEST CRECIMIENTO PERSONAL
Version: 1.0
Author: Psycholoopgy
*/

defined('ABSPATH') or die('Acceso denegado');

register_activation_hook(__FILE__, function () {
    global $wpdb;
    $tabla_resultados = $wpdb->prefix . 'crecimiento_resultados';
    $tabla_respuestas = $wpdb->prefix . 'crecimiento_respuestas';
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

add_shortcode('crecimiento', function () {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['respuestas']) && isset($_POST['nombre']) && isset($_POST['apellido'])) {
        $nombre = sanitize_text_field($_POST['nombre']);
        $apellido = sanitize_text_field($_POST['apellido']);
        $respuestas = array_map('intval', $_POST['respuestas']);
        $total = array_sum($respuestas);

        if ($total <= 35) {
            $diagnostico = "Crecimiento leve";
        } elseif ($total <= 60) {
            $diagnostico = "Crecimiento moderado";
        } else {
            $diagnostico = "Crecimiento alto";
        }

        global $wpdb;
        $tabla_resultados = $wpdb->prefix . 'crecimiento_resultados';
        $tabla_respuestas = $wpdb->prefix . 'crecimiento_respuestas';

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
        <h2>Test de Crecimiento Personal</h2>
        <form method="POST">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" required style="width:100%;margin-bottom:10px;padding:8px;border:1px solid #ccc;border-radius:5px;">
            
            <label for="apellido">Apellido:</label>
            <input type="text" name="apellido" id="apellido" required style="width:100%;margin-bottom:20px;padding:8px;border:1px solid #ccc;border-radius:5px;">

            <?php
            $preguntas = [
                "1.Estoy comprometido/a con mejorar aspectos de mi vida personal y emocional.*",
                "2. Me esfuerzo por conocerme mejor a mí mismo/a.*",
                "3. Reconzoco mis errores y los utilizo como oportunidades de aprendizaje.*",
                "4. Me pongo metas personales claras y trabajo activamente para alcanzarlas.*",
                "5. Dedico tiempo regularmente a reflexionar sobre mi bienestar.*",
                "6. Busco salir de mi zona  de confort para crecer.*",
                "7. Trato de mantener una actitud positiva ante los retos.*",
                "8. Estoy abierto/a a recibir retroalimentación constructiva.*",
                "9. Me permito cambiar de opinión cuando descubro nuevas perspectivas.*",
                "10. Identifico mis fortalezas y las utilizo para avanzar.*",
                "11. Aprendo de las dificultades sin quedarme estancado/a en ellas.*",
                "12. Me siento en proceso de evolución constante.*",
                "13. Cuido mi salud mental, emocional y física como parte de mi desarrollo.*",
                "14. Acepto las emociones difíciles como parte del crecimiento.*",
                "15. Tengo relaciones que nutren mi desarrollo personal.*",
                "16. Me responsabilizo de mis decisiones y sus consecuencias.*",
                "17. Busco recursos (libros, cursos, terapia, etc.) para seguir creciendo.*",
                "18. Celebro mis logros personales, por pequeños que sean.*",
                "19. Trato de vivir de forma coherente con mis valores.*",
                "20. Siento que avanzo hacia una mejor versión de mí mismo/a.*"
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