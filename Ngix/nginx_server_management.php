#!/usr/bin/php
<?php

// Directorios por defecto para las configuraciones de Nginx
$sitesAvailableDir = "/etc/nginx/sites-available/";
$sitesEnabledDir = "/etc/nginx/sites-enabled/";

function displayMenu() {
    echo "\n********** Gestor de configuraciones de Nginx **********\n";
    echo "1. Activar configuración\n";
    echo "2. Desactivar configuración\n";
    echo "3. Ver configuraciones activas\n";
    echo "4. Eliminar configuración\n";
    echo "5. Salir\n";
    echo "********************************************************\n";
    echo "Selecciona una opción: ";
}

while (true) {
    // Obtiene todos los archivos de configuración disponibles
    $sitesAvailable = array_diff(scandir($sitesAvailableDir), array('..', '.'));
    // Obtiene todos los archivos de configuración activados
    $sitesEnabled = array_diff(scandir($sitesEnabledDir), array('..', '.'));

    displayMenu();
    $choice = trim(fgets(STDIN));
    echo "********************************************************\n";

    switch ($choice) {
        case "1":
            // Activar una configuración
            $sitesToActivate = array_diff($sitesAvailable, $sitesEnabled);
            if (empty($sitesToActivate)) {
                echo "\nTodas las configuraciones ya están activadas.\n";
                break;
            }

            echo "\nConfiguraciones disponibles para activar:\n";
            foreach ($sitesToActivate as $index => $site) {
                echo ($index + 1) . ". $site\n";
            }

            echo "\nSelecciona una configuración para activar: ";
            $siteChoice = trim(fgets(STDIN)) - 1;

            if (isset($sitesToActivate[$siteChoice])) {
                $siteToLink = $sitesAvailableDir . $sitesToActivate[$siteChoice];
                $linkLocation = $sitesEnabledDir . $sitesToActivate[$siteChoice];
                exec("ln -s $siteToLink $linkLocation");
                echo "\nConfiguración {$sitesToActivate[$siteChoice]} activada.\n";
            } else {
                echo "\nSelección inválida.\n";
            }
            break;

        case "2":
            // Desactivar una configuración
            if (empty($sitesEnabled)) {
                echo "\nNo hay configuraciones activadas.\n";
                break;
            }

            echo "\nConfiguraciones activadas:\n";
            foreach ($sitesEnabled as $index => $site) {
                echo ($index + 1) . ". $site\n";
            }

            echo "\nSelecciona una configuración para desactivar: ";
            $siteChoice = trim(fgets(STDIN)) - 1;

            if (isset($sitesEnabled[$siteChoice])) {
                $linkToRemove = $sitesEnabledDir . $sitesEnabled[$siteChoice];
                exec("rm $linkToRemove");
                echo "\nConfiguración {$sitesEnabled[$siteChoice]} desactivada.\n";
            } else {
                echo "\nSelección inválida.\n";
            }
            break;

        case "3":
            // Ver configuraciones activas
            if (empty($sitesEnabled)) {
                echo "\nNo hay configuraciones activadas.\n";
                break;
            }

            echo "\nConfiguraciones activadas:\n";
            foreach ($sitesEnabled as $index => $site) {
                echo ($index + 1) . ". $site\n";
            }
            break;

        case "4":
            // Eliminar configuración
            echo "\nConfiguraciones disponibles:\n";
            foreach ($sitesAvailable as $index => $site) {
                echo ($index + 1) . ". $site\n";
            }

            echo "\nSelecciona una configuración para eliminar: ";
            $siteChoice = trim(fgets(STDIN)) - 1;

            if (isset($sitesAvailable[$siteChoice])) {
                $siteToRemove = $sitesAvailableDir . $sitesAvailable[$siteChoice];
                exec("rm $siteToRemove");
                echo "\nConfiguración {$sitesAvailable[$siteChoice]} eliminada.\n";
            } else {
                echo "\nSelección inválida.\n";
            }
            break;

        case "5":
            // Salir
            echo "\n¡Hasta luego!\n";
            exit;

        default:
            echo "\nOpción inválida.\n";
            break;
    }
}

?>
