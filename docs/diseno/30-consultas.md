# 30 Consultas SQL - Plantilla y ejemplos

Organiza las consultas en las siguientes categorías y agrega 30 consultas reales o adaptadas al modelo:

- Consultas simples (SELECT básicas)
- Consultas múltiples (JOINs)
- Subconsultas
- Consultas de agregación y ventanas
- Consultas complejas (CTE)
- Procedimientos almacenados (ejemplos)
- Triggers (ejemplos)

Ejemplo sencillo:

1) Listar materias activas por semestre:

```sql
SELECT m.id, m.nombre, s.nombre AS semestre
FROM materias m
JOIN semestres s ON m.semestre_id = s.id
WHERE m.estado = 'activo';
```

Crear un archivo `docs/diseno/consultas.sql` para volcar todas las consultas en orden numerado.
