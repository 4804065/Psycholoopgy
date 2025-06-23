<?php
/*
Plugin Name: PLUGIN TEST MOVIL Y RRSS
Description: PLUGIN TEST MOVIL Y RRSS
Version: 1.0
Author: Psycholoopgy
*/

defined('ABSPATH') or die('Acceso denegado');

register_activation_hook(__FILE__, function () {
    global $wpdb;
    $tabla_resultados = $wpdb->prefix . 'movil_resultados';
    $tabla_respuestas = $wpdb->prefix . 'movil_respuestas';
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

add_shortcode('movil', function () {
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
        $tabla_resultados = $wpdb->prefix . 'movil_resultados';
        $tabla_respuestas = $wpdb->prefix . 'movil_respuestas';

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
        <h2>Test de Adicción al Móvil y las Redes Sociales</h2>
        <form method="POST">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" required style="width:100%;margin-bottom:10px;padding:8px;border:1px solid #ccc;border-radius:5px;">
            
            <label for="apellido">Apellido:</label>
            <input type="text" name="apellido" id="apellido" required style="width:100%;margin-bottom:20px;padding:8px;border:1px solid #ccc;border-radius:5px;">

            <?php
            $preguntas = [
                "1. Reviso el móvil constantemente, incluso sin notificaciones.",
                "2. Me siento ansioso/a o incómodo/a cuando no tengo el móvil cerca.",
                "3. Paso más tiempo en redes sociales del que considero saludable.",
                "4. He intentado reducir el uso del móvil sin éxito.",
                "5. Me distraigo fácilmente por el móvil cuando estoy trabajando, estudiando o con otras personas.",
                "6. Consulto redes sociales apenas me despierto y antes de dormir.",
                "7. Me siento mal o inquieto/a si no puedo conectarme.",
                "8. Comparo constantemente mi vida con la de otros en redes sociales.",
                "9. El uso del móvil ha afectado negativamente mi rendimiento o relaciones.",
                "10. Me siento más conectado/a al mundo virtual que al entorno real.",
                "11. Publico contenido buscanso aprobación o validación frecuente.",
                "12. Me resulta difícil disfrutar actividades sin usar el móvil.",
                "13. He recibido comentarios negativos sobre cuánto uso el móvil o redes.",
                "14. Me distraigo con el móvil incluso en momentos importantes o íntimos.",
                "15. Me aíslo socialmente porque prefiero interactuar por redes.",
                "16. He puesto en riesgo mi seguridad por usar el móvil (por ejemplo, al cruzar o conducir).",
                "17. Me molesto cuando me interrumpen mientras uso el móvil.",
                "18. Siento que pierdo el tiempo pero no dejo de usar redes sociales.",
                "19. Recurro al móvil para evitar pensar o sentir emociones incómodas.",
                "20. Estoy considerando buscar ayuda profesional por mi uso del móvil o redes sociales."
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