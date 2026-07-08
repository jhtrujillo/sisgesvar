<!DOCTYPE html>
<html>
<head>
    <title>Alerta Crítica de Inventario</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .header { background-color: #fef2f2; border-left: 4px solid #ef4444; padding: 15px; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #b91c1c; }
        .details { background-color: #f9fafb; padding: 15px; border-radius: 8px; }
        .details strong { color: #111; }
        .footer { margin-top: 20px; font-size: 0.85em; color: #666; border-top: 1px solid #eee; padding-top: 10px; }
        .stock-value { color: #dc2626; font-weight: bold; font-size: 1.1em; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>⚠️ Alerta: Nivel Crítico de Inventario</h2>
        </div>
        
        <p>Hola,</p>
        <p>Este es un aviso automático de SIVAR. Se acaba de registrar un movimiento que dejó un ítem del laboratorio por debajo de su cantidad crítica sugerida.</p>
        
        <div class="details">
            <p><strong>Item:</strong> {{ $item->descripcion_item }}</p>
            <p><strong>Área:</strong> {{ $item->area }}</p>
            <p><strong>Stock Actual:</strong> <span class="stock-value">{{ $item->cantidad_en_stock }} {{ $item->unidad }}</span></p>
            <p><strong>Umbral Crítico:</strong> {{ $item->cantidad_critica }} {{ $item->unidad }}</p>
            <p><strong>Último Usuario en retirar:</strong> {{ $user_name }}</p>
            <p><strong>Fecha:</strong> {{ date('Y-m-d H:i:s') }}</p>
        </div>
        
        <p>Por favor, considere gestionar una orden de compra o abastecimiento pronto para evitar desabastecimiento.</p>
        
        <div class="footer">
            <p>Este es un correo generado automáticamente por el Sistema SIVAR. No responda a este mensaje.</p>
        </div>
    </div>
</body>
</html>
