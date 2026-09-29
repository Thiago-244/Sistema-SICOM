# SICOM - Sistema de Gestión y Monitoreo de Productos Comunicacionales

> **Entidad:** Dirección Regional de Educación Piura (DREP)  
> **Área:** Relaciones Públicas e Imagen Institucional  
> **Repositorio Remoto:** [https://github.com/Thiago-244/Sistema-SICOM](https://github.com/Thiago-244/Sistema-SICOM)

---

## 📌 Visión General del Sistema

**SICOM** es una plataforma web modular diseñada para la gestión, trazabilidad, monitoreo y auditoría del proceso de producción comunicacional de la DREP. Reemplaza flujos informales y dispersos por un canal único digital con reglas de negocio claras, asignación explicable, control de versiones, evidencias y paneles de control ejecutivos.

---

## 🎯 Alcance del Módulo Principal (Producción Comunicacional)

- **Gestión de Comunicadores:** Ficha profesional, especialidades (fotografía, redacción, edición de video, diseño gráfico, prensa) y disponibilidad.
- **Gestión de Solicitudes:** Registro formal con SLA, prioridad (Alta, Media, Baja), área solicitante y anexos.
- **Motor de Asignación Explicable:** Algoritmo ponderado de coincidencia candidato-solicitud (Especialidad + Disponibilidad + Carga Laboral + Vencimiento).
- **Ciclo de Vida y Versionado:** Estados de solicitud y producto desarticulados (`Borrador` → `En Revisión` → `Observado` → `Aprobado` → `Publicado`).
- **Repositorio y Evidencias:** Archivos versionados con control de integridad hash (SHA-256) y tipos MIME autorizados.
- **Auditoría e Indicadores:** Bitácora inmutable de eventos críticos y tableros KPI para la Jefatura.

---

## 🏗️ Arquitectura del Sistema

```
[ Frontend: HTML5 / Modern CSS / JS ES6 ]
                     │
                     ▼
[ Backend API / Controller Layer (PHP 8.3 / Clean MVC) ]
                     │
       ┌─────────────┼─────────────┐
       ▼             ▼             ▼
[ Service Layer ] [ Auth/RBAC ] [ Upload Engine ]
       │             │             │
       └─────────────┼─────────────┘
                     ▼
[ Database Layer: MySQL / MariaDB (InnoDB, Foreign Keys, Audit Triggers) ]
```

---

## 🗄️ Estructura del Proyecto

```
Sistema SICOM/
├── docs/                                 # Documentación técnica y funcional
├── database/                             # Migraciones SQL y Seeders
│   ├── schema.sql                        # Estructura DDL relacional
│   └── seeders.sql                       # Datos iniciales (roles, permisos, catálogos)
├── config/                               # Configuración de base de datos y parámetros
├── src/                                  # Código fuente Backend (Controladores, Servicios, Modelos)
├── public/                               # Punto de entrada web y recursos estáticos (CSS, JS)
├── storage/                              # Almacenamiento seguro de archivos y evidencias
├── .gitignore
└── README.md
```

---

## 🚀 Guía de Instalación Rápida (Entorno Local XAMPP)

1. **Clonar el repositorio:**
   ```bash
   git clone https://github.com/Thiago-244/Sistema-SICOM.git
   ```
2. **Importar la Base de Datos en MySQL/MariaDB:**
   - Crear la base de datos `sicom_db`.
   - Ejecutar `database/schema.sql` y luego `database/seeders.sql`.
3. **Configurar Credenciales:**
   - Copiar `config/database.example.php` a `config/database.php` y ajustar usuario y contraseña.
4. **Ejecutar el Servidor Local:**
   - Iniciar Apache y MySQL en XAMPP.
   - Navegar a `http://localhost/Sistema%20SICOM/public/`.

---

## 🔒 Seguridad y Privacidad

- **Autenticación & RBAC:** Control de acceso mediante roles y permisos específicos por caso de uso.
- **Validación Estricta:** Manejo seguro de entradas y uploads con hash de archivo SHA-256.
- **Auditoría Inmutable:** Registro automático de usuario, IP, marca de tiempo y cambios de estado en `auditoria_logs`.
