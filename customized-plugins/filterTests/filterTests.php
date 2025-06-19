<?php
/*
Plugin Name: PLUGIN DE FILTRADO DE GALERÍA
Description: PLUGIN PARA FILTRAR GALERÍA
Version:     1.0
Author:      Psycholoopgy
License:     GPL2
*/

add_shortcode('filterTests', 'filtertest');

function filtertest()
{
    ?>
    <script defer>


document.querySelector('.search-input').addEventListener('keyup', filter);
document.querySelector('.search-order').addEventListener('change', filter);

function filter(){
    const items = document.querySelectorAll('#containerTests .elementor-heading-title');
    console.log(Array.from(items));

    const filterText = document.querySelector('.search-input').value.toLowerCase();
    items.forEach(item => {
        if (
            item.textContent.toLowerCase().includes(filterText)
        ) {
            console.log('showing item');
            item.parentElement.parentElement.style.transition = '';
            item.parentElement.parentElement.style.opacity = '1';
            item.parentElement.parentElement.style.display = '';
        } else {
            console.log('not showing item');
            item.parentElement.parentElement.style.transition = 'opacity 0.15s';
            item.parentElement.parentElement.style.opacity = '0';
            setTimeout(() => {
                item.parentElement.parentElement.style.display = 'none';
            }, 150);
        }
    });

    const order = document.querySelector('.search-order').value;
    const container = items[0].parentElement.parentElement.parentElement;
    if (container) {
        const sortedItems = Array.from(items).sort((a, b) => {
            const textA = a.textContent.toLowerCase();
            const textB = b.textContent.toLowerCase();
            if (order == 'alph_asc') {
                return textA.localeCompare(textB);
            } else {
                return textB.localeCompare(textA);
            }
        });
        container.innerHTML = '';
        sortedItems.forEach(item => {
            const parent = item.parentElement.parentElement;
            container.appendChild(parent);
        });
    }
}
</script>
<?php
}