<?php
session_start();
require_once '../includes/config.php';

$_POST = json_decode(file_get_contents("php://input"), true);

require_once '../modelo/dbconnect.php';

$db = new dbconnect();

switch (@$_GET['op']) {
  case 'getEstadisticas':
    {
      if (isset($_GET)) {
        // Total de productos
        $total_productos = $db->Consult($db->sql("SELECT COUNT(*) as total FROM producto WHERE status = 1"));
        
        // Total de clientes activos
        $total_clientes = $db->Consult($db->sql("SELECT COUNT(*) as total FROM clientes WHERE habilitado = 1"));
        
        // Total de entradas del mes actual
        $mes_actual = date('Y-m');
        $total_entradas = $db->Consult($db->sql("SELECT COUNT(*) as total FROM entrada WHERE DATE_FORMAT(created_at, '%Y-%m') = '$mes_actual'"));
        
        // Total de salidas del mes actual
        $total_salidas = $db->Consult($db->sql("SELECT COUNT(*) as total FROM salida WHERE DATE_FORMAT(created_at, '%Y-%m') = '$mes_actual'"));
        
        // Valor total del inventario
        $valor_inventario = $db->Consult($db->sql("SELECT SUM(cantidad) as total FROM inventario WHERE status = 1"));
        
        // Productos con stock bajo (menos de 10 unidades)
        $stock_bajo = $db->Consult($db->sql("SELECT COUNT(*) as total FROM inventario WHERE cantidad < 10 AND status = 1"));
        
        $response = array(
          'total_productos' => $total_productos->total,
          'total_clientes' => $total_clientes->total,
          'total_entradas' => $total_entradas->total,
          'total_salidas' => $total_salidas->total,
          'valor_inventario' => $valor_inventario->total,
          'stock_bajo' => $stock_bajo->total
        );
        
        setJson($response);
      }
    }
    break;
    
  case 'getEntradasSalidasMes':
    {
      if (isset($_GET)) {
        // Obtener entradas y salidas de los últimos 6 meses
        $data = array();
        
        for ($i = 5; $i >= 0; $i--) {
          $mes = date('Y-m', strtotime("-$i months"));
          $mes_nombre = date('M Y', strtotime("-$i months"));
          
          // Entradas del mes
          $entradas = $db->Consult($db->sql("SELECT COUNT(*) as total FROM entrada WHERE DATE_FORMAT(created_at, '%Y-%m') = '$mes'"));
          
          // Salidas del mes
          $salidas = $db->Consult($db->sql("SELECT COUNT(*) as total FROM salida WHERE DATE_FORMAT(created_at, '%Y-%m') = '$mes'"));
          
          $data[] = array(
            'mes' => $mes_nombre,
            'entradas' => $entradas->total,
            'salidas' => $salidas->total
          );
        }
        
        setJson($data);
      }
    }
    break;
    
  case 'getProductosMasVendidos':
    {
      if (isset($_GET)) {
        $productos = $db->AllConsult($db->sql("
          SELECT 
            p.nombre,
            p.ean,
            SUM(sd.cantidad) as total_vendido
          FROM 
            salida_detalle sd
            INNER JOIN producto p ON sd.id_producto = p.id_producto
            INNER JOIN salida s ON sd.id_salida = s.id_salida
          WHERE 
            DATE_FORMAT(s.created_at, '%Y-%m') = '" . date('Y-m') . "'
          GROUP BY 
            p.id_producto
          ORDER BY 
            total_vendido DESC
          LIMIT 10
        "));
        
        $data = array();
        foreach ($productos as $producto) {
          $data[] = array(
            'nombre' => $producto->nombre,
            'ean' => $producto->ean,
            'cantidad' => $producto->total_vendido
          );
        }
        
        setJson($data);
      }
    }
    break;
    
  case 'getInventarioBajo':
    {
      if (isset($_GET)) {
        $productos = $db->AllConsult($db->sql("
          SELECT 
            p.nombre,
            p.ean,
            i.cantidad,
            b.nombre as bodega
          FROM 
            inventario i
            INNER JOIN producto p ON i.id_producto = p.id_producto
            INNER JOIN bodega b ON p.id_bodega = b.id_bodega
          WHERE 
            i.cantidad < 50 AND i.status = 1
          ORDER BY 
            i.cantidad ASC
          LIMIT 15
        "));
        
        $data = array();
        foreach ($productos as $producto) {
          $data[] = array(
            'nombre' => $producto->nombre,
            'ean' => $producto->ean,
            'cantidad' => $producto->cantidad,
            'bodega' => $producto->bodega
          );
        }
        
        setJson($data);
      }
    }
    break;
    
  case 'getUltimasTransacciones':
    {
      if (isset($_GET)) {
        // Últimas 10 entradas
        $entradas = $db->AllConsult($db->sql("
          SELECT 
            'Entrada' as tipo,
            e.referencia,
            e.factura,
            c.empresa as cliente,
            e.created_at as fecha,
            'success' as color
          FROM 
            entrada e
            INNER JOIN clientes c ON e.id_cliente = c.id_cliente
          ORDER BY 
            e.created_at DESC
          LIMIT 5
        "));
        
        // Últimas 10 salidas
        $salidas = $db->AllConsult($db->sql("
          SELECT 
            'Salida' as tipo,
            s.referencia,
            s.factura,
            c.empresa as cliente,
            s.created_at as fecha,
            'danger' as color
          FROM 
            salida s
            INNER JOIN clientes c ON s.id_cliente = c.id_cliente
          ORDER BY 
            s.created_at DESC
          LIMIT 5
        "));
        
        // Combinar y ordenar por fecha
        $transacciones = array_merge($entradas, $salidas);
        usort($transacciones, function($a, $b) {
          return strtotime($b->fecha) - strtotime($a->fecha);
        });
        
        $data = array();
        foreach ($transacciones as $trans) {
          $data[] = array(
            'tipo' => $trans->tipo,
            'referencia' => $trans->referencia,
            'factura' => $trans->factura,
            'cliente' => $trans->cliente,
            'fecha' => $trans->fecha,
            'color' => $trans->color
          );
        }
        
        setJson($data);
      }
    }
    break;
    
  case 'getClientesActivos':
    {
      if (isset($_GET)) {
        $clientes = $db->AllConsult($db->sql("
          SELECT 
            c.empresa,
            COUNT(DISTINCT e.id_entrada) as entradas,
            COUNT(DISTINCT s.id_salida) as salidas,
            (COUNT(DISTINCT e.id_entrada) + COUNT(DISTINCT s.id_salida)) as total_movimientos
          FROM 
            clientes c
            LEFT JOIN entrada e ON c.id_cliente = e.id_cliente 
              AND DATE_FORMAT(e.created_at, '%Y-%m') = '" . date('Y-m') . "'
            LEFT JOIN salida s ON c.id_cliente = s.id_cliente 
              AND DATE_FORMAT(s.created_at, '%Y-%m') = '" . date('Y-m') . "'
          WHERE 
            c.habilitado = 1
          GROUP BY 
            c.id_cliente
          HAVING 
            total_movimientos > 0
          ORDER BY 
            total_movimientos DESC
          LIMIT 10
        "));
        
        $data = array();
        foreach ($clientes as $cliente) {
          $data[] = array(
            'cliente' => $cliente->empresa,
            'entradas' => $cliente->entradas,
            'salidas' => $cliente->salidas,
            'total' => $cliente->total_movimientos
          );
        }
        
        setJson($data);
      }
    }
    break;
    
  case 'getMovimientosPorCategoria':
    {
      if (isset($_GET)) {
        $categorias = $db->AllConsult($db->sql("
          SELECT 
            cat.nombre as categoria,
            SUM(sd.cantidad) as cantidad
          FROM 
            salida_detalle sd
            INNER JOIN producto p ON sd.id_producto = p.id_producto
            INNER JOIN categoria cat ON p.id_categoria = cat.id_categoria
            INNER JOIN salida s ON sd.id_salida = s.id_salida
          WHERE 
            DATE_FORMAT(s.created_at, '%Y-%m') = '" . date('Y-m') . "'
          GROUP BY 
            cat.id_categoria
          ORDER BY 
            cantidad DESC
        "));
        
        $data = array();
        foreach ($categorias as $cat) {
          $data[] = array(
            'categoria' => $cat->categoria,
            'cantidad' => $cat->cantidad
          );
        }
        
        setJson($data);
      }
    }
    break;
    
  default:
    die("NULL");
    break;
}
?>