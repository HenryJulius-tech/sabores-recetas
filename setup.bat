@echo off
title Instalacion - Sabores & Recetas
cd /d "%~dp0"

echo ============================================
echo   Sabores ^& Recetas - Plataforma de Cursos
echo   Instalacion Rapida para Windows
echo ============================================
echo.

REM Check PHP
where php >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [!] PHP no encontrado en PATH
    echo     Asegurate de tener PHP instalado y en tu PATH
    echo.
) else (
    echo [OK] PHP encontrado
)

REM Check MySQL
where mysql >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [!] MySQL no encontrado en PATH
    echo     Asegurate de tener MySQL instalado y en tu PATH
    echo.
) else (
    echo [OK] MySQL encontrado
)

REM Create .env
if not exist "config\.env" (
    if exist "config\.env.example" (
        echo [!] Creando config\.env desde ejemplo...
        copy config\.env.example config\.env >nul
        if exist "config\.env" (
            echo [OK] Archivo .env creado
        ) else (
            echo [ERROR] No se pudo crear config\.env
        )
    ) else (
        echo [ERROR] No se encuentra config\.env.example
    )
) else (
    echo [OK] config\.env ya existe
)

REM Check uploads
if not exist "public\uploads\.gitkeep" (
    echo [!] Creando directorio public\uploads\...
    if not exist "public\uploads" mkdir public\uploads
    copy nul public\uploads\.gitkeep >nul
)

echo.
echo ============================================
echo   PASOS SIGUIENTES:
echo ============================================
echo.
echo  1. Revisa la configuracion en: config\.env
echo     (DB_HOST, DB_NAME, DB_USER, DB_PASS)
echo.
echo  2. Abre en tu navegador:
echo     http://localhost/sabores-recetas/install.php
echo.
echo  3. Accede con:
echo     Admin:   admin / admin123
echo     Cliente: cliente / cliente123
echo.
echo  4. Elimina install.php despues de instalar
echo.
pause
